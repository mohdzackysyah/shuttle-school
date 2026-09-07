<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Student;
use App\Models\Schedule;
use App\Models\Trip;
use App\Models\TripPassenger;
use Carbon\Carbon;

echo "--- PARENT DASHBOARD DIAGNOSTIC ---\n";
$parents = User::where('role', 'parent')->get();
echo "Total Parents: " . $parents->count() . "\n\n";

foreach ($parents as $parent) {
    echo "Parent ID: {$parent->id} | Name: {$parent->name} | Phone: {$parent->phone}\n";
    $students = Student::where('parent_id', $parent->id)->get();
    echo "  Children Count: " . $students->count() . "\n";
    
    foreach ($students as $s) {
        echo "    - Student ID: {$s->id} | Name: {$s->name} | Route ID: " . ($s->complex->route_id ?? 'None') . "\n";
        
        // Check Schedules (Master schedule)
        $schedules = Schedule::whereHas('students', function($q) use ($s) {
            $q->where('students.id', $s->id);
        })->with(['route', 'driver', 'shuttle'])->get();
        
        echo "      Master Schedules Found: " . $schedules->count() . "\n";
        foreach ($schedules as $sched) {
            echo "        [Sched ID {$sched->id}] Day: {$sched->day_of_week} | Pickup: {$sched->pickup_time} | Dropoff: {$sched->dropoff_time} | Driver: " . ($sched->driver->name ?? '-') . " | Route: " . ($sched->route->name ?? '-') . "\n";
        }
        
        // Check Trips today
        $today = Carbon::today()->format('Y-m-d');
        $passengers = TripPassenger::where('student_id', $s->id)
            ->whereHas('trip', function($q) use ($today) {
                $q->whereDate('date', $today);
            })->with('trip')->get();
        echo "      Today Trips ({$today}) Count: " . $passengers->count() . "\n";
        foreach ($passengers as $tp) {
            echo "        [TripPassenger ID {$tp->id}] Trip Type: {$tp->trip->type} | Status: {$tp->status} | Trip Status: {$tp->trip->status}\n";
        }
    }
    echo "-----------------------------------------\n";
}
