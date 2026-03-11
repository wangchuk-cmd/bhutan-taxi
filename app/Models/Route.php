<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $fillable = [
        'origin_dzongkhag',
        'destination_dzongkhag',
        'distance_km',
        'estimated_time',
    ];

    protected $casts = [
        'distance_km' => 'float',
    ];

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function getRouteNameAttribute()
    {
        return $this->origin_dzongkhag . ' → ' . $this->destination_dzongkhag;
    }
}
