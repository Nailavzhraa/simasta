#!/usr/bin/env bash
# Finish an existing installation after a smoke-test rollback; preserve database and credentials.
set -Eeuo pipefail
umask 027
[[ $EUID -eq 0 ]] || { echo 'Run this activation script with sudo.'; exit 1; }
source_dir=$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd -P)
target=/var/www/simasta.bpskotabatu.com
origin=/etc/nginx/sites-available/simasta.bpskotabatu.com
backup=/var/backups/simasta/activate-$(date -u +%Y%m%dT%H%M%SZ)
[[ -f $target/.env && -f /etc/simasta/admin-bootstrap.json ]]
[[ -x $target/runtime/usr/bin/php8.4 ]]
install -d -m 0700 "$backup"
cp -a "$origin" "$backup/origin.nginx"
sha256sum /etc/nginx/sites-available/presensi-simasta.bpskotabatu.com \
    /home/bps/apps/presensi-web-service/nginx.conf \
    /var/www/presensi.simasta.bpskotabatu.com/.env > "$backup/presensi.sha256"
origin_changed=0
rollback() {
    result=$?
    if [[ $result -ne 0 && $origin_changed -eq 1 ]]; then
        cp -a "$backup/origin.nginx" "$origin"
        nginx -t && systemctl reload nginx
        systemctl stop simasta-web simasta-php || true
        echo "Activation failed; routing restored. Diagnostic files: $backup"
    fi
}
trap rollback EXIT
install -m 0640 -o root -g simasta "$source_dir/deploy/verify-production.php" "$target/deploy/verify-production.php"
install -m 0640 -o root -g simasta "$source_dir/deploy/nginx-app.conf" /etc/simasta/nginx-app.conf
runuser -u simasta -- /usr/sbin/nginx -t -c /etc/simasta/nginx-app.conf
systemctl restart simasta-php simasta-web
for attempt in {1..30}; do
    if curl --fail --silent --max-time 3 http://127.0.0.1:5013/login -o /dev/null; then break; fi
    sleep 1
done
curl --fail --silent --show-error --max-time 10 http://127.0.0.1:5013/login -o /dev/null
origin_changed=1
install -m 0644 "$source_dir/deploy/nginx-origin.conf" "$origin"
nginx -t
systemctl reload nginx
# A reload returns before every old worker exits. Wait for a response from 5013.
for attempt in {1..30}; do
    curl --silent --show-error --insecure --max-time 5 \
        --resolve simasta.bpskotabatu.com:443:127.0.0.1 \
        --dump-header "$backup/origin-headers.txt" \
        --output /dev/null https://simasta.bpskotabatu.com/login
    if grep -qi '^X-Simasta-App: simasta-5013' "$backup/origin-headers.txt"; then break; fi
    sleep 1
done
grep -qi '^X-Simasta-App: simasta-5013' "$backup/origin-headers.txt"
PHP_INI_SCAN_DIR= "$target/runtime/usr/bin/php8.4" -c /etc/simasta/php.ini \
    "$target/deploy/verify-production.php" < /etc/simasta/admin-bootstrap.json
sha256sum -c "$backup/presensi.sha256"
curl --fail --silent --show-error --max-time 10 http://127.0.0.1:5014/ -o /dev/null
initial_snapshot=$(find /var/backups/simasta -mindepth 2 -maxdepth 2 \
    -type f -name other-schemas-before.sql -print | sort | tail -n 1)
if [[ -n $initial_snapshot ]]; then
    runuser -u postgres -- pg_dump --schema-only --no-owner --no-privileges \
        -n iph_batu -n presensi -n management_talenta -n pdrb -n public db_magang \
        | sed '/^\\restrict /d; /^\\unrestrict /d' > "$backup/other-schemas-after.sql"
    cmp "$initial_snapshot" "$backup/other-schemas-after.sql"
    echo 'Other schema definitions match the original pre-install snapshot.'
fi
systemctl enable simasta-php simasta-web
origin_changed=0
echo 'SIMASTA active on 5013; presensi remains on 5014. Database and administrator credentials preserved.'
echo 'Private administrator credentials: sudo cat /etc/simasta/admin-first-login.txt'
echo "Backup: $backup"
