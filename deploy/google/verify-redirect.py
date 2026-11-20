#!/usr/bin/env python3
import urllib.error
import urllib.parse
import urllib.request


class NoRedirect(urllib.request.HTTPRedirectHandler):
    def redirect_request(self, req, fp, code, msg, headers, newurl):
        return None


opener = urllib.request.build_opener(NoRedirect())
request = urllib.request.Request('http://127.0.0.1:5013/auth/google/redirect', headers={
    'Host': 'simasta.bpskotabatu.com', 'X-Forwarded-Proto': 'https',
})
try:
    response = opener.open(request, timeout=15)
except urllib.error.HTTPError as response:
    if response.code != 302:
        raise SystemExit('Google redirect did not return HTTP 302.')
    location = response.headers.get('Location', '')
    response.close()
else:
    response.close()
    raise SystemExit('Google redirect was not produced.')
target = urllib.parse.urlparse(location)
query = urllib.parse.parse_qs(target.query)
assert target.scheme == 'https' and target.hostname == 'accounts.google.com'
assert query.get('client_id', [''])[0].endswith('.apps.googleusercontent.com')
assert query.get('redirect_uri') == ['https://simasta.bpskotabatu.com/auth/google/callback']
assert query.get('state', [''])[0]
assert query.get('response_type') == ['code']
assert {'openid', 'email', 'profile'}.issubset(set(query.get('scope', [''])[0].split()))
assert 'client_secret' not in query
print('PASS: Google authorization redirect includes client ID, exact production callback, state, and login scopes.')
