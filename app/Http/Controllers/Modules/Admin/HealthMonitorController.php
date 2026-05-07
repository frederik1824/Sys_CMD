<?php

namespace App\Http\Controllers\Modules\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class HealthMonitorController extends Controller
{
    public function getSystemStatus()
    {
        $status = [
            'server' => $this->getServerMetrics(),
            'database' => $this->getDatabaseStatus(),
            'firebase' => $this->getFirebaseStatus(),
            'services' => $this->getServicesStatus(),
            'queues' => $this->getQueueStatus(),
            'uptime' => $this->getServerUptime(),
            'timestamp' => now()->toIso8601String(),
        ];

        return response()->json($status);
    }

    private function getServerMetrics()
    {
        // CPU Usage (Cross-platform)
        $cpu = 0;
        if (PHP_OS_FAMILY === 'Windows') {
            $cpuOutput = shell_exec('wmic cpu get loadpercentage');
            $cpu = (int) filter_var($cpuOutput, FILTER_SANITIZE_NUMBER_INT);
        } else {
            $load = sys_getloadavg();
            $cpu = round($load[0] * 100 / 4, 1); // Asumiendo 4 cores
        }

        // RAM Usage
        $ram = ['total' => 0, 'used' => 0, 'percent' => 0];
        if (PHP_OS_FAMILY === 'Windows') {
            $totalMem = (int) filter_var(shell_exec('wmic ComputerSystem get TotalPhysicalMemory'), FILTER_SANITIZE_NUMBER_INT);
            $freeMem = (int) filter_var(shell_exec('wmic OS get FreePhysicalMemory'), FILTER_SANITIZE_NUMBER_INT) * 1024;
            $ram['total'] = $totalMem;
            $ram['used'] = $totalMem - $freeMem;
            $ram['percent'] = round(($ram['used'] / $totalMem) * 100, 1);
        } else {
            $free = shell_exec('free -b | grep Mem');
            $mem = preg_split('/\s+/', trim($free));
            $ram['total'] = $mem[1];
            $ram['used'] = $mem[2];
            $ram['percent'] = round(($mem[2] / $mem[1]) * 100, 1);
        }

        // Disk
        $diskTotal = disk_total_space("/");
        $diskFree = disk_free_space("/");
        $diskUsed = $diskTotal - $diskFree;
        $diskPercent = round(($diskUsed / $diskTotal) * 100, 1);

        return [
            'cpu' => $cpu,
            'ram' => $ram,
            'disk' => ['total' => $diskTotal, 'used' => $diskUsed, 'percent' => $diskPercent],
            'os' => PHP_OS . ' ' . php_uname('r')
        ];
    }

    private function getDatabaseStatus()
    {
        try {
            $start = microtime(true);
            DB::connection()->getPdo();
            $latency = round((microtime(true) - $start) * 1000, 2);
            return ['status' => 'online', 'latency_ms' => $latency];
        } catch (\Exception $e) {
            return ['status' => 'offline', 'latency_ms' => 0];
        }
    }

    private function getFirebaseStatus()
    {
        $start = microtime(true);
        $connected = @fsockopen("firestore.googleapis.com", 443, $errno, $errstr, 2);
        if ($connected) {
            $latency = round((microtime(true) - $start) * 1000, 2);
            fclose($connected);
            return ['status' => 'online', 'latency_ms' => $latency, 'label' => 'Conectado'];
        }
        return ['status' => 'offline', 'latency_ms' => 0, 'label' => 'Sin Respuesta'];
    }

    private function getServicesStatus()
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return [
                ['name' => 'Web Server', 'status' => 'running'],
                ['name' => 'MySQL', 'status' => 'running'],
                ['name' => 'Redis', 'status' => 'stopped'],
            ];
        }

        return [
            ['name' => 'Nginx', 'status' => $this->checkService('nginx')],
            ['name' => 'PHP-FPM', 'status' => $this->checkService('php8.3-fpm')],
            ['name' => 'MySQL', 'status' => $this->checkService('mysql')],
        ];
    }

    private function checkService($name)
    {
        $status = shell_exec("systemctl is-active $name");
        return trim($status) === 'active' ? 'running' : 'stopped';
    }

    private function getQueueStatus()
    {
        try {
            $pending = DB::table('jobs')->count();
            return ['pending' => $pending, 'status' => ($pending > 50) ? 'warning' : 'healthy'];
        } catch (\Exception $e) {
            return ['status' => 'n/a'];
        }
    }

    private function getServerUptime()
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $uptime = shell_exec('wmic os get lastbootuptime');
            return "Boot: " . substr($uptime, 21, 14);
        }
        return trim(shell_exec('uptime -p'));
    }
}
