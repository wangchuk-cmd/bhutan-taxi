<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = [
        'user_id',
        'license_number',
        'taxi_plate_number',
        'date_of_birth',
        'vehicle_type',
        'fuel_type',
        'years_of_experience',
        'show_experience_to_public',
        'show_age_range_to_public',
        'public_age_range',
        'verified',
        'active',
        'average_rating',
        'rating_count',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'years_of_experience' => 'integer',
        'show_experience_to_public' => 'boolean',
        'show_age_range_to_public' => 'boolean',
        'verified' => 'boolean',
        'active' => 'boolean',
    ];

    public function getAgeAttribute()
    {
        return $this->date_of_birth ? Carbon::parse($this->date_of_birth)->age : null;
    }

    public function getAgeRangeAttribute()
    {
        if (!empty($this->attributes['public_age_range'])) {
            return $this->attributes['public_age_range'];
        }

        if (!$this->age) {
            return null;
        }

        if ($this->age < 25) {
            return 'Under 25';
        }
        if ($this->age <= 34) {
            return '25-34';
        }
        if ($this->age <= 44) {
            return '35-44';
        }
        if ($this->age <= 54) {
            return '45-54';
        }

        return '55+';
    }

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
