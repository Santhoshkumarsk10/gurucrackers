<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseManagerController extends Controller
{
    /**
     * Tables that should NEVER be cleared by the admin (system-critical)
     */
    const PROTECTED_TABLES = [
        'migrations',
        'users',
        'shops',
        'password_reset_tokens',
        'jobs',
        'failed_jobs',
        'job_batches',
    ];

    /**
     * Clearable tables with human-friendly info
     */
    const TABLE_META = [
        'orders'            => ['label' => '📦 Orders',              'color' => 'rose',    'desc' => 'All customer orders'],
        'order_items'       => ['label' => '🛒 Order Items',          'color' => 'rose',    'desc' => 'Products inside orders'],
        'whatsapp_messages' => ['label' => '💬 WhatsApp Messages',    'color' => 'emerald', 'desc' => 'All WA message logs'],
        'audit_logs'        => ['label' => '📋 Audit Logs',           'color' => 'indigo',  'desc' => 'Activity audit trail'],
        'categories'        => ['label' => '🏷 Categories',           'color' => 'amber',   'desc' => 'Product categories'],
        'products'          => ['label' => '🎆 Products',             'color' => 'amber',   'desc' => 'All products in catalog'],
        'banners'           => ['label' => '🖼 Banners',              'color' => 'purple',  'desc' => 'Homepage banners'],
        'sessions'          => ['label' => '🔐 Sessions',             'color' => 'slate',   'desc' => 'Active user sessions'],
        'cache'             => ['label' => '⚡ Cache',                'color' => 'slate',   'desc' => 'Application cache data'],
        'cache_locks'       => ['label' => '🔒 Cache Locks',          'color' => 'slate',   'desc' => 'Cache locking entries'],
    ];

    /**
     * Show the database manager page
     */
    public function index()
    {
        $tables = $this->getTableStats();
        $backupFiles = $this->getBackupFiles();
        $adminEmail = Auth::user()->email ?? config('mail.from.address', 'admin@gurucrackers.com');

        return view('admin.database.index', compact('tables', 'backupFiles', 'adminEmail'));
    }

    /**
     * Generate full database backup
     */
    public function backup(Request $request)
    {
        $filename = $this->generateBackup();

        if (!$filename) {
            return back()->with('error', '❌ Backup failed. Please check server permissions.');
        }

        // Log this action
        $this->logDatabaseAction('full_backup', 'Full database backup created: ' . $filename);

        if ($request->boolean('download')) {
            $path = storage_path('app/db_backups/' . $filename);
            return response()->download($path, $filename, [
                'Content-Type'        => 'application/sql',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        }

        return back()->with('success', '✅ Backup created: <strong>' . $filename . '</strong>');
    }

    /**
     * Download a specific backup file
     */
    public function downloadBackup($filename)
    {
        // Security: only allow .sql.gz or .sql files in backup dir
        if (!preg_match('/^guru_crackers_backup_[\d_]+\.(sql|sql\.gz)$/', $filename)) {
            abort(403, 'Invalid backup filename.');
        }

        $path = storage_path('app/db_backups/' . $filename);
        if (!file_exists($path)) {
            abort(404, 'Backup file not found.');
        }

        return response()->download($path, $filename);
    }

    /**
     * Delete a backup file
     */
    public function deleteBackup($filename)
    {
        if (!preg_match('/^guru_crackers_backup_[\d_]+\.(sql|sql\.gz)$/', $filename)) {
            abort(403, 'Invalid backup filename.');
        }

        $path = storage_path('app/db_backups/' . $filename);
        if (file_exists($path)) {
            unlink($path);
        }

        return back()->with('success', '🗑 Backup file deleted: ' . $filename);
    }

    /**
     * Send backup via email
     */
    public function emailBackup(Request $request)
    {
        $adminUser = Auth::user();
        if (!$adminUser || empty($adminUser->email)) {
            return back()->with('error', '❌ No verified admin email address associated with your session.');
        }

        // Enforce destination email strictly to authenticated administrator to prevent data exfiltration
        $email = $adminUser->email;

        // Use existing backup or create a new one
        $filename = $request->input('filename');
        if (!$filename || !file_exists(storage_path('app/db_backups/' . $filename))) {
            $filename = $this->generateBackup();
        }

        if (!$filename) {
            return back()->with('error', '❌ Failed to create backup for email.');
        }

        $filePath = storage_path('app/db_backups/' . $filename);
        $fileSize = number_format(filesize($filePath) / 1024, 1) . ' KB';

        try {
            Mail::send([], [], function ($message) use ($email, $filePath, $filename, $fileSize) {
                $message->to($email)
                    ->subject('🗄️ Guru Crackers - Database Backup: ' . now()->format('d M Y H:i'))
                    ->html(
                        '<div style="font-family: sans-serif; max-width: 600px; margin: 0 auto; background: #f8fafc; padding: 30px; border-radius: 12px;">
                            <div style="background: linear-gradient(135deg, #0f172a, #1e1b4b); padding: 24px; border-radius: 12px; text-align: center; margin-bottom: 24px;">
                                <div style="font-size: 36px; margin-bottom: 8px;">🎆</div>
                                <h1 style="color: white; margin: 0; font-size: 22px; font-weight: 900;">Guru Crackers Admin</h1>
                                <p style="color: #94a3b8; margin: 4px 0 0; font-size: 13px;">Database Backup Report</p>
                            </div>
                            <div style="background: white; padding: 20px; border-radius: 10px; border: 1px solid #e2e8f0; margin-bottom: 16px;">
                                <h2 style="color: #1e293b; font-size: 16px; margin-top: 0;">📦 Backup Details</h2>
                                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                                    <tr><td style="padding: 6px 0; color: #64748b; width: 40%;">File Name</td><td style="font-weight: bold; color: #1e293b;">' . $filename . '</td></tr>
                                    <tr><td style="padding: 6px 0; color: #64748b;">File Size</td><td style="font-weight: bold; color: #1e293b;">' . $fileSize . '</td></tr>
                                    <tr><td style="padding: 6px 0; color: #64748b;">Generated</td><td style="font-weight: bold; color: #1e293b;">' . now()->format('d M Y, h:i A') . '</td></tr>
                                    <tr><td style="padding: 6px 0; color: #64748b;">Admin</td><td style="font-weight: bold; color: #1e293b;">' . Auth::user()->name . '</td></tr>
                                </table>
                            </div>
                            <div style="background: #fef9c3; border: 1px solid #fde047; padding: 14px; border-radius: 10px; font-size: 12px; color: #713f12;">
                                ⚠️ <strong>Keep this file secure.</strong> It contains your complete database. Store it safely and delete after use.
                            </div>
                        </div>'
                    )
                    ->attach($filePath, ['as' => $filename, 'mime' => 'application/sql']);
            });

            $this->logDatabaseAction('email_backup', 'DB backup emailed to: ' . $email . ' | File: ' . $filename);
            return back()->with('success', '📧 Backup sent to <strong>' . $email . '</strong> successfully!');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Email failed: ' . $e->getMessage());
        }
    }

    /**
     * Clear specific tables (with mandatory backup first)
     */
    public function clearTables(Request $request)
    {
        $request->validate([
            'tables'           => 'required|array|min:1',
            'tables.*'         => 'required|string',
            'confirm_text'     => 'required|string',
            'backup_confirmed' => 'required|accepted',
        ]);

        if (strtoupper(trim($request->input('confirm_text'))) !== 'DELETE') {
            return back()->with('error', '❌ Please type DELETE (in caps) to confirm.');
        }

        // Validate tables - none can be protected
        $requestedTables = $request->input('tables', []);
        $blockedTables = array_intersect($requestedTables, self::PROTECTED_TABLES);
        if (!empty($blockedTables)) {
            return back()->with('error', '🛡️ Protected tables cannot be cleared: ' . implode(', ', $blockedTables));
        }

        // Verify all requested tables actually exist
        $existingTables = array_column(DB::select('SHOW TABLES'), array_key_first((array) DB::select('SHOW TABLES')[0]));
        $invalidTables = array_diff($requestedTables, $existingTables);
        if (!empty($invalidTables)) {
            return back()->with('error', '❌ Invalid tables: ' . implode(', ', $invalidTables));
        }

        // Create backup before clearing
        $backupFilename = $this->generateBackup();
        if (!$backupFilename) {
            return back()->with('error', '❌ Backup failed. Tables NOT cleared for safety.');
        }

        // Clear selected tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $cleared = [];
        $errors = [];

        foreach ($requestedTables as $table) {
            try {
                DB::table($table)->truncate();
                $cleared[] = $table;
            } catch (\Exception $e) {
                $errors[] = $table . ': ' . $e->getMessage();
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->logDatabaseAction(
            'clear_tables',
            'Tables cleared: [' . implode(', ', $cleared) . '] | Backup: ' . $backupFilename
        );

        $msg = '✅ Cleared ' . count($cleared) . ' table(s): <strong>' . implode(', ', $cleared) . '</strong><br>🔒 Backup saved: <strong>' . $backupFilename . '</strong>';
        if (!empty($errors)) {
            $msg .= '<br>⚠️ Some errors: ' . implode(', ', $errors);
        }

        return back()->with('success', $msg);
    }

    /**
     * Clear FULL database (all non-protected tables), backup first
     */
    public function clearAll(Request $request)
    {
        $request->validate([
            'confirm_text' => 'required|string',
        ]);

        if (strtoupper(trim($request->input('confirm_text'))) !== 'CLEAR ALL DATA') {
            return back()->with('error', '❌ Type exactly: CLEAR ALL DATA (to confirm)');
        }

        // Create backup first (mandatory)
        $backupFilename = $this->generateBackup();
        if (!$backupFilename) {
            return back()->with('error', '❌ Backup failed. Database NOT cleared for safety.');
        }

        // Get clearable tables (all tables minus protected ones)
        $allTables = $this->getAllTableNames();
        $clearableTables = array_diff($allTables, self::PROTECTED_TABLES);

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $cleared = [];

        foreach ($clearableTables as $table) {
            try {
                DB::table($table)->truncate();
                $cleared[] = $table;
            } catch (\Exception $e) {
                // Silently skip tables that can't be cleared
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->logDatabaseAction(
            'clear_all',
            'FULL DB CLEAR executed. Cleared ' . count($cleared) . ' tables. Backup: ' . $backupFilename
        );

        return back()->with('success',
            '🧹 <strong>' . count($cleared) . ' tables cleared!</strong><br>🔒 Full backup saved: <strong>' . $backupFilename . '</strong>'
        );
    }

    // ─────────────── PRIVATE HELPERS ───────────────

    private function generateBackup(): ?string
    {
        $backupDir = storage_path('app/db_backups');
        if (!is_dir($backupDir)) {
            if (!@mkdir($backupDir, 0775, true) && !is_dir($backupDir)) {
                \Log::error('DB Backup: Failed to create backup directory: ' . $backupDir);
                return null;
            }
        }
        @chmod($backupDir, 0775);

        // Ensure index blocker exists to prevent directory traversal
        $indexFile = $backupDir . '/index.php';
        if (!file_exists($indexFile)) {
            @file_put_contents($indexFile, "<?php http_response_code(403); exit('403 Forbidden');\n");
            @chmod($indexFile, 0644);
        }

        $filename = 'guru_crackers_backup_' . now()->format('Y_m_d_H_i_s') . '.sql';
        $filePath = $backupDir . '/' . $filename;

        // Pure PHP PDO backup engine — 100% resilient across Docker, TiDB Cloud with SSL, and all cloud platforms
        try {
            $handle = @fopen($filePath, 'w');
            if (!$handle) {
                \Log::error('DB Backup: Failed to open file for writing: ' . $filePath);
                return null;
            }

            $dbName = config('database.connections.mysql.database', 'guru_crackers');
            $host   = config('database.connections.mysql.host', 'localhost');

            fwrite($handle, "-- ============================================================\n");
            fwrite($handle, "-- GURU CRACKERS - FULL DATABASE BACKUP\n");
            fwrite($handle, "-- Generated: " . now()->format('Y-m-d H:i:s') . "\n");
            fwrite($handle, "-- Database: " . $dbName . "\n");
            fwrite($handle, "-- Host: " . $host . "\n");
            fwrite($handle, "-- ============================================================\n\n");
            fwrite($handle, "SET NAMES utf8mb4;\n");
            fwrite($handle, "SET FOREIGN_KEY_CHECKS = 0;\n");
            fwrite($handle, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n\n");

            $tables = $this->getAllTableNames();
            $pdo = DB::connection()->getPdo();

            foreach ($tables as $table) {
                fwrite($handle, "-- ------------------------------------------------------------\n");
                fwrite($handle, "-- Table structure for `{$table}`\n");
                fwrite($handle, "-- ------------------------------------------------------------\n");
                fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");

                $createRes = DB::select("SHOW CREATE TABLE `{$table}`");
                if (!empty($createRes)) {
                    $row = (array) $createRes[0];
                    $createSql = null;
                    foreach ($row as $k => $v) {
                        if (stripos($k, 'create') !== false) {
                            $createSql = $v;
                            break;
                        }
                    }
                    if ($createSql) {
                        fwrite($handle, $createSql . ";\n\n");
                    }
                }

                $batch = [];
                $columns = null;

                foreach (DB::table($table)->cursor() as $record) {
                    $recordArr = (array) $record;
                    if ($columns === null) {
                        $columns = array_keys($recordArr);
                    }

                    $values = [];
                    foreach ($recordArr as $val) {
                        if ($val === null) {
                            $values[] = 'NULL';
                        } elseif (is_int($val) || is_float($val)) {
                            $values[] = (string) $val;
                        } else {
                            $values[] = $pdo->quote((string) $val);
                        }
                    }
                    $batch[] = '(' . implode(', ', $values) . ')';

                    if (count($batch) >= 100) {
                        $colList = '`' . implode('`, `', $columns) . '`';
                        fwrite($handle, "INSERT INTO `{$table}` ({$colList}) VALUES\n" . implode(",\n", $batch) . ";\n\n");
                        $batch = [];
                    }
                }

                if (!empty($batch)) {
                    $colList = '`' . implode('`, `', $columns) . '`';
                    fwrite($handle, "INSERT INTO `{$table}` ({$colList}) VALUES\n" . implode(",\n", $batch) . ";\n\n");
                }
            }

            fwrite($handle, "SET FOREIGN_KEY_CHECKS = 1;\n");
            fwrite($handle, "-- End of backup.\n");
            fclose($handle);

            if (file_exists($filePath) && filesize($filePath) > 50) {
                @chmod($filePath, 0644);
                return $filename;
            }

            \Log::error('DB Backup: Result file is missing or too small (< 50 bytes): ' . $filePath);
            return null;
        } catch (\Throwable $e) {
            \Log::error('DB Backup Generation Exception: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            if (isset($handle) && is_resource($handle)) {
                @fclose($handle);
            }
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
            return null;
        }
    }

    private function getTableStats(): array
    {
        $tables = [];
        $allTableNames = $this->getAllTableNames();

        foreach ($allTableNames as $name) {
            $isProtected  = in_array($name, self::PROTECTED_TABLES);
            $isClearable  = !$isProtected && isset(self::TABLE_META[$name]);
            $count        = 0;

            try {
                $count = DB::table($name)->count();
            } catch (\Exception $e) {
            }

            $meta = self::TABLE_META[$name] ?? [
                'label' => '📄 ' . ucfirst(str_replace('_', ' ', $name)),
                'color' => 'slate',
                'desc'  => ucfirst(str_replace('_', ' ', $name)) . ' table',
            ];

            $tables[$name] = array_merge($meta, [
                'name'        => $name,
                'count'       => $count,
                'protected'   => $isProtected,
                'clearable'   => !$isProtected,
            ]);
        }

        return $tables;
    }

    private function getBackupFiles(): array
    {
        $backupDir = storage_path('app/db_backups');
        if (!is_dir($backupDir)) {
            return [];
        }

        $files = glob($backupDir . '/guru_crackers_backup_*.sql');
        $list = [];

        foreach ($files as $file) {
            $list[] = [
                'name'         => basename($file),
                'size'         => filesize($file),
                'size_display' => $this->formatBytes(filesize($file)),
                'modified'     => filemtime($file),
                'modified_fmt' => date('d M Y, h:i A', filemtime($file)),
            ];
        }

        // Sort newest first
        usort($list, fn($a, $b) => $b['modified'] - $a['modified']);

        return $list;
    }

    private function getAllTableNames(): array
    {
        $tables = DB::select('SHOW TABLES');
        if (empty($tables)) return [];
        $key = array_key_first((array) $tables[0]);
        return array_map(fn($t) => $t->$key, $tables);
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return number_format($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }

    private function logDatabaseAction(string $event, string $summary): void
    {
        try {
            DB::table('audit_logs')->insert([
                'user_id'      => Auth::id(),
                'user_name'    => Auth::user()?->name ?? 'Admin',
                'user_role'    => 'admin',
                'ip_address'   => request()->ip(),
                'user_agent'   => request()->userAgent(),
                'module'       => 'database',
                'event'        => $event,
                'summary'      => $summary,
                'url'          => request()->fullUrl(),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        } catch (\Exception $e) {
            // Silently ignore if audit_logs table doesn't exist
        }
    }
}
