<?php

// Installer-only smoke test. Credentials enter through stdin and stay out of logs.
$credentials = json_decode(stream_get_contents(STDIN), true, flags: JSON_THROW_ON_ERROR);
$curl = curl_init();
$responseHeaders = [];
curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEFILE => '',
    CURLOPT_RESOLVE => ['simasta.bpskotabatu.com:443:127.0.0.1'],
    // This request stays on loopback and uses the existing origin certificate.
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HEADERFUNCTION => function ($curl, string $line) use (&$responseHeaders): int {
        if (str_contains($line, ':')) {
            [$name, $value] = explode(':', $line, 2);
            $responseHeaders[strtolower(trim($name))] = trim($value);
        }
        return strlen($line);
    },
]);
$request = function (string $path, int $expected, ?array $form = null) use ($curl, &$responseHeaders): string {
    $responseHeaders = [];
    curl_setopt($curl, CURLOPT_URL, 'https://simasta.bpskotabatu.com'.$path);
    if ($form === null) {
        curl_setopt($curl, CURLOPT_HTTPGET, true);
    } else {
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($form));
    }
    $body = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    if ($body === false || $status !== $expected) {
        throw new RuntimeException("Smoke test failed: {$path} expected {$expected}, got {$status}. ".curl_error($curl));
    }
    if (($responseHeaders['x-simasta-app'] ?? '') !== 'simasta-5013') {
        throw new RuntimeException('Origin has not switched to the SIMASTA backend yet.');
    }
    echo "PASS {$path}: {$status}\n";
    return $body;
};
$login = $request('/login', 200);
if (!preg_match('/name="_token"[^>]*value="([^"]+)"/', $login, $matches)) {
    throw new RuntimeException('Missing CSRF token on login form.');
}
$request('/login', 302, [
    '_token' => html_entity_decode($matches[1]),
    'email' => $credentials['email'],
    'password' => $credentials['password'],
]);
foreach (['dashboard', 'mahasiswa', 'pengajuan', 'penempatan', 'logbook', 'divisi', 'penilaian', 'laporan', 'pembimbing'] as $page) {
    $request('/admin/'.$page, 200);
}
$dashboard = $request('/admin/dashboard', 200);
if (!preg_match('/name="_token"[^>]*value="([^"]+)"/', $dashboard, $matches)) {
    throw new RuntimeException('Missing logout CSRF token.');
}
$request('/logout', 302, ['_token' => html_entity_decode($matches[1])]);
$request('/admin/dashboard', 302);
$request('/forgot-password', 200);
$request('/reset-password/test-token', 200);
$request('/.env', 403);
$request('/preview/reset', 404);
