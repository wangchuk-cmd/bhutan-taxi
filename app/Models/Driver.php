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
        'fuel_type',
        'verified',
        'active',
        'average_rating',
        'rating_count',
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

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Get average rating for this driver
     */
    public function getAverageRating()
    {
        return $this->ratings()->avg('rating') ?? 0;
    }

    /**
     * Get total number of ratings for this driver
     */
    public function getRatingCount()
    {
        return $this->ratings()->count();
    }

    /**
     * Update cached rating stats (call after new rating is added)
     */
    public function updateRatingStats()
    {
        $this->average_rating = $this->ratings()->avg('rating') ?? 0;
        $this->rating_count = $this->ratings()->count();
        $this->save();
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
