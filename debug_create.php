<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::create('/protocolo-sanidad/create', 'GET')
);

echo "Response Status: " . $response->getStatusCode() . PHP_EOL;

if ($response->getStatusCode() !== 200) {
    echo "Content: " . $response->getContent() . PHP_EOL;
}