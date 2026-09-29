<?php

namespace App\Models;

use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'category_id',
        'name',
        'tamil_name',
        'unit',
        'image',
        'actual_rate',
        'net_rate',
        'discount_percent',
        'is_active',
    ];

    protected $casts = [
        'actual_rate' => 'decimal:2',
        'net_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'image_url',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }
        return null;
    }
}

