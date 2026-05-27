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
        'data',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public static function send($userId, $type, $message, $title = null, array $data = [])
    {
        $titles = [
            'payment' => 'Payment Confirmation',
            'booking' => 'Booking Update',
            'cancellation' => 'Booking Cancelled',
            'refund_request' => 'Refund Request',
            'refund_review' => 'Refund Review',
            'refund_rejected' => 'Refund Rejected',
            'refund_approved' => 'Refund Approved',
            'payout' => 'Payout Update',
            'admin' => 'Admin Notice',
            'system' => 'System Notification',
        ];

        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title ?? ($titles[$type] ?? ucfirst($type)),
            'message' => $message,
            'data' => $data ?: null,
        ]);
    }

    public function targetUrl(?User $viewer = null): ?string
    {
        if (!empty($this->data['url'])) {
            return $this->data['url'];
        }

        $role = $viewer?->role ?? $this->user?->role;

        if (in_array($this->type, ['refund_request', 'refund_review', 'refund_approved', 'refund_rejected'])) {
            return $role === 'admin' ? route('admin.refunds') : route('bookings.my');
        }

        if (preg_match('/booking #([0-9]+)/i', $this->message, $matches)) {
            $bookingId = (int) $matches[1];

            if (in_array($this->type, ['booking', 'payment', 'cancellation'])) {
                return $role === 'admin'
                    ? route('admin.bookings.show', $bookingId)
                    : route('bookings.show', $bookingId);
            }
        }

        return match ($this->type) {
            'booking', 'payment', 'cancellation' => $role === 'admin' ? route('admin.bookings') : route('bookings.my'),
            'refund_request', 'refund_review', 'refund_approved', 'refund_rejected' => $role === 'admin' ? route('admin.refunds') : route('bookings.my'),
            'payout' => $role === 'driver' ? route('driver.payouts') : route('admin.payouts'),
            'admin', 'alert' => $role === 'admin' ? route('admin.dashboard') : route('home'),
            default => null,
        };
    }
}
