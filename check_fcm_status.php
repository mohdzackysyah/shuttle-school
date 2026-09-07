<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$users = App\Models\User::all();
foreach ($users as $u) {
    echo "ID: {$u->id} | Name: {$u->name} | Role: {$u->role} | FCM Token: " . ($u->fcm_token ? substr($u->fcm_token, 0, 35) . '...' : 'NULL') . "\n";
}

$notifications = App\Models\Notification::latest()->take(5)->get();
echo "\n--- Recent Notifications in DB ---\n";
foreach ($notifications as $n) {
    echo "ID: {$n->id} | User ID: {$n->user_id} | Title: {$n->title} | Message: {$n->message} | Read: {$n->is_read}\n";
}
