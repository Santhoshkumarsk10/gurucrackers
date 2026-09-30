<?php

namespace App\Models;

use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use Auditable;

    protected $fillable = [
        'order_number',
        'name',
        'phone1',
        'phone2',
        'delivery_address',
        'city',
        'state',
        'pincode',
        'total_amount',
        'status',
        'payment_status',
        'payment_notes',
        'parcel_service_name',
        'lr_number',
        'parcel_count',
        'dispatch_date',
        'transport_phone',
        'destination_hub',
        'lr_receipt_image',
        'dispatch_notes',
        'dispatched_at',
        'admin_read_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'dispatch_date' => 'date',
        'dispatched_at' => 'datetime',
        'admin_read_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return in_array($this->status, ['confirmed', 'packed', 'dispatched']);
    }

    public function isPacked(): bool
    {
        return in_array($this->status, ['packed', 'dispatched']);
    }

    public function isDispatched(): bool
    {
        return $this->status === 'dispatched' || !empty($this->lr_number) || !is_null($this->dispatched_at);
    }

    public function getLrReceiptUrlAttribute(): ?string
    {
        if (empty($this->lr_receipt_image)) {
            return null;
        }

        if (str_starts_with($this->lr_receipt_image, 'http://') || str_starts_with($this->lr_receipt_image, 'https://')) {
            return $this->lr_receipt_image;
        }

        return asset('storage/' . $this->lr_receipt_image);
    }

    public function getTotalQuantityAttribute(): int
    {
        return (int) $this->items->sum('quantity');
    }

    public function getInvoiceSignature(): string
    {
        return substr(hash_hmac('sha256', $this->order_number . '|' . ($this->phone1 ?? ''), config('app.key')), 0, 32);
    }

    public function verifyInvoiceSignature(?string $token): bool
    {
        if (empty($token) || !is_string($token)) {
            return false;
        }

        if (hash_equals($this->getInvoiceSignature(), $token)) {
            return true;
        }

        // Backward compatibility for existing 12-character legacy links
        $legacy = substr(hash_hmac('sha256', $this->order_number . '|' . ($this->phone1 ?? ''), config('app.key')), 0, 12);
        return hash_equals($legacy, $token);
    }
}
