<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function base64UrlEncode($text) {
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($text));
}

function getGoogleAccessToken() {
    $jsonPath = storage_path('app/firebase-service-account.json');
    if (!file_exists($jsonPath)) {
        echo "Service Account file not found!\n";
        return null;
    }

    $sa = json_decode(file_get_contents($jsonPath), true);
    $now = time();

    $header = base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
    $payload = base64UrlEncode(json_encode([
        'iss' => $sa['client_email'],
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud' => 'https://oauth2.googleapis.com/token',
        'iat' => $now,
        'exp' => $now + 3600
    ]));

    $signature = '';
    openssl_sign("$header.$payload", $signature, $sa['private_key'], OPENSSL_ALGO_SHA256);
    $jwt = "$header.$payload." . base64UrlEncode($signature);

    $response = \Illuminate\Support\Facades\Http::asForm()->post('https://oauth2.googleapis.com/token', [
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
    ]);

    if ($response->successful()) {
        return $response->json('access_token');
    }

    echo "OAuth Token Error: " . $response->body() . "\n";
    return null;
}

$token = getGoogleAccessToken();
echo "Generated Google Access Token: " . ($token ? (substr($token, 0, 25) . '...') : 'FAILED') . "\n";
