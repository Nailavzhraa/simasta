#!/usr/bin/env bash
set -Eeuo pipefail
umask 077
[[ $EUID -eq 0 && $# -eq 1 ]] || { echo 'Usage: sudo bash activate.sh /private/path/google-oauth.json'; exit 1; }
source_dir=$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd -P)
target=/var/www/simasta.bpskotabatu.com
php="$target/runtime/usr/bin/php8.4"
export PHP_INI_SCAN_DIR=
[[ -f $target/.env && -x $php && -f $1 ]]
python3 "$source_dir/configure.py" "$1"
backup=/var/backups/simasta/google-$(date -u +%Y%m%dT%H%M%SZ)
install -d -m 0700 "$backup"
cp -a "$target/.env" "$backup/environment-before"
changed=0
main_cli() { (cd "$target" && runuser -u simasta -- "$php" -c /etc/simasta/php.ini artisan "$@"); }
rollback() {
    result=$?
    if [[ $result -ne 0 && $changed -eq 1 ]]; then
        cp -a "$backup/environment-before" "$target/.env"
        main_cli config:cache >/dev/null || true
        systemctl restart simasta-php || true
        echo "Google activation failed; previous configuration restored. Private backup: $backup"
    fi
}
trap rollback EXIT
changed=1
python3 "$source_dir/configure.py" "$1" --apply
main_cli config:cache
systemctl restart simasta-php
for attempt in {1..15}; do
    if curl --fail --silent --max-time 2 http://127.0.0.1:5013/login -o /dev/null; then break; fi
    sleep 1
done
python3 "$source_dir/verify-redirect.py"
"$php" -c /etc/simasta/php.ini "$target/deploy/verify-production.php" < /etc/simasta/admin-bootstrap.json
changed=0
echo 'Google redirect configured. Complete one real Google sign-in in your browser to verify Console consent and callback.'
echo "Private configuration backup: $backup"
