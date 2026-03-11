<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = [
        'user_id',
        'license_number',
        'taxi_plate_number',
        'vehicle_type',
        'verified',
        'active',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function payouts()
    {
        return $this->hasMany(Payout::class);
    }

    public function scopeVerified($query)
    {
        return $query->where('verified', true);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
