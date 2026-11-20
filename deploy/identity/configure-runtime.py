#!/usr/bin/env python3
"""Create a dedicated identity runtime login; keep secrets in root-owned stdin/files."""
import os
from pathlib import Path
import re
import secrets
import subprocess


def postgres(sql):
    result = subprocess.run(
        ['runuser', '-u', 'postgres', '--', 'psql', '-X', '-v', 'ON_ERROR_STOP=1', '-d', 'db_magang'],
        input=sql, text=True, capture_output=True,
    )
    if result.returncode:
        # An SQL diagnostic can include a password-bearing statement.
        raise RuntimeError('Dedicated identity database role could not be configured; inspect locally as root.')


def update_environment(path, updates):
    content = path.read_text()
    for name, value in updates.items():
        pattern = re.compile(r'^' + re.escape(name) + r'=.*$', re.MULTILINE)
        entry = name + '=' + value
        content = pattern.sub(lambda _: entry, content) if pattern.search(content) else content.rstrip('\n') + '\n' + entry + '\n'
    metadata = path.stat()
    temporary = path.with_name(path.name + '.identity-new')
    fd = os.open(temporary, os.O_WRONLY | os.O_CREAT | os.O_EXCL, 0o600)
    with os.fdopen(fd, 'w') as output:
        output.write(content)
    os.chown(temporary, metadata.st_uid, metadata.st_gid)
    os.chmod(temporary, metadata.st_mode & 0o777)
    temporary.replace(path)


if os.geteuid() != 0:
    raise SystemExit('Run as root.')

password = secrets.token_hex(32)
postgres("""
DO $$ BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_roles WHERE rolname='presensi_identity_app') THEN
        CREATE ROLE presensi_identity_app LOGIN NOSUPERUSER NOCREATEDB NOCREATEROLE NOINHERIT;
    ELSIF EXISTS (SELECT 1 FROM pg_roles WHERE rolname='presensi_identity_app'
        AND (rolsuper OR rolcreatedb OR rolcreaterole OR rolreplication OR rolbypassrls)) THEN
        RAISE EXCEPTION 'Unexpected privileged identity role';
    END IF;
END $$;
ALTER ROLE presensi_identity_app PASSWORD '""" + password + "';\n")

update_environment(Path('/home/bps/apps/presensi-backend/.env'), {
    'PGUSER': 'presensi_identity_app', 'PGPASSWORD': password,
    'SIMASTA_IDENTITY_ENABLED': 'true', 'SIMASTA_PHP_BINARY': '/usr/bin/php',
})
update_environment(Path('/var/www/simasta.bpskotabatu.com/.env'), {
    'PRESENSI_NODE_BINARY': '/usr/bin/node',
})
print('Dedicated database login configured; existing Google and mail settings preserved.')
