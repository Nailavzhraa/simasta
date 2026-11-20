#!/usr/bin/env python3
"""Create credentials and the new schema; never read another application's secrets."""
import base64
import json
from pathlib import Path
import secrets
import subprocess

target = Path('/var/www/simasta.bpskotabatu.com')
database_password = secrets.token_hex(32)
admin_password = secrets.token_urlsafe(24)

sql = f"""
BEGIN;
CREATE ROLE simasta_app LOGIN PASSWORD '{database_password}'
    NOSUPERUSER NOCREATEDB NOCREATEROLE NOINHERIT;
CREATE SCHEMA simasta AUTHORIZATION simasta_app;
REVOKE ALL ON SCHEMA simasta FROM PUBLIC;
GRANT CONNECT ON DATABASE db_magang TO simasta_app;
COMMIT;
"""
subprocess.run([
    'runuser', '-u', 'postgres', '--', 'psql', '--no-psqlrc',
    '-v', 'ON_ERROR_STOP=1', 'db_magang',
], input=sql, text=True, check=True)

configuration = {
    'APP_NAME': 'SIMASTA',
    'APP_ENV': 'production',
    'APP_KEY': 'base64:' + base64.b64encode(secrets.token_bytes(32)).decode(),
    'APP_DEBUG': 'false',
    'APP_URL': 'https://simasta.bpskotabatu.com',
    'APP_TIMEZONE': 'Asia/Jakarta',
    'APP_LOCALE': 'id',
    'APP_FALLBACK_LOCALE': 'en',
    'LOG_CHANNEL': 'stack',
    'LOG_LEVEL': 'warning',
    'DB_CONNECTION': 'pgsql',
    'DB_HOST': '127.0.0.1',
    'DB_PORT': '5432',
    'DB_DATABASE': 'db_magang',
    'DB_USERNAME': 'simasta_app',
    'DB_PASSWORD': database_password,
    'DB_SCHEMA': 'simasta',
    'DB_SSLMODE': 'prefer',
    'SESSION_DRIVER': 'database',
    'SESSION_LIFETIME': '120',
    'SESSION_ENCRYPT': 'true',
    'SESSION_COOKIE': 'simasta_session',
    'SESSION_DOMAIN': 'null',
    'SESSION_SECURE_COOKIE': 'true',
    'SESSION_HTTP_ONLY': 'true',
    'SESSION_SAME_SITE': 'lax',
    'CACHE_STORE': 'database',
    'CACHE_PREFIX': 'simasta_cache_',
    'QUEUE_CONNECTION': 'sync',
    'FILESYSTEM_DISK': 'local',
    # Email/Google setup is explicitly deferred by the owner.
    'MAIL_MAILER': 'log',
    'MAIL_FROM_ADDRESS': 'noreply@bpskotabatu.com',
    'MAIL_FROM_NAME': 'SIMASTA',
    'GOOGLE_CLIENT_ID': '',
    'GOOGLE_CLIENT_SECRET': '',
    'GOOGLE_REDIRECT_URI': 'https://simasta.bpskotabatu.com/auth/google/callback',
    'PRESENSI_URL': 'https://presensi-simasta.bpskotabatu.com',
}
with (target / '.env').open('x') as output:
    output.write(''.join(f'{key}={value}\n' for key, value in configuration.items()))

credentials = {
    'name': 'Admin SIMASTA',
    'email': 'admin@simasta.bpskotabatu.com',
    'password': admin_password,
}
with Path('/etc/simasta/admin-bootstrap.json').open('x') as output:
    json.dump(credentials, output)
Path('/etc/simasta/admin-bootstrap.json').chmod(0o600)
Path('/etc/simasta/admin-first-login.txt').write_text(
    'URL: https://simasta.bpskotabatu.com/login\n'
    f'Email: {credentials["email"]}\nPassword: {admin_password}\n'
)
Path('/etc/simasta/admin-first-login.txt').chmod(0o600)
print('Created dedicated simasta schema, role, and private production configuration.')
