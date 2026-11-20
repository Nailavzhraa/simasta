<?php

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$credentials = json_decode(stream_get_contents(STDIN), true, flags: JSON_THROW_ON_ERROR);
if (User::where('role', 'admin')->exists()) {
    throw new RuntimeException('An administrator already exists; refusing to overwrite credentials.');
}
$user = User::create([
    'name' => $credentials['name'],
    'email' => $credentials['email'],
    'password' => $credentials['password'],
    'role' => 'admin',
]);
$user->forceFill(['email_verified_at' => now()])->save();
echo "Created production administrator without demo accounts.\n";
