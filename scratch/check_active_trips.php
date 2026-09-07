<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ALL TRIPS TODAY ===\n";
foreach (\App\Models\Trip::with('passengers.student')->latest()->take(5)->get() as $t) {
    echo "Trip ID: {$t->id} | Date: {$t->date} | Type: {$t->type} | Status: {$t->status} | Driver ID: {$t->driver_id}\n";
    foreach ($t->passengers as $p) {
        echo "   - Passenger ID: {$p->id} | Student: {$p->student->name} (ID: {$p->student_id}) | Parent ID: " . ($p->student->parent_id ?? 'NULL') . " | Status: {$p->status}\n";
    }
}

echo "\n=== ALL NOTIFICATIONS CREATED TODAY ===\n";
foreach (\App\Models\Notification::latest()->take(10)->get() as $n) {
    echo "Notif ID: {$n->id} | User ID: {$n->user_id} | Title: {$n->title} | Time: {$n->created_at}\n";
}
