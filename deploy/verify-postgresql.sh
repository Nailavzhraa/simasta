#!/usr/bin/env bash
# Runs against a new socket-only PostgreSQL cluster, never the office database.
set -Eeuo pipefail
source_dir=$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd -P)
stage=$(dirname -- "$source_dir")
test_dir=$(mktemp -d "$stage/postgresql-qa.XXXXXX")
chmod 0700 "$test_dir"
pg_bin=/usr/lib/postgresql/16/bin
"$pg_bin/initdb" -D "$test_dir/data" --auth=trust --username=simasta_test > "$test_dir/initdb.log"
cleanup() { "$pg_bin/pg_ctl" -D "$test_dir/data" -m fast stop >/dev/null; }
"$pg_bin/pg_ctl" -D "$test_dir/data" -l "$test_dir/postgres.log" \
    -o "-k '$test_dir' -h '' -p 15435" start
trap cleanup EXIT
"$pg_bin/createdb" -h "$test_dir" -p 15435 -U simasta_test db_magang
psql -h "$test_dir" -p 15435 -U simasta_test -v ON_ERROR_STOP=1 db_magang <<'SQL'
CREATE ROLE simasta_app LOGIN NOSUPERUSER NOCREATEDB NOCREATEROLE NOINHERIT;
CREATE SCHEMA simasta AUTHORIZATION simasta_app;
REVOKE ALL ON SCHEMA simasta FROM PUBLIC;
CREATE SCHEMA presensi;
CREATE TABLE public.users (sentinel text);
INSERT INTO public.users VALUES ('public preserved');
CREATE TABLE presensi.users (sentinel text);
INSERT INTO presensi.users VALUES ('presensi preserved');
SQL
export PHP_INI_SCAN_DIR=
export PHPRC="$stage/runtime/php.ini"
export DB_CONNECTION=pgsql DB_HOST="$test_dir" DB_PORT=15435 DB_DATABASE=db_magang
export DB_USERNAME=simasta_app DB_PASSWORD= DB_SCHEMA=simasta DB_URL=
export APP_ENV=production APP_DEBUG=false
export APP_KEY="base64:$(openssl rand -base64 32)"
php="$stage/runtime/usr/bin/php8.4"
cd "$source_dir"
"$php" artisan simasta:check-database
"$php" artisan migrate --force
"$php" artisan migrate --force
"$php" artisan db:seed --class=DivisiSeeder --force
"$php" artisan optimize
"$php" artisan optimize:clear
if DB_SCHEMA=public "$php" artisan simasta:check-database; then
    echo 'Database guard accepted the wrong schema'; exit 1
fi
APP_ENV=testing "$php" vendor/bin/phpunit
psql -h "$test_dir" -p 15435 -U simasta_test -v ON_ERROR_STOP=1 db_magang <<'SQL'
DO $$
BEGIN
    IF (SELECT count(*) FROM simasta.migrations) <> 28 THEN
        RAISE EXCEPTION 'Not all 28 migrations were applied';
    END IF;
    IF (SELECT sentinel FROM public.users) <> 'public preserved'
        OR (SELECT sentinel FROM presensi.users) <> 'presensi preserved' THEN
        RAISE EXCEPTION 'Another schema was modified';
    END IF;
    IF EXISTS (
        SELECT 1 FROM pg_constraint fk
        JOIN pg_class source ON source.oid=fk.conrelid
        JOIN pg_namespace source_schema ON source_schema.oid=source.relnamespace
        JOIN pg_class target ON target.oid=fk.confrelid
        JOIN pg_namespace target_schema ON target_schema.oid=target.relnamespace
        WHERE fk.contype='f' AND source_schema.nspname='simasta'
          AND target_schema.nspname <> 'simasta'
    ) THEN
        RAISE EXCEPTION 'Foreign key points outside simasta';
    END IF;
END $$;
SELECT schemaname, count(*) AS tables FROM pg_tables
WHERE schemaname IN ('simasta','presensi','public') GROUP BY schemaname ORDER BY schemaname;
SQL
echo "PASS: PostgreSQL 16 migrations, repeat migration, production caches, and schema isolation. QA files: $test_dir"
