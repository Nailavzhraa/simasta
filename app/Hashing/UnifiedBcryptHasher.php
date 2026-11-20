<?php

namespace App\Hashing;

use Illuminate\Hashing\BcryptHasher;
use Symfony\Component\Process\Process;

class UnifiedBcryptHasher extends BcryptHasher
{
    public function needsRehash($hashedValue, array $options = [])
    {
        // Legacy scrypt supports passwords beyond bcrypt's 72-byte limit.
        // Keep its full-length verification until the user changes the password.
        if (is_string($hashedValue) && str_starts_with($hashedValue, 'scrypt$')) {
            return false;
        }
        return parent::needsRehash($hashedValue, $options);
    }

    public function check(#[\SensitiveParameter] $value, $hashedValue, array $options = [])
    {
        if ($hashedValue === '!google-only' || $hashedValue === '!simasta-managed') {
            return false;
        }
        if (is_string($hashedValue) && str_starts_with($hashedValue, 'scrypt$')) {
            if (!is_string($value) || strlen($value) > 256) {
                return false;
            }
            $process = new Process([
                config('services.presensi.node_binary', 'node'),
                base_path('scripts/verify-presensi-password.mjs'),
            ]);
            $process->setInput(json_encode(['password' => $value, 'hash' => $hashedValue], JSON_THROW_ON_ERROR));
            $process->setTimeout(5);
            $process->run();
            return $process->isSuccessful() && $process->getOutput() === 'true';
        }
        return parent::check($value, $hashedValue, $options);
    }
}
