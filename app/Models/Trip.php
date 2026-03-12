<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Trip extends Model
{
    protected $fillable = [
        'driver_id',
        'route_id',
        'origin_dzongkhag',
        'destination_dzongkhag',
        'departure_datetime',
        'total_seats',
        'available_seats',
        'price_per_seat',
        'full_taxi_price',
        'status',
    ];

    protected $casts = [
        'departure_datetime' => 'datetime',
        'price_per_seat' => 'decimal:2',
        'full_taxi_price' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        // Clear relevant caches when trip is created/updated/deleted
        static::created(function ($trip) {
            static::invalidateCache($trip);
        });

        static::updated(function ($trip) {
            static::invalidateCache($trip);
            // Clear specific trip cache
            Cache::forget('trip_' . $trip->id);
        });

        static::deleted(function ($trip) {
            static::invalidateCache($trip);
            Cache::forget('trip_' . $trip->id);
        });
    }

    public static function invalidateCache($trip): void
    {
        // Clear featured trips cache
        Cache::flush();
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function payout()
    {
        return $this->hasOne(Payout::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('departure_datetime', '>', now());
    }

    public function hasAvailableSeats($count = 1)
    {
        return $this->available_seats >= $count;
    }
}
