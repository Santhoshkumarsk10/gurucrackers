<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('whatsapp:restore-session {--force : Force overwrite existing files}', function () {
    $this->info('Restoring WhatsApp session from MySQL database...');
    $res = \App\Services\WhatsAppOrderService::restoreSessionFromDatabase((bool) $this->option('force'));
    if ($res['success']) {
        if (!empty($res['restored'])) {
            $this->info("Successfully restored {$res['files_count']} WhatsApp auth files from database.");
        } else {
            $this->line($res['message'] ?? 'Session files already exist on disk.');
        }
    } else {
        $this->warn($res['message'] ?? 'Could not restore WhatsApp session from database.');
    }
})->purpose('Restore WhatsApp auth session files from database to auth_info');

Artisan::command('whatsapp:backup-session {phone? : Connected phone number}', function ($phone = null) {
    $this->info('Backing up WhatsApp session from disk to MySQL database...');
    $res = \App\Services\WhatsAppOrderService::backupSessionToDatabase($phone);
    if ($res['success']) {
        $this->info("Successfully backed up {$res['files_count']} files ({$res['size_kb']} KB) to database.");
    } else {
        $this->error($res['message'] ?? $res['error'] ?? 'Backup failed.');
    }
})->purpose('Backup WhatsApp auth session files from disk to MySQL database');

Artisan::command('whatsapp:clear-session', function () {
    $this->info('Clearing stored WhatsApp session from MySQL database...');
    \App\Services\WhatsAppOrderService::clearStoredSession();
    $this->info('Stored WhatsApp session cleared.');
})->purpose('Delete stored WhatsApp session from MySQL database');
