<?php

namespace App\Models;

use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Shop extends Model
{
    use Auditable;

    protected $fillable = [
        'name',
        'tagline',
        'email',
        'phone',
        'secondary_phone',
        'whatsapp_phone',
        'website',
        'logo',
        'address',
        'city',
        'pincode',
        'offer',
        'offer_percentage',
        'min_order_amount',
        'banner_notice',
        'upi_id',
        'upi_name',
        'upi_qr_image',
        'bank_details',
        'facebook_url',
        'instagram_url',
        'youtube_url',
        'maps_url',
        'is_active',
    ];

    protected $casts = [
        'offer_percentage' => 'integer',
        'min_order_amount' => 'float',
        'is_active' => 'boolean',
    ];

    protected static ?Shop $cachedCurrent = null;

    /**
     * Get the active or default shop instance (cached per request).
     */
    public static function current(): self
    {
        if (static::$cachedCurrent !== null) {
            return static::$cachedCurrent;
        }

        return static::$cachedCurrent = (static::first() ?? static::create([
            'name' => 'Guru Crackers',
            'tagline' => 'Sivakasi Direct Wholesale & Retail Crackers',
            'email' => 'contact@gurucrackers.com',
            'phone' => '+91 9789874381',
            'secondary_phone' => null,
            'whatsapp_phone' => '+91 9789874381',
            'website' => 'https://gurucrackers.com',
            'address' => '3/324B Naduvapatti,H.P petrol bunk NH7, SATTUR',
            'city' => 'Sivakasi',
            'pincode' => '626123',
            'offer' => '💥 DIWALI 2026 SPECIAL OFFER | தீபாவளி மெகா தள்ளுபடி',
            'offer_percentage' => 90,
            'min_order_amount' => 2500.00,
            'banner_notice' => '✨ Sivakasi Direct Factory Prices | 100% Genuine Green Crackers | Mega Festival Discount',
            'upi_id' => '9789874381@apl',
            'upi_name' => 'Guru Crackers',
            'is_active' => true,
        ]));
    }

    public function getMinOrderAmount(): float
    {
        return (float) ($this->min_order_amount > 0 ? $this->min_order_amount : 2500.00);
    }

    public static function clearCache(): void
    {
        static::$cachedCurrent = null;
    }

    /**
     * Get logo image public URL.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo && Storage::disk('public')->exists($this->logo)) {
            return asset('storage/' . $this->logo);
        }
        return null;
    }

    /**
     * Get absolute filesystem path to the logo.
     */
    public function getLogoPathAttribute(): ?string
    {
        if (!$this->logo) {
            return null;
        }

        if (Storage::disk('public')->exists($this->logo)) {
            return Storage::disk('public')->path($this->logo);
        }

        $publicStoragePath = public_path('storage/' . $this->logo);
        if (file_exists($publicStoragePath)) {
            return $publicStoragePath;
        }

        return null;
    }

    /**
     * Get logo encoded as a base64 Data URI (safe and reliable for DomPDF).
     */
    public function getLogoBase64Attribute(): ?string
    {
        $path = $this->logo_path;
        if ($path && file_exists($path)) {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $mime = match ($ext) {
                'png' => 'image/png',
                'jpg', 'jpeg' => 'image/jpeg',
                'webp' => 'image/webp',
                'svg' => 'image/svg+xml',
                default => 'image/png',
            };
            $content = @file_get_contents($path);
            if ($content !== false) {
                return 'data:' . $mime . ';base64,' . base64_encode($content);
            }
        }
        return null;
    }

    /**
     * Get custom UPI QR code image URL.
     */
    public function getUpiQrImageUrlAttribute(): ?string
    {
        if ($this->upi_qr_image && Storage::disk('public')->exists($this->upi_qr_image)) {
            return asset('storage/' . $this->upi_qr_image);
        }
        return null;
    }

    /**
     * Generate standard UPI payment URI deep link.
     */
    public function getUpiPaymentUrl(?float $amount = null, ?string $orderNumber = null): ?string
    {
        if (empty($this->upi_id)) {
            return null;
        }

        $pa = $this->upi_id;
        $pn = rawurlencode($this->upi_name ?: $this->name);
        $url = "upi://pay?pa={$pa}&pn={$pn}&cu=INR";

        if ($amount !== null && $amount > 0) {
            $url .= '&am=' . number_format($amount, 2, '.', '');
        }

        if (!empty($orderNumber)) {
            $url .= '&tn=' . rawurlencode('Order ' . $orderNumber);
        }

        return $url;
    }

    /**
     * Decrypt bank details when accessed.
     *
     * @param string|null $value
     */
    public function getBankDetailsAttribute(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            return \Illuminate\Support\Facades\Crypt::decryptString($value);
        } catch (\Throwable $e) {
            // Backward compatibility fallback if stored as plaintext
            return $value;
        }
    }

    /**
     * Encrypt bank details before storing in database (AES-256-CBC).
     *
     * @param string|null $value
     */
    public function setBankDetailsAttribute(?string $value): void
    {
        $this->attributes['bank_details'] = !empty($value)
            ? \Illuminate\Support\Facades\Crypt::encryptString(trim($value))
            : null;
    }
}

