<?php

// Core PHP password functions only; no application configuration is loaded.
try {
    $input = stream_get_contents(STDIN, 8193);
    if (strlen($input) > 8192) { exit(2); }
    $data = json_decode($input, true, flags: JSON_THROW_ON_ERROR);
    $password = $data['password'] ?? null;
    if (!is_string($password) || strlen($password) > 256) { exit(2); }
    if (($data['mode'] ?? null) === 'hash') {
        if (strlen($password) < 8 || strlen($password) > 72) { exit(2); }
        echo password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    } else {
        $hash = $data['hash'] ?? '';
        echo is_string($hash) && password_verify($password, $hash) ? 'true' : 'false';
    }
} catch (Throwable) {
    exit(2);
}
