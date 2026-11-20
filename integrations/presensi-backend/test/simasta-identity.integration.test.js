import assert from 'node:assert/strict';
import test from 'node:test';
import pg from 'pg';
import { spawn } from 'node:child_process';
import { createApp } from '../src/app.js';
import { createSimastaAuth } from '../src/simasta-auth.js';
import { createGoogleAuth } from '../src/google.js';
import { createRateLimiter, tokenHash } from '../src/security.js';
import { phpPassword } from '../src/simasta-password.js';

test('SIMASTA is the live identity source for presensi', { skip: !process.env.IDENTITY_TEST_CLUSTER }, async t => {
  assert.match(process.env.PGHOST, /postgresql-qa\./, 'Must use the isolated socket-only cluster');
  const database = new pg.Pool({ max: 3 });
  const main = new pg.Pool({ user: 'simasta_app', max: 2 });
  const auth = createSimastaAuth(database);
  let googlePayload;
  const audience = '123-test.apps.googleusercontent.com';
  const google = createGoogleAuth(database, { identity: auth, clientIds: [audience],
    client: { async verifyIdToken() { return { getPayload: () => googlePayload }; } } });
  const server = createApp({ database, auth, googleAuth: google, rateLimit: createRateLimiter({ max: 500 }) });
  await new Promise(resolve => server.listen(0, '127.0.0.1', resolve));
  t.after(async () => { await new Promise(resolve => server.close(resolve)); await database.end(); await main.end(); });
  const url = `http://127.0.0.1:${server.address().port}`;
  async function api(path, { method = 'GET', body, token } = {}) {
    const response = await fetch(url + path, { method,
      headers: { ...(body ? { 'Content-Type': 'application/json' } : {}), ...(token ? { Authorization: `Bearer ${token}` } : {}) },
      ...(body ? { body: JSON.stringify(body) } : {}) });
    return { status: response.status, body: await response.json() };
  }
  async function login(email, password) { return api('/api/auth/login', { method: 'POST', body: { email, password } }); }
  function mainLogin(email, password) {
    return new Promise((resolve, reject) => {
      const child = spawn(process.env.IDENTITY_SIMASTA_PHP, [process.env.IDENTITY_SIMASTA_SOURCE + '/tests/Support/identity-login.php'],
        { env: { ...process.env, APP_ENV: 'testing', DB_CONNECTION: 'pgsql', DB_DATABASE: 'db_magang', DB_SCHEMA: 'simasta',
          DB_HOST: process.env.PGHOST, DB_PORT: process.env.PGPORT, DB_USERNAME: 'simasta_app', DB_PASSWORD: 'test',
          SESSION_DRIVER: 'array', CACHE_STORE: 'array', DB_URL: '' }, stdio: ['pipe', 'pipe', 'pipe'] });
      let output = ''; let error = '';
      child.stdout.on('data', chunk => output += chunk);
      child.stderr.on('data', chunk => error += chunk);
      child.on('error', reject);
      child.on('close', code => { try { assert.equal(code, 0, error); resolve(JSON.parse(output)); } catch (e) { reject(e); } });
      child.stdin.end(JSON.stringify({ email, password }));
    });
  }
  let participant, admin;
  await t.test('legacy UUID and attendance history survive account matching', async () => {
    participant = (await database.query("SELECT id,simasta_user_id FROM presensi.users WHERE email='legacy@example.com'")).rows[0];
    assert.ok(participant.simasta_user_id);
    assert.equal((await database.query('SELECT count(*)::int AS n FROM presensi.absensi WHERE user_id=$1', [participant.id])).rows[0].n, 1);
    assert.equal((await database.query('SELECT count(*)::int AS n FROM presensi.users WHERE simasta_user_id IS NULL')).rows[0].n, 0);
    await assert.rejects(database.query('SELECT password FROM simasta.users'), e => e.code === '42501');
  });
  await t.test('SIMASTA bcrypt administrator can log into presensi; passwords never appear in responses', async () => {
    const result = await login('main-admin@example.com', 'Main-admin-password-2026');
    assert.equal(result.status, 200, JSON.stringify(result.body));
    assert.equal(result.body.user.account_source, 'simasta');
    assert.equal(result.body.user.role, 'admin');
    assert.equal(result.body.user.password, undefined);
    assert.equal(result.body.user.password_hash, undefined);
    admin = result.body;
    assert.equal((await api('/api/admin/users', { token: admin.access_token })).status, 200);
  });
  await t.test('legacy scrypt credentials work in both applications without truncation or copying', async () => {
    const before = await login('legacy@example.com', 'Legacy-password-2026');
    assert.equal(before.status, 200, JSON.stringify(before.body));
    assert.equal(before.body.user.id, participant.id);
    assert.equal((await mainLogin('legacy@example.com', 'Legacy-password-2026')).authenticated, true);
    const hash = (await main.query('SELECT password FROM simasta.users WHERE id=$1', [participant.simasta_user_id])).rows[0].password;
    assert.match(hash, /^scrypt\$/);
    assert.equal((await api('/api/me', { token: before.body.access_token })).status, 200);
    assert.equal((await login('legacy@example.com', 'Legacy-password-2026')).status, 200);
    assert.equal((await login('legacy@example.com', 'wrong')).status, 401);
  });
  await t.test('new SIMASTA accounts provision immediately but require the same OTP verification', async () => {
    const hash = await phpPassword({ mode: 'hash', password: 'New-account-password-2026' });
    await main.query("INSERT INTO simasta.users(name,email,password,role) VALUES ('New user','new@example.com',$1,'mahasiswa')", [hash]);
    assert.equal((await database.query("SELECT count(*)::int AS n FROM presensi.users WHERE email='new@example.com'")).rows[0].n, 1);
    assert.equal((await login('new@example.com', 'New-account-password-2026')).status, 403);
    await main.query("UPDATE simasta.users SET email_verified_at=now() WHERE email='new@example.com'");
    assert.equal((await login('new@example.com', 'New-account-password-2026')).status, 403);
    await main.query("INSERT INTO simasta.pengajuans(user_id,status) SELECT id,'diproses' FROM simasta.users WHERE email='new@example.com'");
    assert.equal((await login('new@example.com', 'New-account-password-2026')).status, 403);
    await main.query("UPDATE simasta.pengajuans SET status='diterima' WHERE user_id=(SELECT id FROM simasta.users WHERE email='new@example.com')");
    const accepted = await login('new@example.com', 'New-account-password-2026');
    assert.equal(accepted.status, 200);
    await main.query("UPDATE simasta.pengajuans SET status='ditolak' WHERE user_id=(SELECT id FROM simasta.users WHERE email='new@example.com')");
    assert.equal((await api('/api/me', { token: accepted.body.access_token })).status, 401);
    assert.equal((await login('new@example.com', 'New-account-password-2026')).status, 403);
    await main.query("UPDATE simasta.pengajuans SET status='diterima' WHERE user_id=(SELECT id FROM simasta.users WHERE email='new@example.com')");
    assert.equal((await api('/api/me', { token: accepted.body.access_token })).status, 401);
    const readmitted = await login('new@example.com', 'New-account-password-2026');
    assert.equal(readmitted.status, 200);
    // An older acceptance cannot unlock a newer application still under review.
    const latest = (await main.query("INSERT INTO simasta.pengajuans(user_id,status) SELECT id,'diproses' FROM simasta.users WHERE email='new@example.com' RETURNING id")).rows[0];
    assert.equal((await login('new@example.com', 'New-account-password-2026')).status, 403);
    assert.equal((await api('/api/me', { token: readmitted.body.access_token })).status, 401);
    await main.query("UPDATE simasta.pengajuans SET status='diterima' WHERE id=$1", [latest.id]);
    assert.equal((await login('new@example.com', 'New-account-password-2026')).status, 200);
  });
  await t.test('profile edits update the main SIMASTA identity without role escalation', async () => {
    const token = (await login('legacy@example.com', 'Legacy-password-2026')).body.access_token;
    assert.equal((await api('/api/me', { method: 'PATCH', token, body: { role: 'admin' } })).status, 400);
    const result = await api('/api/me', { method: 'PATCH', token, body: { nama_lengkap: 'Unified Name', nim: 'NIM-QA-1', asal_instansi: 'Universitas QA', no_hp: '081234567890' } });
    assert.equal(result.status, 200, JSON.stringify(result.body));
    assert.equal(result.body.user.nama_lengkap, 'Unified Name');
    assert.equal((await main.query('SELECT name FROM simasta.users WHERE id=$1', [participant.simasta_user_id])).rows[0].name, 'Unified Name');
    assert.equal((await main.query('SELECT nim FROM simasta.mahasiswas WHERE user_id=$1', [participant.simasta_user_id])).rows[0].nim, 'NIM-QA-1');
  });
  await t.test('password changed through presensi works in SIMASTA and revokes previous sessions', async () => {
    const token = (await login('legacy@example.com', 'Legacy-password-2026')).body.access_token;
    const result = await api('/api/auth/password', { method: 'POST', token,
      body: { current_password: 'Legacy-password-2026', new_password: 'Updated-password-2026' } });
    assert.equal(result.status, 200, JSON.stringify(result.body));
    assert.equal((await mainLogin('legacy@example.com', 'Updated-password-2026')).authenticated, true);
    assert.equal((await mainLogin('legacy@example.com', 'Legacy-password-2026')).authenticated, false);
    assert.equal((await api('/api/me', { token })).status, 401);
    assert.equal((await login('legacy@example.com', 'Updated-password-2026')).status, 200);
  });
  await t.test('SIMASTA password resets immediately affect presensi without copying credentials', async () => {
    const token = (await login('legacy@example.com', 'Updated-password-2026')).body.access_token;
    const hash = await phpPassword({ mode: 'hash', password: 'Reset-password-2026' });
    await main.query('UPDATE simasta.users SET password=$1 WHERE id=$2', [hash, participant.simasta_user_id]);
    assert.equal((await api('/api/me', { token })).status, 401);
    assert.equal((await login('legacy@example.com', 'Updated-password-2026')).status, 401);
    assert.equal((await login('legacy@example.com', 'Reset-password-2026')).status, 200);
    const localHash = (await database.query('SELECT password_hash FROM presensi.users WHERE id=$1', [participant.id])).rows[0].password_hash;
    assert.equal(localHash, '!simasta-managed');
  });
  await t.test('disabling an account blocks both applications and keeps attendance history', async () => {
    const token = (await login('legacy@example.com', 'Reset-password-2026')).body.access_token;
    await main.query('UPDATE simasta.users SET is_active=false WHERE id=$1', [participant.simasta_user_id]);
    assert.equal((await api('/api/me', { token })).status, 401);
    assert.equal((await login('legacy@example.com', 'Reset-password-2026')).status, 401);
    assert.equal((await mainLogin('legacy@example.com', 'Reset-password-2026')).authenticated, false);
    assert.equal((await database.query('SELECT count(*)::int AS n FROM presensi.absensi WHERE user_id=$1', [participant.id])).rows[0].n, 1);
    await main.query('UPDATE simasta.users SET is_active=true WHERE id=$1', [participant.simasta_user_id]);
  });
  await t.test('Google-only identities remain passwordless and require the linked Google subject', async () => {
    assert.equal((await login('google@example.com', 'anything')).status, 401);
    const challenge = await api('/api/auth/google/challenge', { method: 'POST', body: {} });
    googlePayload = { sub: 'google-fixture', email: 'google@example.com', email_verified: true, aud: audience,
      iss: 'https://accounts.google.com', exp: Date.now()/1000+300, iat: Date.now()/1000, nonce: challenge.body.nonce };
    const result = await api('/api/auth/google', { method: 'POST', body: { credential: 'x'.repeat(30), nonce: challenge.body.nonce } });
    assert.equal(result.status, 200, JSON.stringify(result.body));
    assert.equal(result.body.user.has_password, false);
    assert.equal(result.body.user.account_source, 'simasta');
    assert.equal((await api('/api/auth/google', { method: 'POST', body: { credential: 'x'.repeat(30), nonce: challenge.body.nonce } })).status, 401);
  });
  await t.test('registration in presensi cannot create an independent identity', async () => {
    const before = (await main.query('SELECT count(*)::int AS n FROM simasta.users')).rows[0].n;
    const result = await api('/api/auth/register', { method: 'POST', body: { email: 'separate@example.com', password: 'Separate-password-2026', nama_lengkap: 'Separate' } });
    assert.equal(result.status, 409);
    assert.equal(result.body.code, 'register_in_simasta');
    assert.equal((await main.query('SELECT count(*)::int AS n FROM simasta.users')).rows[0].n, before);
    await assert.rejects(main.query("INSERT INTO simasta.users(name,email,password) VALUES ('Duplicate','MAIN-ADMIN@EXAMPLE.COM','x')"), e => e.code === '23505');
  });
});
