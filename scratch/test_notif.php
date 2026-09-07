<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$n = \App\Models\Notification::create([
    'user_id' => 2,
    'title' => 'Tes Notifikasi System Bar 🔔',
    'message' => 'Ananda Budi Jr telah dijemput oleh driver.',
    'type' => 'picked_up',
    'data' => ['student_name' => 'Budi Jr']
]);

echo "Created Notification ID: {$n->id}\n";
echo "Total notifications now: " . \App\Models\Notification::count() . "\n";
