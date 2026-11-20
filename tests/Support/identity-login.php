<?php

// Used only by the isolated integration test cluster. No HTTP endpoint is exposed.
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$input = json_decode(stream_get_contents(STDIN), true, flags: JSON_THROW_ON_ERROR);
$kernel = $app->make(Kernel::class);
$response = $kernel->handle(Request::create('/login', 'POST', $input));
echo json_encode(['status' => $response->getStatusCode(), 'authenticated' => Auth::check()]);
