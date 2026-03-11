<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public static function send($userId, $type, $message, $title = null)
    {
        $titles = [
            'payment' => 'Payment Confirmation',
            'booking' => 'Booking Update',
            'cancellation' => 'Booking Cancelled',
            'payout' => 'Payout Update',
            'admin' => 'Admin Notice',
            'system' => 'System Notification',
        ];

        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title ?? ($titles[$type] ?? ucfirst($type)),
            'message' => $message,
        ]);
    }
}
