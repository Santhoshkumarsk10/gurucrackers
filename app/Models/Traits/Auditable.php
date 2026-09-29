<?php

namespace App\Models\Traits;

use App\Services\AuditLogger;

trait Auditable
{
    public static bool $auditDisabled = false;

    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            if (self::$auditDisabled) {
                return;
            }

            $module = self::resolveAuditModule($model);
            $recordName = self::resolveAuditRecordName($model);
            $summary = "Created new {$recordName}";

            AuditLogger::log(
                event: 'created',
                module: $module,
                summary: $summary,
                record: $model,
                newValues: $model->attributesToArray()
            );
        });

        static::updated(function ($model) {
            if (self::$auditDisabled) {
                return;
            }

            $changes = $model->getChanges();
            unset($changes['updated_at']);

            if (empty($changes)) {
                return;
            }

            $oldValues = [];
            $newValues = [];
            $changedFieldNames = [];

            foreach ($changes as $key => $newVal) {
                $oldVal = $model->getOriginal($key);
                $oldValues[$key] = $oldVal;
                $newValues[$key] = $newVal;

                $shortOld = is_string($oldVal) ? (strlen($oldVal) > 30 ? substr($oldVal, 0, 27) . '...' : $oldVal) : json_encode($oldVal);
                $shortNew = is_string($newVal) ? (strlen($newVal) > 30 ? substr($newVal, 0, 27) . '...' : $newVal) : json_encode($newVal);
                $changedFieldNames[] = "{$key}: {$shortOld} → {$shortNew}";
            }

            $module = self::resolveAuditModule($model);
            $recordName = self::resolveAuditRecordName($model);
            $diffSummary = implode(', ', array_slice($changedFieldNames, 0, 2));
            if (count($changedFieldNames) > 2) {
                $diffSummary .= ' and ' . (count($changedFieldNames) - 2) . ' more';
            }

            // Determine if event is a specialized update like status_change or dispatched
            $event = 'updated';
            if (array_key_exists('payment_status', $changes)) {
                $event = 'status_change';
            } elseif (array_key_exists('lr_number', $changes) || array_key_exists('dispatched_at', $changes)) {
                $event = 'dispatched';
            }

            $summary = "Updated {$recordName} ({$diffSummary})";

            AuditLogger::log(
                event: $event,
                module: $module,
                summary: $summary,
                record: $model,
                oldValues: $oldValues,
                newValues: $newValues
            );
        });

        static::deleted(function ($model) {
            if (self::$auditDisabled) {
                return;
            }

            $module = self::resolveAuditModule($model);
            $recordName = self::resolveAuditRecordName($model);
            $isForce = method_exists($model, 'isForceDeleting') && $model->isForceDeleting();
            $event = 'deleted';
            $summary = ($isForce ? "Permanently deleted " : "Moved to trash ") . $recordName;

            AuditLogger::log(
                event: $event,
                module: $module,
                summary: $summary,
                record: $model,
                oldValues: $model->attributesToArray()
            );
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                if (self::$auditDisabled) {
                    return;
                }

                $module = self::resolveAuditModule($model);
                $recordName = self::resolveAuditRecordName($model);
                $summary = "Restored {$recordName} from trash";

                AuditLogger::log(
                    event: 'restored',
                    module: $module,
                    summary: $summary,
                    record: $model,
                    newValues: $model->attributesToArray()
                );
            });
        }
    }

    protected static function resolveAuditModule($model): string
    {
        return match (class_basename($model)) {
            'Order' => 'orders',
            'OrderItem' => 'orders',
            'Product' => 'products',
            'Category' => 'categories',
            'Shop' => 'shop',
            'Banner' => 'banners',
            'User' => 'auth',
            default => strtolower(class_basename($model)),
        };
    }

    protected static function resolveAuditRecordName($model): string
    {
        $base = class_basename($model);
        if (isset($model->order_number)) {
            return "Order #{$model->order_number}" . (isset($model->name) ? " ({$model->name})" : '');
        }
        if (isset($model->name)) {
            return "{$base}: {$model->name}";
        }
        if (isset($model->title)) {
            return "{$base}: {$model->title}";
        }
        return "{$base} #{$model->getKey()}";
    }
}
