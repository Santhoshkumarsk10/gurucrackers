<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /**
     * Sensitive fields that should never be stored in plaintext diffs.
     */
    protected static array $hiddenAttributes = [
        'password',
        'remember_token',
        'otp',
        'api_token',
        'secret',
        'app_key',
    ];

    /**
     * Create an audit log record safely.
     */
    public static function log(
        string $event,
        string $module,
        string $summary,
        ?Model $record = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $recordName = null
    ): ?AuditLog {
        try {
            $user = Auth::user();

            $userName = null;
            $userRole = 'admin';

            if ($user) {
                $userName = $user->name . ' (' . $user->email . ')';
                $userRole = 'admin';
            } elseif (Request::is('api/*') || Request::is('webhook/*')) {
                $userName = 'System Webhook';
                $userRole = 'system';
            } else {
                $userName = 'Public Customer / Guest';
                $userRole = 'customer';
            }

            // Derive record name if not explicitly passed
            if (!$recordName && $record) {
                $recordName = self::resolveRecordName($record);
            }

            // Filter out sensitive data from old/new values
            $cleanOld = $oldValues ? self::sanitizeValues($oldValues) : null;
            $cleanNew = $newValues ? self::sanitizeValues($newValues) : null;

            return AuditLog::create([
                'user_id' => $user?->id,
                'user_name' => $userName,
                'user_role' => $userRole,
                'module' => $module,
                'event' => $event,
                'auditable_type' => $record ? get_class($record) : null,
                'auditable_id' => $record ? $record->getKey() : null,
                'record_name' => $recordName,
                'summary' => $summary,
                'old_values' => $cleanOld,
                'new_values' => $cleanNew,
                'ip_address' => Request::ip() ?? '127.0.0.1',
                'user_agent' => Request::userAgent() ?? 'CLI / Internal',
                'url' => Request::fullUrl() ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to write audit log: ' . $e->getMessage(), [
                'event' => $event,
                'module' => $module,
                'summary' => $summary,
                'exception' => $e,
            ]);
            return null;
        }
    }

    /**
     * Resolve a readable record name based on the model instance.
     */
    protected static function resolveRecordName(Model $record): string
    {
        $className = class_basename($record);

        if (isset($record->order_number)) {
            return "Order #{$record->order_number} ({$record->customer_name})";
        }

        if (isset($record->name)) {
            return "{$className}: {$record->name}";
        }

        if (isset($record->title)) {
            return "{$className}: {$record->title}";
        }

        return "{$className} #{$record->getKey()}";
    }

    /**
     * Remove sensitive keys from attributes.
     */
    protected static function sanitizeValues(array $values): array
    {
        foreach (self::$hiddenAttributes as $key) {
            if (array_key_exists($key, $values)) {
                $values[$key] = '********';
            }
        }
        return $values;
    }

    /**
     * Convenience logger for Orders.
     */
    public static function logOrder(Model $order, string $event, string $summary, ?array $oldValues = null, ?array $newValues = null): ?AuditLog
    {
        return self::log(
            event: $event,
            module: 'orders',
            summary: $summary,
            record: $order,
            oldValues: $oldValues,
            newValues: $newValues
        );
    }

    /**
     * Convenience logger for Products.
     */
    public static function logProduct(Model $product, string $event, string $summary, ?array $oldValues = null, ?array $newValues = null): ?AuditLog
    {
        return self::log(
            event: $event,
            module: 'products',
            summary: $summary,
            record: $product,
            oldValues: $oldValues,
            newValues: $newValues
        );
    }

    /**
     * Convenience logger for Categories.
     */
    public static function logCategory(Model $category, string $event, string $summary, ?array $oldValues = null, ?array $newValues = null): ?AuditLog
    {
        return self::log(
            event: $event,
            module: 'categories',
            summary: $summary,
            record: $category,
            oldValues: $oldValues,
            newValues: $newValues
        );
    }

    /**
     * Convenience logger for Shop Settings.
     */
    public static function logShop(Model $shop, string $event, string $summary, ?array $oldValues = null, ?array $newValues = null): ?AuditLog
    {
        return self::log(
            event: $event,
            module: 'shop',
            summary: $summary,
            record: $shop,
            oldValues: $oldValues,
            newValues: $newValues
        );
    }

    /**
     * Convenience logger for Banners.
     */
    public static function logBanner(Model $banner, string $event, string $summary, ?array $oldValues = null, ?array $newValues = null): ?AuditLog
    {
        return self::log(
            event: $event,
            module: 'banners',
            summary: $summary,
            record: $banner,
            oldValues: $oldValues,
            newValues: $newValues
        );
    }

    /**
     * Convenience logger for Auth & Security events.
     */
    public static function logAuth(string $event, string $summary, ?Model $user = null): ?AuditLog
    {
        return self::log(
            event: $event,
            module: 'auth',
            summary: $summary,
            record: $user
        );
    }

    /**
     * Convenience logger for Exports & Reports.
     */
    public static function logExport(string $module, string $format, string $summary): ?AuditLog
    {
        return self::log(
            event: 'export',
            module: $module,
            summary: $summary . " (Format: {$format})",
            recordName: strtoupper($format) . ' Export'
        );
    }
}
