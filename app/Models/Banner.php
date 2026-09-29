<?php

namespace App\Models;

use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use Auditable;

    protected $fillable = [
        'title',
        'image',
        'mobile_image',
        'link',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the desktop banner image public URL.
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->image)) {
            return '';
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        if (Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }

        return asset($this->image);
    }

    /**
     * Get the mobile banner image public URL (falls back to desktop banner if not set).
     */
    public function getMobileImageUrlAttribute(): string
    {
        if (empty($this->mobile_image)) {
            return $this->image_url;
        }

        if (str_starts_with($this->mobile_image, 'http://') || str_starts_with($this->mobile_image, 'https://')) {
            return $this->mobile_image;
        }

        if (Storage::disk('public')->exists($this->mobile_image)) {
            return asset('storage/' . $this->mobile_image);
        }

        return asset($this->mobile_image);
    }

    /**
     * Get a sanitized, safe link that prevents javascript: or data: XSS payloads.
     */
    public function getSafeLinkAttribute(): string
    {
        if (empty($this->link)) {
            return '#categories';
        }
        $trimmed = trim($this->link);
        if (preg_match('/^\s*(?:javascript|data|vbscript):/i', $trimmed)) {
            return '#categories';
        }
        if (preg_match('/^(?:https?:\/\/|\/|#)/i', $trimmed)) {
            return $trimmed;
        }
        return '#categories';
    }
}
