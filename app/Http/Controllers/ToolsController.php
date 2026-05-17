<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\DB;

class ToolsController extends Controller
{
    public function index()
    {
        $migrations = $this->getMigrationStatus();
        $logExists = File::exists(storage_path('logs/laravel.log'));
        $logSize = $logExists ? $this->formatBytes(File::size(storage_path('logs/laravel.log'))) : '0 B';
        $isDown = File::exists(storage_path('framework/down'));

        return view('tools.index', compact('migrations', 'logSize', 'isDown'));
    }

    public function maintenanceView()
    {
        $isDown = File::exists(storage_path('framework/down'));
        return view('tools.maintenance', compact('isDown'));
    }

    public function toggleMaintenance(Request $request)
    {
        $action = $request->input('action');

        if ($action === 'enable') {
            $secret = $request->input('secret', '');
            $message = $request->input('message', 'Sistem sedang dalam pemeliharaan.');

            if (strlen($secret) < 6) {
                return back()->withErrors(['secret' => 'Secret key minimal 6 karakter.']);
            }

            // Simpan pesan ke file sementara
            File::put(storage_path('framework/down_message.txt'), $message);

            Artisan::call('down', [
                '--secret' => $secret,
                '--render' => "errors::503",
            ]);

            return back()->with('success', 'Maintenance mode diaktifkan. Bypass: /' . $secret);
        }

        // Hapus file pesan saat maintenance dimatikan
        if (File::exists(storage_path('framework/down_message.txt'))) {
            File::delete(storage_path('framework/down_message.txt'));
        }

        Artisan::call('up');
        return back()->with('success', 'Maintenance mode dinonaktifkan. Sistem kembali normal.');
    }

    public function logClear()
    {
        $logFile = storage_path('logs/laravel.log');
        if (File::exists($logFile)) {
            File::put($logFile, '');
        }

        return back()->with('success', 'Log berhasil dibersihkan.');
    }

    public function runMigrate()
    {
        Artisan::call('migrate', ['--force' => true]);

        return back()->with('success', 'Migration berhasil dijalankan.');
    }

    private function getMigrationStatus(): array
    {
        Artisan::call('migrate:status');
        $output = Artisan::output();
        $lines = explode("\n", trim($output));

        $status = [];
        foreach ($lines as $line) {
            if (str_contains($line, 'Ran')) {
                $status[] = ['file' => str_replace('Ran: ', '', $line), 'ran' => true];
            } elseif (str_contains($line, 'Pending')) {
                $status[] = ['file' => str_replace('Pending: ', '', $line), 'ran' => false];
            }
        }

        return $status;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    public function trashedProjects()
    {
        $projects = \App\Models\Project::onlyTrashed()->latest()->get();
        return view('tools.trashed-projects', compact('projects'));
    }

    public function hardDeleteProject($id)
    {
        $project = \App\Models\Project::onlyTrashed()->findOrFail($id);
        $project->forceDelete();
        return back()->with('success', 'Project berhasil dihapus permanen.');
    }

    public function downloadAuditLog()
    {
        $logs = Activity::with('causer')->latest()->get();
        $csvFileName = 'audit_log_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($logs) {
            echo "\xEF\xBB\xBF";

            $handle = fopen('php://output', 'w');

            // delimiter ; agar Excel Indonesia kebaca kolom
            fputcsv($handle, ['Tanggal', 'User', 'Aktivitas', 'Detail'], ';');

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->created_at?->format('d-m-Y H:i:s') ?? '-',
                    optional($log->causer)->name ?? 'System',
                    $log->description ?? '-',
                    json_encode($log->properties, JSON_UNESCAPED_UNICODE),
                ], ';');
            }

            fclose($handle);
        }, $csvFileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function clearAuditLog()
    {
        \Spatie\Activitylog\Models\Activity::truncate();
        return back()->with('success', 'Audit log berhasil dikosongkan.');
    }

    public function errorLogs()
    {
        $logFile = storage_path('logs/laravel.log');
        $logs = [];

        if (File::exists($logFile)) {
            $content = File::get($logFile);
            $pattern = '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\].*?\.(ERROR|WARNING|CRITICAL|INFO|DEBUG)\: (.*?)(?=^\[|\z)/ms';

            if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
                foreach (array_slice($matches, -50) as $match) {
                    $logs[] = [
                        'datetime' => $match[1],
                        'level'    => $match[2],
                        'message'  => trim($match[3]),
                    ];
                }
                $logs = array_reverse($logs);
            }
        }

        return view('tools.logs', compact('logs'));
    }

    public function auditLogs()
    {
        $logs = \Spatie\Activitylog\Models\Activity::with('causer')->latest()->paginate(20);
        return view('tools.audit', compact('logs'));
    }

    public function databaseView()
    {
        $backups = [];
        $backupPath = storage_path('app/backups');
        if (File::isDirectory($backupPath)) {
            $files = File::files($backupPath);
            foreach ($files as $file) {
                $backups[] = [
                    'name' => $file->getFilename(),
                    'size' => $this->formatBytes($file->getSize()),
                    'date' => date('d M Y H:i', $file->getMTime()),
                ];
            }
            usort($backups, fn($a, $b) => strcmp($b['date'], $a['date']));
        }
        return view('tools.database', compact('backups'));
    }

    public function databaseDownload()
    {
        $filename = 'backup_pmo_' . now()->format('Y-m-d_His') . '.sql';

        $filepath = storage_path('app/' . $filename);

        $db = config('database.connections.mysql.database');

        $command = "mysqldump --user=" . env('DB_USERNAME') .
            " --password=" . env('DB_PASSWORD') .
            " " . $db .
            " > " . escapeshellarg($filepath);

        exec($command, $output, $result);

        if ($result !== 0 || !File::exists($filepath)) {
            return back()->withErrors([
                'error' => 'Gagal download backup database.'
            ]);
        }

        return response()->download($filepath)->deleteFileAfterSend(true);
    }

    public function databaseBackupServer()
    {
        $filename = 'backup_pmo_' . now()->format('Y-m-d_His') . '.sql';

        $backupPath = storage_path('app/backups');

        if (!File::isDirectory($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }

        $filepath = $backupPath . '/' . $filename;

        $db = config('database.connections.mysql.database');

        $command = "mysqldump --user=" . env('DB_USERNAME') .
            " --password=" . env('DB_PASSWORD') .
            " " . $db .
            " > " . escapeshellarg($filepath);

        exec($command, $output, $result);

        if ($result !== 0) {
            return back()->withErrors([
                'error' => 'Gagal backup database.'
            ]);
        }

        return back()->with('success', 'Backup berhasil disimpan.');
    }

    public function databaseRestore(Request $request)
    {
        $request->validate([
            'sql_file' => 'required|file|mimes:sql,txt|max:204800',
        ]);

        $file = $request->file('sql_file');

        $db = config('database.connections.mysql.database');

        $filepath = $file->getRealPath();

        $command = "mysql --user=" . env('DB_USERNAME') .
            " --password=" . env('DB_PASSWORD') .
            " " . $db .
            " < " . escapeshellarg($filepath);

        exec($command, $output, $result);

        if ($result !== 0) {
            return back()->withErrors([
                'error' => 'Restore database gagal.'
            ]);
        }

        Artisan::call('cache:clear');

        return back()->with('success', 'Database berhasil di-restore.');
    }

    public function databaseRestoreServer(Request $request)
    {
        $filename = $request->input('filename');

        $filepath = storage_path('app/backups/' . $filename);

        if (!File::exists($filepath)) {
            return back()->withErrors([
                'error' => 'File backup tidak ditemukan.'
            ]);
        }

        $db = config('database.connections.mysql.database');

        $command = "mysql --user=" . env('DB_USERNAME') .
            " --password=" . env('DB_PASSWORD') .
            " " . $db .
            " < " . escapeshellarg($filepath);

        exec($command, $output, $result);

        if ($result !== 0) {
            return back()->withErrors([
                'error' => 'Restore database gagal.'
            ]);
        }

        Artisan::call('cache:clear');

        return back()->with('success', 'Database berhasil di-restore dari server.');
    }

    public function systemHealth()
    {
        $data = [
            // PHP & Laravel
            'php_version'     => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment'     => app()->environment(),
            'debug_mode'      => config('app.debug') ? 'Aktif ⚠️' : 'Nonaktif ✅',
            'timezone'        => config('app.timezone'),

            // Database
            'db_connection'   => config('database.default'),
            'db_name'         => config('database.connections.' . config('database.default') . '.database'),
            'db_size'         => $this->getDatabaseSize(),

            // Storage
            'storage_used'    => $this->getStorageUsed(),
            'storage_total'   => $this->getStorageTotal(),
            'storage_percent' => $this->getStoragePercent(),

            // Cache & Session
            'cache_driver'    => config('cache.default'),
            'session_driver'  => config('session.driver'),

            // Log
            'log_size'        => $this->getLogSize(),

            // Uptime
            'server_time'     => now()->format('d/m/Y H:i:s'),
        ];

        return view('tools.health', compact('data'));
    }

    private function getDatabaseSize(): string
    {
        try {
            $dbName = config('database.connections.' . config('database.default') . '.database');
            $result = DB::select("
                SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size
                FROM information_schema.tables
                WHERE table_schema = ?
            ", [$dbName]);
            return ($result[0]->size ?? 0) . ' MB';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    private function getStorageUsed(): string
    {
        $bytes = 0;
        $path  = storage_path('app/public');
        if (is_dir($path)) {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)) as $file) {
                $bytes += $file->getSize();
            }
        }
        return round($bytes / 1024 / 1024, 2) . ' MB';
    }

    private function getStorageTotal(): string
    {
        return round(disk_total_space('/') / 1024 / 1024 / 1024, 2) . ' GB';
    }

    private function getStoragePercent(): int
    {
        $total = disk_total_space('/');
        $free  = disk_free_space('/');
        if ($total <= 0) return 0;
        return (int) round((($total - $free) / $total) * 100);
    }

    private function getLogSize(): string
    {
        $logFile = storage_path('logs/laravel.log');
        if (!file_exists($logFile)) return '0 KB';
        $bytes = filesize($logFile);
        return $bytes > 1048576
            ? round($bytes / 1048576, 2) . ' MB'
            : round($bytes / 1024, 2) . ' KB';
    }

    public function cacheView()
    {
        return view('tools.cache');
    }

    public function clearSelectedCache(Request $request)
    {
        $type = $request->type;

        match ($type) {
            'all'    => Artisan::call('optimize:clear'),
            'app'    => Artisan::call('cache:clear'),
            'view'   => Artisan::call('view:clear'),
            'config' => Artisan::call('config:clear'),
            'route'  => Artisan::call('route:clear'),
            'event'  => Artisan::call('event:clear'),
            default  => null,
        };

        return back()->with('success', 'Cache (' . $type . ') berhasil dibersihkan.');
    }

    public function loginHistoryView()
    {
        $logs = \App\Models\LoginHistory::with('user')
            ->latest()
            ->paginate(20);

        return view('tools.login-history', compact('logs'));
    }

    public function downloadLoginHistory()
    {
        $logs = \App\Models\LoginHistory::with('user')->latest()->get();
        $filename = 'login_history_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($logs) {
            echo "\xEF\xBB\xBF";
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Tanggal Login', 'Waktu Logout', 'User', 'IP Address', 'Browser'], ';');

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->logged_in_at?->format('d-m-Y H:i') ?? '-',
                    $log->logged_out_at?->format('d-m-Y H:i') ?? 'Masih aktif',
                    $log->user->name ?? '-',
                    $log->ip_address ?? '-',
                    $log->user_agent ?? '-',
                ], ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function clearLoginHistory()
    {
        \App\Models\LoginHistory::truncate();
        return back()->with('success', 'Login history berhasil dikosongkan.');
    }
}
