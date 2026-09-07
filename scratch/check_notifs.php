<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CHECKING NOTIFICATIONS TABLE ===\n";
echo "Total notifications: " . \App\Models\Notification::count() . "\n\n";

foreach (\App\Models\Notification::latest()->take(10)->get() as $n) {
    echo "ID: {$n->id} | User ID: {$n->user_id} | Title: {$n->title} | Message: {$n->message} | Read: " . ($n->is_read ? 'YES' : 'NO') . "\n";
}

echo "\n=== CHECKING STUDENTS & PARENTS ===\n";
foreach (\App\Models\Student::with('parent')->get() as $s) {
    echo "Student ID: {$s->id} ({$s->name}) -> Parent ID: " . ($s->parent_id ?? 'NULL') . " (User: " . ($s->parent->name ?? 'NONE') . ")\n";
}
