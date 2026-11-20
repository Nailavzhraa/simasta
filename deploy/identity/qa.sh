#!/usr/bin/env bash
set -Eeuo pipefail
stage=$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/../.." && pwd -P)
source_dir="$stage/simasta"
backend="$stage/backend"
runtime=/home/bps/simasta-deploy-20261118/runtime
test_dir=$(mktemp -d "$stage/postgresql-qa.XXXXXX")
chmod 0700 "$test_dir"
pg_bin=/usr/lib/postgresql/16/bin
"$pg_bin/initdb" -D "$test_dir/data" --auth=trust --username=identity_test_admin > "$test_dir/initdb.log"
"$pg_bin/pg_ctl" -D "$test_dir/data" -l "$test_dir/postgres.log" -o "-k '$test_dir' -h '' -p 15436" start
trap '"$pg_bin/pg_ctl" -D "$test_dir/data" -m fast stop >/dev/null' EXIT
export PGHOST="$test_dir" PGPORT=15436 PGDATABASE=db_magang PGPASSWORD=test
"$pg_bin/createdb" -U identity_test_admin db_magang
psql -U identity_test_admin -v ON_ERROR_STOP=1 <<'SQL'
CREATE ROLE simasta_app LOGIN NOSUPERUSER NOCREATEDB NOCREATEROLE;
CREATE ROLE presensi_identity_app LOGIN NOSUPERUSER NOCREATEDB NOCREATEROLE;
CREATE ROLE magang NOLOGIN;
CREATE ROLE legacy_presensi_owner LOGIN NOSUPERUSER NOCREATEDB NOCREATEROLE;
CREATE SCHEMA simasta AUTHORIZATION simasta_app;
GRANT CREATE ON DATABASE db_magang TO legacy_presensi_owner;
SQL
export PHP_INI_SCAN_DIR= PHPRC="$runtime/php.ini"
export APP_KEY="base64:$(openssl rand -base64 32)"
export DB_CONNECTION=pgsql DB_HOST="$test_dir" DB_PORT=15436 DB_DATABASE=db_magang DB_SCHEMA=simasta
export DB_USERNAME=simasta_app DB_PASSWORD=test DB_URL= APP_ENV=testing
php="$runtime/usr/bin/php8.4"
cd "$source_dir"
mkdir -p bootstrap/cache storage/framework/{cache/data,sessions,views} storage/logs storage/app/{public,private}
"$php" vendor/bin/phpunit
export PGUSER=legacy_presensi_owner
cd "$backend"
node --input-type=module <<'JS'
import pg from 'pg';
import {migrate} from './src/migrate.js';
import {hashPassword} from './src/security.js';
import {phpPassword} from './src/simasta-password.js';
const db=new pg.Pool();
await migrate(db);
for(const [email,name,role,password] of [
  ['legacy@example.com','Legacy Participant','peserta',await hashPassword('Legacy-password-2026')],
  ['google@example.com','Google Participant','peserta','!google-only'],
  ['old-admin@example.com','Old Admin','admin',await hashPassword('Old-admin-password-2026')]]){
  const u=(await db.query('INSERT INTO presensi.users(email,password_hash) VALUES($1,$2) RETURNING id',[email,password])).rows[0];
  await db.query('INSERT INTO presensi.profiles(id,nama_lengkap,role) VALUES($1,$2,$3)',[u.id,name,role]);
  if(email.startsWith('google'))await db.query("INSERT INTO presensi.google_identities(subject,user_id)VALUES('google-fixture',$1)",[u.id]);
  if(email.startsWith('legacy'))await db.query("INSERT INTO presensi.absensi(user_id,tanggal)VALUES($1,'2026-11-17')",[u.id]);
}
const legacy=(await db.query("SELECT id FROM presensi.users WHERE email='legacy@example.com'")).rows[0];
await db.query("INSERT INTO presensi.izin(user_id,tanggal_mulai,tanggal_selesai,jenis,keterangan)VALUES($1,'2026-11-20','2026-11-20','izin','Temporary QA pending request')",[legacy.id]);
const main=new pg.Pool({user:'simasta_app'});
await main.query("INSERT INTO simasta.users(name,email,password,role)VALUES('Main Admin','main-admin@example.com',$1,'admin')",[await phpPassword({mode:'hash',password:'Main-admin-password-2026'})]);
await main.end(); await db.end();
JS
history_snapshot() {
    pg_dump -U identity_test_admin --data-only --no-owner --no-privileges -n presensi \
        -T presensi.users -T presensi.profiles -T presensi.sessions -T presensi.google_challenges db_magang
}
history_snapshot > "$test_dir/history-before.sql"
pg_dump -U identity_test_admin -Fc -n presensi db_magang > "$test_dir/presensi-before.dump"
pg_restore --data-only --schema=presensi --table=profiles --file=- "$test_dir/presensi-before.dump" > "$test_dir/profiles-before.sql"
psql -U identity_test_admin -v presensi_role=presensi_identity_app -v ON_ERROR_STOP=1 < "$stage/deploy/identity/merge.sql"
history_snapshot > "$test_dir/history-after.sql"
pg_dump -U identity_test_admin --data-only -t presensi.profiles db_magang > "$test_dir/profiles-after.sql"
python3 "$source_dir/deploy/identity/compare-dumps.py" "$test_dir/history-before.sql" "$test_dir/history-after.sql" \
    --profiles-before "$test_dir/profiles-before.sql" --profiles-after "$test_dir/profiles-after.sql"
psql -U identity_test_admin -v presensi_role=presensi_identity_app -v ON_ERROR_STOP=1 < "$stage/deploy/identity/runtime-grants.sql"
psql -U identity_test_admin -v ON_ERROR_STOP=1 < "$stage/deploy/identity/verify.sql"
export PGUSER=presensi_identity_app
# Approval fixtures belong only to this temporary database; production migration
# never creates or accepts an internship application on a student's behalf.
psql -U simasta_app -v ON_ERROR_STOP=1 <<'SQL'
INSERT INTO simasta.pengajuans(user_id,status)
SELECT id,'diterima' FROM simasta.users WHERE email IN ('legacy@example.com','google@example.com');
SQL
export IDENTITY_TEST_CLUSTER=1 IDENTITY_SIMASTA_SOURCE="$source_dir" IDENTITY_SIMASTA_PHP="$php"
npm test
HOST=127.0.0.1 PORT=15437 SIMASTA_IDENTITY_ENABLED=true node src/index.js > "$test_dir/backend.log" 2>&1 &
backend_pid=$!
trap 'kill "$backend_pid" 2>/dev/null || true; "$pg_bin/pg_ctl" -D "$test_dir/data" -m fast stop >/dev/null' EXIT
for attempt in {1..20}; do
    if curl --fail --silent --max-time 2 http://127.0.0.1:15437/ready -o /dev/null; then break; fi
    sleep 1
done
IDENTITY_VERIFY_URL=http://127.0.0.1:15437 node "$stage/deploy/identity/verify-live.mjs" <<'JSON'
{"email":"main-admin@example.com","password":"Main-admin-password-2026"}
JSON
echo 'PASS: isolated PostgreSQL merge, shared login, password changes, Google identity mapping, account status, and attendance history.'
