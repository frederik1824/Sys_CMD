<?php

namespace App\Livewire\Dashboard;

use App\Models\FirebaseSyncLog;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class LiveSyncMonitor extends Component
{
    public $lastSync;
    public $status = 'idle'; // idle, running, failed, success
    public $intensity = 0;
    public $activeProcesses = 0;
    public $syncHealth = 100;

    public function mount()
    {
        $this->updateStats();
    }

    public function updateStats()
    {
        $this->lastSync = FirebaseSyncLog::latest()->first();
        
        if ($this->lastSync) {
            $this->status = $this->lastSync->status === 'finished' ? 'success' : $this->lastSync->status;
            
            // Calculate intensity based on items count and time
            $baseIntensity = $this->lastSync->items_count > 0 ? min(100, $this->lastSync->items_count / 10) : 0;
            
            // If it finished within the last 5 minutes, keep some "afterglow" intensity
            $diffMinutes = $this->lastSync->finished_at ? $this->lastSync->finished_at->diffInMinutes(now()) : 0;
            if ($diffMinutes < 5) {
                $this->intensity = $baseIntensity * (1 - ($diffMinutes / 5));
            } else {
                $this->intensity = 0;
                $this->status = 'idle';
            }
        }

        // Check for currently running jobs if possible (simplified)
        $this->activeProcesses = DB::table('jobs')->where('queue', 'default')->count();
        if ($this->activeProcesses > 0) {
            $this->status = 'running';
            $this->intensity = min(100, 50 + ($this->activeProcesses * 10));
        }
    }

    public function render()
    {
        return view('livewire.dashboard.live-sync-monitor');
    }
}
