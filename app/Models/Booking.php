<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'trip_id',
        'passenger_id',
        'passengers_info',
        'booking_type',
        'seats_booked',
        'payment_status',
        'payment_time',
        'booking_time',
        'cancellation_time',
        'refund_status',
        'status',
    ];

    protected $casts = [
        'passengers_info' => 'array',
        'payment_time' => 'datetime',
        'booking_time' => 'datetime',
        'cancellation_time' => 'datetime',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function passenger()
    {
        return $this->belongsTo(User::class, 'passenger_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Get the primary passenger name from passengers_info
     */
    public function getPrimaryPassengerName()
    {
        if (is_array($this->passengers_info) && isset($this->passengers_info[0]['name'])) {
            return $this->passengers_info[0]['name'];
        }
        return $this->passenger->name ?? 'N/A';
    }

    /**
     * Get the primary passenger phone from passengers_info
     */
    public function getPrimaryPassengerPhone()
    {
        if (is_array($this->passengers_info) && isset($this->passengers_info[0]['phone'])) {
            return $this->passengers_info[0]['phone'];
        }
        return $this->passenger->phone_number ?? 'N/A';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function canCancel()
    {
        return $this->status === 'active' 
            && $this->trip->departure_datetime > now()->addHours(24);
    }

    public function getTotalAmountAttribute()
    {
        if ($this->booking_type === 'full') {
            return $this->trip->full_taxi_price;
        }
        return $this->trip->price_per_seat * $this->seats_booked;
    }
}
