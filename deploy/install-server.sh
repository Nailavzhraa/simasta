#!/usr/bin/env bash
# First installation only. No presensi/IPH/Talenta configuration or credentials are reused.
set -Eeuo pipefail
umask 027

[[ $EUID -eq 0 ]] || { echo 'Run this reviewed installer with sudo.'; exit 1; }
source_dir=$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd -P)
stage=$(dirname -- "$source_dir")
target=/var/www/simasta.bpskotabatu.com
backup=/var/backups/simasta/$(date -u +%Y%m%dT%H%M%SZ)
origin=/etc/nginx/sites-available/simasta.bpskotabatu.com
presensi=/etc/nginx/sites-available/presensi-simasta.bpskotabatu.com

[[ -f $source_dir/public/build/manifest.json && -f $source_dir/vendor/autoload.php ]]
[[ -x $stage/runtime/usr/bin/php8.4 && -x $stage/runtime/usr/sbin/php-fpm8.4 ]]
[[ ! -e $target && ! -e /etc/simasta ]] || { echo 'SIMASTA installation already exists. Refusing to overwrite.'; exit 1; }
if ss -H -ltn 'sport = :5013' | grep -q .; then
    echo 'Port 5013 is occupied. Nothing has been replaced.'; exit 1
fi
[[ -f $origin && -f $presensi ]]
[[ -r /etc/ssl/certs/simasta.crt && -r /etc/ssl/private/simasta.key ]]
nginx -t
existing=$(runuser -u postgres -- psql --no-psqlrc -At db_magang -c \
    "SELECT (EXISTS(SELECT 1 FROM pg_namespace WHERE nspname='simasta') OR EXISTS(SELECT 1 FROM pg_roles WHERE rolname='simasta_app'))::int")
[[ $existing == 0 ]] || { echo 'SIMASTA schema or role already exists; inspect before installing.'; exit 1; }
curl --fail --silent --show-error --max-time 10 http://127.0.0.1:5014/ -o /dev/null

install -d -m 0700 "$backup"
cp -a "$origin" "$backup/origin.nginx"
sha256sum "$presensi" /home/bps/apps/presensi-web-service/nginx.conf \
    /var/www/presensi.simasta.bpskotabatu.com/.env > "$backup/presensi.sha256"

snapshot_other_schemas() {
    runuser -u postgres -- pg_dump --schema-only --no-owner --no-privileges \
        -n iph_batu -n presensi -n management_talenta -n pdrb -n public db_magang \
        | sed '/^\\restrict /d; /^\\unrestrict /d'
}
snapshot_other_schemas > "$backup/other-schemas-before.sql"
origin_changed=0
rollback_origin() {
    result=$?
    if [[ $result -ne 0 && $origin_changed -eq 1 ]]; then
        cp -a "$backup/origin.nginx" "$origin"
        nginx -t && systemctl reload nginx
        systemctl stop simasta-web.service simasta-php.service || true
        echo "Installation failed; original SIMASTA routing restored. Diagnostic files: $backup"
    fi
}
trap rollback_origin EXIT

id simasta >/dev/null 2>&1 || useradd --system --home-dir "$target" --shell /usr/sbin/nologin simasta
install -d -m 0750 -o root -g simasta "$target" /etc/simasta
tar -C "$source_dir" --exclude='./.env' --exclude='./.env.*' \
    --exclude='./.phpunit.cache' --exclude='./storage/*' --exclude='./bootstrap/cache/*' \
    -cf - . | tar -C "$target" -xf -
install -d -m 0750 "$target/runtime"
cp -a "$stage/runtime/usr" "$target/runtime/"
find "$target" -type d -exec chmod 0750 {} +
find "$target" -type f -exec chmod 0640 {} +
chown -R root:simasta "$target"
chmod 0750 "$target/runtime/usr/bin/php8.4" "$target/runtime/usr/sbin/php-fpm8.4"
install -d -m 0750 -o simasta -g simasta \
    "$target/storage/run/client" "$target/storage/run/proxy" \
    "$target/storage/run/fastcgi" "$target/storage/run/uploads" \
    "$target/storage/framework/cache/data" "$target/storage/framework/sessions" \
    "$target/storage/framework/views" "$target/storage/logs" \
    "$target/storage/app/public" "$target/storage/app/private" "$target/bootstrap/cache"
chown -R simasta:simasta "$target/storage" "$target/bootstrap/cache"

install -m 0640 -o root -g simasta "$source_dir/deploy/php.ini" /etc/simasta/php.ini
install -m 0640 -o root -g simasta "$source_dir/deploy/php-fpm.conf" /etc/simasta/php-fpm.conf
install -m 0640 -o root -g simasta "$source_dir/deploy/nginx-app.conf" /etc/simasta/nginx-app.conf
python3 "$source_dir/deploy/prepare-production.py"
chown root:simasta "$target/.env"
chmod 0640 "$target/.env"

php="$target/runtime/usr/bin/php8.4"
artisan() { runuser -u simasta -- env PHP_INI_SCAN_DIR= "$php" -c /etc/simasta/php.ini "$target/artisan" "$@"; }
artisan config:clear
artisan simasta:check-database
artisan migrate --force
artisan db:seed --class=DivisiSeeder --force
runuser -u simasta -- env PHP_INI_SCAN_DIR= "$php" -c /etc/simasta/php.ini \
    "$target/deploy/bootstrap-admin.php" < /etc/simasta/admin-bootstrap.json
ln -s "$target/storage/app/public" "$target/public/storage"
artisan optimize

install -m 0644 "$source_dir/deploy/simasta-php.service" /etc/systemd/system/simasta-php.service
install -m 0644 "$source_dir/deploy/simasta-web.service" /etc/systemd/system/simasta-web.service
runuser -u simasta -- env PHP_INI_SCAN_DIR= "$target/runtime/usr/sbin/php-fpm8.4" \
    -c /etc/simasta/php.ini -y /etc/simasta/php-fpm.conf --test
runuser -u simasta -- /usr/sbin/nginx -t -c /etc/simasta/nginx-app.conf
systemctl daemon-reload
systemctl start simasta-php.service simasta-web.service
for attempt in {1..20}; do
    if curl --fail --silent --max-time 3 http://127.0.0.1:5013/login -o /dev/null; then break; fi
    sleep 1
done
curl --fail --silent --show-error --max-time 10 http://127.0.0.1:5013/login -o /dev/null

origin_changed=1
install -m 0644 "$source_dir/deploy/nginx-origin.conf" "$origin"
nginx -t
systemctl reload nginx
for attempt in {1..30}; do
    curl --silent --show-error --insecure --max-time 5 \
        --resolve simasta.bpskotabatu.com:443:127.0.0.1 \
        --dump-header "$backup/origin-headers.txt" \
        --output /dev/null https://simasta.bpskotabatu.com/login
    if grep -qi '^X-Simasta-App: simasta-5013' "$backup/origin-headers.txt"; then break; fi
    sleep 1
done
grep -qi '^X-Simasta-App: simasta-5013' "$backup/origin-headers.txt"
PHP_INI_SCAN_DIR= "$php" -c /etc/simasta/php.ini "$target/deploy/verify-production.php" < /etc/simasta/admin-bootstrap.json
curl --fail --silent --show-error --max-time 10 http://127.0.0.1:5014/ -o /dev/null
sha256sum -c "$backup/presensi.sha256"
snapshot_other_schemas > "$backup/other-schemas-after.sql"
cmp "$backup/other-schemas-before.sql" "$backup/other-schemas-after.sql"
systemctl enable simasta-php.service simasta-web.service
origin_changed=0
echo 'SIMASTA installed: https://simasta.bpskotabatu.com (127.0.0.1:5013).'
echo 'Presensi remains on 5014. Other schema definitions match the pre-install snapshot.'
echo 'Private administrator credentials: sudo cat /etc/simasta/admin-first-login.txt'
echo "Backup: $backup"
echo 'Email and Google OAuth are deferred; email is currently logged locally.'
