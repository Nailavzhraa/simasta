#!/usr/bin/env bash
# Resume the committed merge without rerunning migrations, importing accounts, or rotating credentials.
set -Eeuo pipefail
umask 077
[[ $EUID -eq 0 && $# -eq 1 ]] || { echo 'Usage: sudo bash finish-release.sh /var/backups/simasta/identity-<timestamp>'; exit 1; }
source_dir=$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/../.." && pwd -P)
stage=$(dirname "$source_dir")
backup=$(realpath -e -- "$1")
[[ $backup == /var/backups/simasta/identity-* && -d $backup ]]
[[ -f $backup/history-before.sql && -f $backup/history-after.sql && -f $backup/other-schemas-before.sql ]]
target=/var/www/simasta.bpskotabatu.com
backend=/home/bps/apps/presensi-backend
php="$target/runtime/usr/bin/php8.4"
export PHP_INI_SCAN_DIR=
psql_root() { runuser -u postgres -- psql -X -v ON_ERROR_STOP=1 -d db_magang "$@"; }
main_cli() { (cd "$target" && runuser -u simasta -- "$php" -c /etc/simasta/php.ini artisan "$@"); }
[[ $(psql_root -Atc "SELECT count(*) FROM information_schema.columns WHERE table_schema='presensi' AND table_name='users' AND column_name='simasta_user_id'") == 1 ]]

# Test the original before/after snapshots before touching service state.
pg_restore --data-only --schema=presensi --table=profiles --file=- "$backup/database-before.dump" > "$backup/profiles-before.sql"
runuser -u postgres -- pg_dump --data-only --no-owner --no-privileges -t presensi.profiles db_magang > "$backup/profiles-current.sql"
python3 "$source_dir/deploy/identity/compare-dumps.py" "$backup/history-before.sql" "$backup/history-after.sql" \
    --profiles-before "$backup/profiles-before.sql" --profiles-after "$backup/profiles-current.sql" \
    | tee "$backup/history-comparison.txt"
runuser -u postgres -- pg_dump --data-only --no-owner --no-privileges -n presensi \
    -T presensi.users -T presensi.profiles -T presensi.sessions -T presensi.google_challenges db_magang \
    | sed '/^\\restrict /d; /^\\unrestrict /d' > "$backup/history-current.sql"
python3 "$source_dir/deploy/identity/compare-dumps.py" "$backup/history-before.sql" "$backup/history-current.sql" \
    --profiles-before "$backup/profiles-before.sql" --profiles-after "$backup/profiles-current.sql"
runuser -u postgres -- pg_dump --schema-only --no-owner --no-privileges -N simasta -N presensi db_magang \
    | sed '/^\\restrict /d; /^\\unrestrict /d' > "$backup/other-schemas-after.sql"
cmp "$backup/other-schemas-before.sql" "$backup/other-schemas-after.sql"
psql_root < "$source_dir/deploy/identity/verify.sql" | tee "$backup/verification.txt"
while IFS= read -r file; do
    [[ -n $file ]] || continue
    cmp "$source_dir/$file" "$target/$file" || { echo "Installed source differs: $file"; exit 1; }
done < <(tr -d '\r' < "$source_dir/deploy/identity/main-files.txt")
for file in "$source_dir"/integrations/presensi-backend/src/*; do cmp "$file" "$backend/src/$(basename "$file")"; done
main_cli simasta:check-database
main_cli optimize
systemctl start simasta-php simasta-web presensi-backend
for attempt in {1..30}; do
    if curl --fail --silent --max-time 2 http://127.0.0.1:5010/ready -o /dev/null \
        && curl --fail --silent --max-time 2 http://127.0.0.1:5013/login -o /dev/null; then break; fi
    sleep 1
done
"$php" -c /etc/simasta/php.ini "$target/deploy/verify-production.php" < /etc/simasta/admin-bootstrap.json
node "$source_dir/deploy/identity/verify-live.mjs" < /etc/simasta/admin-bootstrap.json
for service in simasta-php simasta-web presensi-backend presensi-web; do systemctl is-active --quiet "$service"; done
printf '%s\n' "$backup" > /etc/simasta/identity-backup-path
chmod 0600 /etc/simasta/identity-backup-path
echo 'PASS identity integration active; attendance records and unrelated schema definitions verified.'
bash "$source_dir/deploy/google/activate.sh" "$stage/google-oauth.json"
echo 'PASS merged application, central identities, and Google authorization redirect active.'
