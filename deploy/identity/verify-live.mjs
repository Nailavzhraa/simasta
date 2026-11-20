// Root-only smoke test. Credentials arrive through stdin, never arguments/logs.
import assert from 'node:assert/strict';
let input = '';
for await (const chunk of process.stdin) {
  input += chunk;
  if (input.length > 8192) throw new Error('Oversized credential input');
}
const credentials = JSON.parse(input);
const base = process.env.IDENTITY_TEST_CLUSTER === '1' ? process.env.IDENTITY_VERIFY_URL : 'http://127.0.0.1:5010';
assert.equal(new URL(base).hostname, '127.0.0.1');
assert.equal(new URL(base).protocol, 'http:');
async function request(path, expected, body, token) {
  const response = await fetch(base + path, {
    method: body === undefined ? 'GET' : 'POST',
    headers: { ...(body === undefined ? {} : { 'Content-Type': 'application/json' }),
      ...(token ? { Authorization: `Bearer ${token}` } : {}) },
    ...(body === undefined ? {} : { body: JSON.stringify(body) }),
    signal: AbortSignal.timeout(15000),
  });
  assert.equal(response.status, expected, `Unexpected response at ${path}: ${response.status}`);
  return response.json();
}
await request('/ready', 200);
const login = await request('/api/auth/login', 200, { email: credentials.email, password: credentials.password });
try {
  assert.equal(login.user.account_source, 'simasta');
  assert.equal(login.user.role, 'admin');
  assert.equal(login.user.email, credentials.email);
  assert.ok(login.user.simasta_user_id);
  assert.equal(login.user.password, undefined);
  assert.equal(login.user.password_hash, undefined);
  const me = await request('/api/me', 200, undefined, login.access_token);
  assert.equal(me.user.simasta_user_id, login.user.simasta_user_id);
  await request('/api/admin/users', 200, undefined, login.access_token);
  const blocked = await request('/api/auth/register', 409, {});
  assert.equal(blocked.code, 'register_in_simasta');
} finally {
  await request('/api/auth/logout', 200, {}, login.access_token);
}
await request('/api/me', 401, undefined, login.access_token);
console.log('PASS shared SIMASTA administrator login, account source, admin API, logout, and central registration policy.');
