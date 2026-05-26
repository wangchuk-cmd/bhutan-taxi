<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = [
        'booking_id',
        'driver_id',
        'passenger_id',
        'rating',
        'review',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    protected static function booted(): void
    {
        // Update driver's cached ratings and clear trip cache when a rating is created or updated
        static::created(function ($rating) {
            $rating->driver->updateRatingStats();
            // Clear trip cache so rating updates show immediately
            if ($rating->booking && $rating->booking->trip) {
                \Illuminate\Support\Facades\Cache::forget('trip_' . $rating->booking->trip_id);
            }
        });

        static::updated(function ($rating) {
            $rating->driver->updateRatingStats();
            // Clear trip cache
            if ($rating->booking && $rating->booking->trip) {
                \Illuminate\Support\Facades\Cache::forget('trip_' . $rating->booking->trip_id);
            }
        });

        static::deleted(function ($rating) {
            $rating->driver->updateRatingStats();
            // Clear trip cache
            if ($rating->booking && $rating->booking->trip) {
                \Illuminate\Support\Facades\Cache::forget('trip_' . $rating->booking->trip_id);
            }
        });
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function passenger()
    {
        return $this->belongsTo(User::class, 'passenger_id');
    }

    /**
     * Scope to get ratings for a specific driver
     */
    public function scopeForDriver($query, $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    /**
     * Scope to get ratings from a specific passenger
     */
    public function scopeFromPassenger($query, $passengerId)
    {
        return $query->where('passenger_id', $passengerId);
    }

    /**
     * Get average rating for a driver
     */
    public static function getDriverAverageRating($driverId)
    {
        return self::forDriver($driverId)->avg('rating');
    }

    /**
     * Get rating count for a driver
     */
    public static function getDriverRatingCount($driverId)
    {
        return self::forDriver($driverId)->count();
    }
}
