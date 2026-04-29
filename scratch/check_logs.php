<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$logs = \App\Models\FirebaseSyncLog::latest()->limit(5)->get();
foreach ($logs as $log) {
    echo "ID: {$log->id} | Type: {$log->type} | Status: {$log->status}\n";
    echo "Summary: " . json_encode($log->summary) . "\n";
    echo "Finished: {$log->finished_at}\n";
    echo "----------------------------------------\n";
}
