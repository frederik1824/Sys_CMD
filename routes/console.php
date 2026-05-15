<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Scheduled Backups based on Database Settings
try {
    if (Schema::hasTable('backup_settings')) {
        $settings = \App\Models\BackupSetting::first();
        if ($settings && $settings->is_automated) {
            $schedule = Schedule::command('backup:automated');
            
            if ($settings->schedule_frequency === 'daily') {
                $schedule->dailyAt($settings->schedule_time);
            } elseif ($settings->schedule_frequency === 'weekly') {
                $schedule->weekly()->at($settings->schedule_time);
            } elseif ($settings->schedule_frequency === 'monthly') {
                $schedule->monthly()->at($settings->schedule_time);
            }
        }
    }
} catch (\Exception $e) {
    // Table doesn't exist yet or DB not connected
}

// ─────────────────────────────────────────────────────────────────────────────
// AUTO FIREBASE SYNC — Se activa a las 12:05 AM cuando Google resetea la cuota
// Firebase resetea las cuotas a medianoche UTC. Con 5 min de margen es seguro.
// ─────────────────────────────────────────────────────────────────────────────
/*
Schedule::command('firebase:auto-midnight-sync')
    ->dailyAt('00:05')
    ->name('firebase-auto-midnight-sync')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/firebase-scheduled.log'));
*/
// Cron Heartbeat para el Monitor de Salud
Schedule::call(function () {
    \Illuminate\Support\Facades\Cache::put('cron_last_heartbeat', now());
})->everyMinute();

// Warm up Executive Dashboard Cache
Schedule::job(new \App\Jobs\WarmExecutiveDashboardCache)->everyFifteenMinutes();
