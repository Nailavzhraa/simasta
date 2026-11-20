import { createHash } from 'node:crypto';
import { HttpError } from './errors.js';
import { publicUser, transaction, createSession } from './auth.js';
import { normalizeEmail, tokenHash, validatePassword } from './security.js';
import { phpPassword, verifyCentralPassword } from './simasta-password.js';

const columns = `u.id, u.email, u.active, p.*, a.id AS simasta_id,
  a.name AS canonical_name, a.email AS canonical_email, a.password AS canonical_password,
  a.role AS canonical_role, a.is_active AS identity_active, a.can_authenticate, a.google_id, a.magang_status`;
const from = `FROM presensi.users u JOIN presensi.profiles p ON p.id=u.id
  JOIN simasta.presensi_accounts a ON a.id=u.simasta_user_id`;
const configured = hash => typeof hash === 'string' && !hash.startsWith('!');
const version = row => createHash('sha256').update(row.canonical_password).digest('hex');
function identityUser(row) {
  return { ...publicUser({ ...row, email: row.canonical_email, nama_lengkap: row.canonical_name,
    role: row.canonical_role === 'admin' ? 'admin' : 'peserta', has_password: configured(row.canonical_password) }),
  simasta_user_id: String(row.simasta_id), account_source: 'simasta', magang_status: row.magang_status };
}
function onlyFields(input, fields) {
  if (Object.keys(input).some(key => !fields.includes(key))) throw new HttpError(400, 'Ada kolom yang tidak diizinkan.');
}
function permitted(row) { return row && row.active && row.identity_active && row.can_authenticate; }
async function session(client, row) {
  const result = await createSession(client, row);
  await client.query('UPDATE presensi.sessions SET identity_version=$1 WHERE token_hash=$2', [version(row), tokenHash(result.access_token)]);
  return { ...result, user: identityUser(row) };
}

export function createSimastaAuth(database) {
  let dummyHash;
  return {
    async register() {
      throw new HttpError(409, 'Pendaftaran akun dilakukan di https://simasta.bpskotabatu.com/register. Gunakan akun SIMASTA untuk masuk di sini.', 'register_in_simasta');
    },
    async login(input) {
      onlyFields(input, ['email', 'password']);
      const email = normalizeEmail(input.email);
      if (typeof input.password !== 'string' || Buffer.byteLength(input.password) > 256) throw new HttpError(400, 'Password tidak valid.');
      dummyHash ??= phpPassword({ mode: 'hash', password: 'dummy-password-for-timing-only' });
      const dummy = await dummyHash;
      return transaction(database, async client => {
        const row = (await client.query(`SELECT ${columns} ${from} WHERE a.email=$1 FOR UPDATE OF u`, [email])).rows[0];
        const matches = await verifyCentralPassword(input.password, row?.canonical_password ?? dummy);
        if (!row || !row.active || !row.identity_active || !matches) throw new HttpError(401, 'Email atau password salah.');
        if (!row.can_authenticate) throw new HttpError(403, 'Akses presensi terbuka setelah pengajuan magang diterima di SIMASTA dan verifikasi akun selesai.', 'magang_not_approved');
        return session(client, row);
      });
    },
    async authenticate(token) {
      const row = (await database.query(`SELECT ${columns}, s.identity_version ${from}
        JOIN presensi.sessions s ON s.user_id=u.id
        WHERE s.token_hash=$1 AND s.expires_at>now()`, [tokenHash(token)])).rows[0];
      if (!permitted(row) || row.identity_version !== version(row)) throw new HttpError(401, 'Sesi berakhir. Silakan login kembali dengan akun SIMASTA.');
      return identityUser(row);
    },
    async logout(token) {
      await database.query('DELETE FROM presensi.sessions WHERE token_hash=$1', [tokenHash(token)]);
      return { message: 'Logout berhasil.' };
    },
    async changePassword(user, token, input) {
      onlyFields(input, ['current_password', 'new_password']);
      validatePassword(input.new_password);
      if (Buffer.byteLength(input.new_password) > 72) throw new HttpError(400, 'Password maksimal 72 byte.');
      return transaction(database, async client => {
        const row = (await client.query(`SELECT ${columns}, s.identity_version,
          (s.created_at>now()-interval '10 minutes') AS recent ${from}
          JOIN presensi.sessions s ON s.user_id=u.id
          WHERE u.id=$1 AND s.token_hash=$2 AND s.expires_at>now()`, [user.id, tokenHash(token)])).rows[0];
        if (!permitted(row) || row.identity_version !== version(row)) throw new HttpError(401, 'Sesi berakhir. Silakan login kembali.');
        if (configured(row.canonical_password)) {
          if (!await verifyCentralPassword(input.current_password, row.canonical_password)) throw new HttpError(401, 'Password saat ini salah.');
        } else if (!row.recent) {
          throw new HttpError(401, 'Masuk kembali dengan Google sebelum membuat kata sandi.', 'google_recent_login_required');
        }
        const nextHash = await phpPassword({ mode: 'hash', password: input.new_password });
        const changed = (await client.query('SELECT simasta.presensi_change_password($1,$2,$3) AS changed',
          [user.id, row.canonical_password, nextHash])).rows[0].changed;
        if (!changed) throw new HttpError(409, 'Akun berubah bersamaan. Silakan login kembali.');
        return { message: 'Password SIMASTA dan presensi diperbarui. Silakan login kembali di kedua aplikasi.' };
      });
    },
    async updateProfile(user, input) {
      onlyFields(input, ['nama_lengkap', 'nim', 'asal_instansi', 'no_hp']);
      if (!Object.keys(input).length) throw new HttpError(400, 'Isi setidaknya satu kolom profil.');
      for (const [key, value] of Object.entries(input)) {
        if (key === 'nama_lengkap' && (typeof value !== 'string' || !value.trim() || value.trim().length > 150)) throw new HttpError(400, 'Nama maksimal 150 karakter.');
        if (value !== null && (typeof value !== 'string' || value.length > 200)) throw new HttpError(400, 'Isian profil maksimal 200 karakter.');
      }
      return transaction(database, async client => {
        await client.query('SELECT simasta.presensi_update_profile($1,$2::jsonb)', [user.id, JSON.stringify(input)]);
        const row = (await client.query(`SELECT ${columns} ${from} WHERE u.id=$1`, [user.id])).rows[0];
        if (!permitted(row)) throw new HttpError(401, 'Akun tidak aktif.');
        return { user: identityUser(row) };
      });
    },
    async listUsers() {
      const rows = (await database.query(`SELECT ${columns} ${from} ORDER BY u.created_at DESC,u.id LIMIT 100`)).rows;
      return { users: rows.map(identityUser) };
    },
    async loginWithGoogle(payload, nonce) {
      return transaction(database, async client => {
        const challenge = (await client.query(`DELETE FROM presensi.google_challenges
          WHERE nonce_hash=$1 AND expires_at>now() RETURNING nonce_hash`, [tokenHash(nonce)])).rows[0];
        if (!challenge) throw new HttpError(401, 'Sesi Google sudah digunakan atau berakhir.', 'google_challenge_expired');
        const row = (await client.query(`SELECT ${columns} ${from} WHERE a.google_id=$1 FOR UPDATE OF u`, [payload.sub])).rows[0];
        if (!row || row.canonical_email !== normalizeEmail(payload.email)) {
          throw new HttpError(403, 'Hubungkan akun Google melalui SIMASTA terlebih dahulu.', 'link_google_in_simasta');
        }
        if (!permitted(row)) throw new HttpError(403, 'Akses presensi memerlukan akun aktif dan pengajuan magang diterima di SIMASTA.', 'magang_not_approved');
        return session(client, row);
      });
    },
  };
}
