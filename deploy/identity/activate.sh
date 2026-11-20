#!/usr/bin/env bash
set -Eeuo pipefail
umask 077
[[ $EUID -eq 0 ]] || { echo 'Run this script with sudo.'; exit 1; }
source_dir=$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/../.." && pwd -P)
target=/var/www/simasta.bpskotabatu.com
backend=/home/bps/apps/presensi-backend
frontend=/var/www/presensi.simasta.bpskotabatu.com/public/presensi-assets/app.js
original=/home/bps/simasta-deploy-20261118/app
manifest="$source_dir/deploy/identity/main-files.txt"
php="$target/runtime/usr/bin/php8.4"
export PHP_INI_SCAN_DIR=
psql_root() { runuser -u postgres -- psql -X -v ON_ERROR_STOP=1 -d db_magang "$@"; }
main_cli() { (cd "$target" && runuser -u simasta -- "$php" -c /etc/simasta/php.ini artisan "$@"); }
[[ -f $target/.env && -f $backend/.env && -f /etc/simasta/admin-bootstrap.json && -x $php ]]
[[ $(psql_root -Atc "SELECT count(*) FROM information_schema.columns WHERE table_schema='presensi' AND table_name='users' AND column_name='simasta_user_id'") == 0 ]] || {
    echo 'Identity integration is already present; use verification instead of repeating the merge.'; exit 1;
}

# Refuse to overwrite code changed since the audited deployment.
while IFS= read -r file; do
    [[ -n $file ]] || continue
    [[ $file != /* && $file != *..* && -f $source_dir/$file ]]
    if [[ -f $original/$file ]]; then
        cmp "$original/$file" "$target/$file" || { echo "SIMASTA baseline changed: $file"; exit 1; }
    else
        [[ ! -e $target/$file ]] || { echo "Unexpected existing file: $file"; exit 1; }
    fi
done < <(tr -d '\r' < "$manifest")
(cd "$backend/src" && sha256sum -c <<'HASHES'
fa96c28cce4730ec41cb606db148ad52af8b96484077967f62a03a03b043eb12  index.js
99af82ce55843343b76faac3dee00844bc3f632064f81bbb98849c04bcaae999  config.js
9ebb4bc74abe611c362c37f7990b20a4a7941ae834293f2d7d299ff0a41c77b1  app.js
90697e9cc2c91137ac2542a5fa7061018e1e97f6847c094a89a42937b1807a85  auth.js
be220357943e4edb392c4facf5a187974edaa02ed7eff3a0df76488da1bde3f9  google.js
5063deb58c9cd2d0c45eab84738f9c473874af765e1504d1e80d6193bb3bf437  security.js
HASHES
)
echo "05b45ee3f0f0f01374ff6de7e73e886ab92d440d70b354e6ad8dba9eb6eec0ee  $frontend" | sha256sum -c
for file in "$source_dir"/integrations/presensi-backend/src/*.js; do node --check "$file"; done
"$php" -n -l "$source_dir/integrations/presensi-backend/src/verify-simasta-password.php"
python3 -c 'import ast,sys; ast.parse(open(sys.argv[1]).read())' "$source_dir/deploy/identity/configure-runtime.py"
systemctl is-active --quiet simasta-php presensi-backend
"$php" -c /etc/simasta/php.ini "$target/deploy/verify-production.php" < /etc/simasta/admin-bootstrap.json

backup=/var/backups/simasta/identity-$(date -u +%Y%m%dT%H%M%SZ)
install -d -m 0700 "$backup"
services_stopped=0
merge_attempted=0
merged=0
code_changed=0
on_exit() {
    result=$?
    if [[ $result -ne 0 && $services_stopped -eq 1 ]]; then
        if [[ $merge_attempted -eq 1 ]]; then
            state=$(psql_root -Atc "SELECT count(*) FROM information_schema.columns WHERE table_schema='presensi' AND table_name='users' AND column_name='simasta_user_id'" 2>/dev/null) || state=unknown
            [[ $state == 0 ]] || merged=1
        fi
        if [[ $merged -eq 0 ]]; then
            if [[ $code_changed -eq 1 ]]; then
                tar -xzf "$backup/code-before.tgz" -P
                while IFS= read -r file; do
                    case "$file" in "$target"/*|"$backend"/src/*) rm -f -- "$file" ;; *) exit 1 ;; esac
                done < "$backup/new-files.txt"
                main_cli optimize:clear >/dev/null || true
            fi
            systemctl start simasta-php simasta-web presensi-backend || true
            echo 'Activation stopped before identity merge; original application code and configuration restored.'
        else
            systemctl stop simasta-php presensi-backend || true
            echo 'Identity merge committed. Authentication services remain stopped for repair; do not restore separate credential code.'
        fi
        echo "Private diagnostics and backup: $backup"
    fi
}
trap on_exit EXIT
systemctl stop simasta-web simasta-php presensi-backend
services_stopped=1

# A single PostgreSQL dump captures both related schemas consistently.
runuser -u postgres -- pg_dump -Fc -n simasta -n presensi db_magang > "$backup/database-before.dump"
pg_restore --list "$backup/database-before.dump" > "$backup/database-contents.txt"
psql_root < "$source_dir/deploy/identity/audit.sql" > "$backup/audit-before.txt"
other_schemas() {
    runuser -u postgres -- pg_dump --schema-only --no-owner --no-privileges \
        -N simasta -N presensi db_magang | sed '/^\\restrict /d; /^\\unrestrict /d'
}
history() {
    runuser -u postgres -- pg_dump --data-only --no-owner --no-privileges -n presensi \
        -T presensi.users -T presensi.profiles -T presensi.sessions -T presensi.google_challenges db_magang \
        | sed '/^\\restrict /d; /^\\unrestrict /d'
}
other_schemas > "$backup/other-schemas-before.sql"
history > "$backup/history-before.sql"
runuser -u postgres -- pg_dump --data-only --no-owner --no-privileges -t presensi.profiles db_magang > "$backup/profiles-before.sql"
: > "$backup/existing-files.txt"
: > "$backup/new-files.txt"
remember() {
    if [[ -e $1 ]]; then printf '%s\n' "$1" >> "$backup/existing-files.txt";
    else printf '%s\n' "$1" >> "$backup/new-files.txt"; fi
}
while IFS= read -r file; do [[ -z $file ]] || remember "$target/$file"; done < <(tr -d '\r' < "$manifest")
for file in "$source_dir"/integrations/presensi-backend/src/*; do remember "$backend/src/$(basename "$file")"; done
remember "$target/.env"
remember "$backend/.env"
remember "$frontend"
tar -czf "$backup/code-before.tgz" -P -T "$backup/existing-files.txt"

code_changed=1
while IFS= read -r file; do
    [[ -n $file ]] || continue
    install -d -m 0750 -o root -g simasta "$(dirname "$target/$file")"
    install -m 0640 -o root -g simasta "$source_dir/$file" "$target/$file"
done < <(tr -d '\r' < "$manifest")
for file in "$source_dir"/integrations/presensi-backend/src/*; do
    install -m 0644 -o bps -g bps "$file" "$backend/src/$(basename "$file")"
done
install -m 0644 -o bps -g bps "$source_dir/integrations/presensi-website/public/presensi-assets/app.js" "$frontend"
python3 "$source_dir/deploy/identity/configure-runtime.py"
psql_root -v presensi_role=presensi_identity_app < "$source_dir/deploy/identity/runtime-grants.sql" > "$backup/runtime-grants.log"
main_cli optimize:clear
main_cli simasta:check-database
main_cli migrate --force
merge_attempted=1
psql_root -v presensi_role=presensi_identity_app < "$source_dir/deploy/identity/merge.sql" > "$backup/merge.log" 2>&1
merged=1
history > "$backup/history-after.sql"
runuser -u postgres -- pg_dump --data-only --no-owner --no-privileges -t presensi.profiles db_magang > "$backup/profiles-after.sql"
python3 "$source_dir/deploy/identity/compare-dumps.py" "$backup/history-before.sql" "$backup/history-after.sql" \
    --profiles-before "$backup/profiles-before.sql" --profiles-after "$backup/profiles-after.sql"
other_schemas > "$backup/other-schemas-after.sql"
cmp "$backup/other-schemas-before.sql" "$backup/other-schemas-after.sql"
psql_root < "$source_dir/deploy/identity/verify.sql" | tee "$backup/verification.txt"
main_cli optimize
systemctl start simasta-php simasta-web presensi-backend
for attempt in {1..30}; do
    if curl --fail --silent --max-time 2 http://127.0.0.1:5010/ready -o /dev/null \
        && curl --fail --silent --max-time 2 http://127.0.0.1:5013/login -o /dev/null; then break; fi
    sleep 1
done
"$php" -c /etc/simasta/php.ini "$target/deploy/verify-production.php" < /etc/simasta/admin-bootstrap.json
node "$source_dir/deploy/identity/verify-live.mjs" < /etc/simasta/admin-bootstrap.json
curl --fail --silent --show-error --max-time 10 http://127.0.0.1:5014/login -o /dev/null
for service in simasta-php simasta-web presensi-backend presensi-web; do systemctl is-active --quiet "$service"; done
printf '%s\n' "$backup" > /etc/simasta/identity-backup-path
chmod 0600 /etc/simasta/identity-backup-path
services_stopped=0
echo 'PASS: central accounts active; participant presensi access follows accepted internship status.'
echo 'Attendance history and unrelated schema definitions are unchanged.'
echo 'SIMASTA: 5013. Presensi: 5014. Existing sessions require a fresh login.'
echo "Private backup: $backup"
