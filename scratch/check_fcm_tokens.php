<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== USERS AND FCM TOKENS ===\n";
foreach (\App\Models\User::all() as $u) {
    $tokenPreview = $u->fcm_token ? (substr($u->fcm_token, 0, 20) . '...') : 'NULL (EMPTY!)';
    echo "ID: {$u->id} | Name: {$u->name} | Role: {$u->role} | FCM Token: {$tokenPreview}\n";
}

echo "\n=== LATEST NOTIFICATIONS IN DB ===\n";
foreach (\App\Models\Notification::latest()->take(5)->get() as $n) {
    echo "ID: {$n->id} | User ID: {$n->user_id} | Title: {$n->title} | Message: {$n->message} | Date: {$n->created_at}\n";
}
