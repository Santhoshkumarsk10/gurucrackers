<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'module',
        'event',
        'auditable_type',
        'auditable_id',
        'record_name',
        'summary',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to search audit logs.
     */
    public function scopeSearch($query, $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('summary', 'like', "%{$term}%")
              ->orWhere('record_name', 'like', "%{$term}%")
              ->orWhere('user_name', 'like', "%{$term}%")
              ->orWhere('ip_address', 'like', "%{$term}%")
              ->orWhere('auditable_id', 'like', "%{$term}%");
        });
    }

    /**
     * Scope a query to filter by module.
     */
    public function scopeModule($query, $module)
    {
        if (!empty($module) && $module !== 'all') {
            return $query->where('module', $module);
        }
        return $query;
    }

    /**
     * Scope a query to filter by event type.
     */
    public function scopeEvent($query, $event)
    {
        if (!empty($event) && $event !== 'all') {
            return $query->where('event', $event);
        }
        return $query;
    }

    /**
     * Scope a query by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        if (!empty($startDate)) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if (!empty($endDate)) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        return $query;
    }

    /**
     * Get CSS badge color and icon for event type.
     */
    public function getEventBadgeAttribute(): array
    {
        return match ($this->event) {
            'created' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'icon' => 'fa-plus-circle', 'label' => 'Created'],
            'updated' => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200', 'icon' => 'fa-pen-to-square', 'label' => 'Updated'],
            'deleted' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'icon' => 'fa-trash-can', 'label' => 'Deleted'],
            'restored' => ['bg' => 'bg-teal-50 text-teal-700 border-teal-200', 'icon' => 'fa-rotate-left', 'label' => 'Restored'],
            'status_change' => ['bg' => 'bg-amber-50 text-amber-800 border-amber-200', 'icon' => 'fa-arrows-rotate', 'label' => 'Status Change'],
            'dispatched' => ['bg' => 'bg-purple-50 text-purple-700 border-purple-200', 'icon' => 'fa-truck-fast', 'label' => 'Dispatched'],
            'login' => ['bg' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'icon' => 'fa-right-to-bracket', 'label' => 'Admin Login'],
            'logout' => ['bg' => 'bg-slate-100 text-slate-700 border-slate-200', 'icon' => 'fa-right-from-bracket', 'label' => 'Logout'],
            'password_change' => ['bg' => 'bg-orange-50 text-orange-800 border-orange-200', 'icon' => 'fa-key', 'label' => 'Password Change'],
            'bulk_upload' => ['bg' => 'bg-cyan-50 text-cyan-800 border-cyan-200', 'icon' => 'fa-file-csv', 'label' => 'Bulk Upload'],
            'export' => ['bg' => 'bg-lime-50 text-lime-800 border-lime-200', 'icon' => 'fa-file-export', 'label' => 'Data Export'],
            'whatsapp_sent' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'icon' => 'fa-brands fa-whatsapp', 'label' => 'WhatsApp Sent'],
            default => ['bg' => 'bg-slate-100 text-slate-700 border-slate-200', 'icon' => 'fa-circle-info', 'label' => ucfirst($this->event ?? 'Action')],
        };
    }

    /**
     * Get Module Icon & Color.
     */
    public function getModuleBadgeAttribute(): array
    {
        return match ($this->module) {
            'orders' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'icon' => 'fa-boxes-stacked', 'label' => 'Orders'],
            'products' => ['bg' => 'bg-amber-50 text-amber-800 border-amber-200', 'icon' => 'fa-box-open', 'label' => 'Products'],
            'categories' => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200', 'icon' => 'fa-tags', 'label' => 'Categories'],
            'shop' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'icon' => 'fa-store', 'label' => 'Shop Settings'],
            'banners' => ['bg' => 'bg-fuchsia-50 text-fuchsia-700 border-fuchsia-200', 'icon' => 'fa-images', 'label' => 'Banners'],
            'whatsapp' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'icon' => 'fa-brands fa-whatsapp', 'label' => 'WhatsApp'],
            'auth' => ['bg' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'icon' => 'fa-user-shield', 'label' => 'Security / Auth'],
            'reports' => ['bg' => 'bg-violet-50 text-violet-700 border-violet-200', 'icon' => 'fa-chart-line', 'label' => 'Reports & Analytics'],
            'bulk_messaging' => ['bg' => 'bg-teal-50 text-teal-700 border-teal-200', 'icon' => 'fa-paper-plane', 'label' => 'Bulk Messaging'],
            default => ['bg' => 'bg-slate-100 text-slate-700 border-slate-200', 'icon' => 'fa-cube', 'label' => ucfirst($this->module ?? 'System')],
        };
    }
}
