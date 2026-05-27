<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundRequest extends Model
{
    protected $fillable = [
        'booking_id',
        'passenger_id',
        'payment_id',
        'transaction_id',
        'amount',
        'reason',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function passenger()
    {
        return $this->belongsTo(User::class, 'passenger_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['pending', 'under_review']);
    }
}