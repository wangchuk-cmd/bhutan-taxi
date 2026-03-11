<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $fillable = [
        'driver_id',
        'trip_id',
        'total_amount',
        'service_charge',
        'payout_amount',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'payout_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public static function calculateServiceCharge($amount, $rate = null)
    {
        if ($rate === null) {
            $rate = Setting::getServiceChargeRate();
        }
        return $amount * $rate;
    }
}
