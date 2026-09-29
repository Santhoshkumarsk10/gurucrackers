<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppMessage extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_messages';

    protected $fillable = [
        'order_id',
        'message_id',
        'remote_jid',
        'phone',
        'from_me',
        'sender_name',
        'message_type',
        'message_text',
        'media_url',
        'media_filename',
        'status',
        'trigger_source',
        'error_message',
        'is_read',
        'sent_at',
    ];

    protected $casts = [
        'from_me' => 'boolean',
        'is_read' => 'boolean',
        'sent_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Clean phone number to 10 digits if possible.
     */
    public static function cleanPhone(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($digits) > 10) {
            return substr($digits, -10);
        }
        return $digits;
    }
}
