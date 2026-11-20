#!/usr/bin/env python3
"""Load a Google web OAuth JSON file without exposing its secret in output."""
import argparse
import json
import os
from pathlib import Path
import re

CALLBACK = 'https://simasta.bpskotabatu.com/auth/google/callback'


def credentials(path):
    if path.stat().st_size > 32768:
        raise ValueError('OAuth JSON is too large.')
    data = json.loads(path.read_text())
    if not isinstance(data, dict):
        raise ValueError('OAuth JSON must be an object.')
    web = data.get('web')
    if not isinstance(web, dict):
        raise ValueError('Choose a Google OAuth client of type Web application.')
    client_id = web.get('client_id', '')
    secret = web.get('client_secret', '')
    if not isinstance(client_id, str) or not re.fullmatch(r'\d+-[A-Za-z0-9_-]+\.apps\.googleusercontent\.com', client_id):
        raise ValueError('The OAuth client ID is missing or invalid.')
    if not isinstance(secret, str) or not re.fullmatch(r'[A-Za-z0-9_-]{16,256}', secret):
        raise ValueError('The OAuth client secret is missing or invalid.')
    if CALLBACK not in web.get('redirect_uris', []):
        raise ValueError('Add the SIMASTA production callback to Authorized redirect URIs, save, and download the JSON again.')
    return {'GOOGLE_CLIENT_ID': client_id, 'GOOGLE_CLIENT_SECRET': secret, 'GOOGLE_REDIRECT_URI': CALLBACK}


def apply(path, updates):
    metadata = path.stat()
    text = path.read_text()
    for name, value in updates.items():
        pattern = re.compile(r'^' + re.escape(name) + r'=.*$', re.MULTILINE)
        entry = name + '=' + value
        text = pattern.sub(lambda _: entry, text) if pattern.search(text) else text.rstrip('\n') + '\n' + entry + '\n'
    temporary = path.with_name('.env.google-new')
    fd = os.open(temporary, os.O_WRONLY | os.O_CREAT | os.O_EXCL, 0o600)
    with os.fdopen(fd, 'w') as output:
        output.write(text)
    os.chown(temporary, metadata.st_uid, metadata.st_gid)
    os.chmod(temporary, metadata.st_mode & 0o777)
    temporary.replace(path)


if __name__ == '__main__':
    parser = argparse.ArgumentParser()
    parser.add_argument('json_file', type=Path)
    parser.add_argument('--apply', action='store_true')
    args = parser.parse_args()
    try:
        settings = credentials(args.json_file)
        if args.apply:
            if os.geteuid() != 0:
                raise ValueError('Run installation as root.')
            apply(Path('/var/www/simasta.bpskotabatu.com/.env'), settings)
            print('Google OAuth configuration installed without changing database or mail settings.')
        else:
            print('OAuth JSON valid; the production callback is configured. Secret omitted.')
    except (ValueError, OSError, TypeError):
        # JSON contents and parser exception context can contain a credential.
        raise SystemExit('OAuth JSON validation/configuration failed. Check the Web application client, client secret, and SIMASTA callback locally.')
