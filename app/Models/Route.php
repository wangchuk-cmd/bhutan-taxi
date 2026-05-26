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

    public function scopeBetweenDzongkhags($query, string $first, string $second)
    {
        return $query->where(function ($innerQuery) use ($first, $second) {
            $innerQuery
                ->where(function ($q) use ($first, $second) {
                    $q->where('origin_dzongkhag', $first)
                        ->where('destination_dzongkhag', $second);
                })
                ->orWhere(function ($q) use ($first, $second) {
                    $q->where('origin_dzongkhag', $second)
                        ->where('destination_dzongkhag', $first);
                });
        });
    }

    public static function findBetweenDzongkhags(string $first, string $second): ?self
    {
        return static::query()
            ->betweenDzongkhags($first, $second)
            ->orderBy('id')
            ->first();
    }

    protected static function parseEstimatedTimeToMinutes($value): ?int
    {
        $time = trim((string) $value);
        if ($time === '') {
            return null;
        }

        if (preg_match('/(\d{1,2})\s*:\s*(\d{1,2})/', $time, $matches)) {
            $hours = (int) $matches[1];
            $minutes = (int) $matches[2];
            return ($hours * 60) + $minutes;
        }

        $hours = 0;
        $minutes = 0;

        if (preg_match('/(\d+)\s*(h|hr|hrs|hour|hours)/i', $time, $hourMatch)) {
            $hours = (int) $hourMatch[1];
        }

        if (preg_match('/(\d+)\s*(m|min|mins|minute|minutes)/i', $time, $minuteMatch)) {
            $minutes = (int) $minuteMatch[1];
        }

        if ($hours === 0 && $minutes === 0) {
            preg_match_all('/\d+/', $time, $numberMatches);
            $numbers = $numberMatches[0] ?? [];

            if (count($numbers) >= 2) {
                $hours = (int) $numbers[0];
                $minutes = (int) $numbers[1];
            } elseif (count($numbers) === 1) {
                $minutes = (int) $numbers[0];
            }
        }

        $total = ($hours * 60) + $minutes;
        return $total > 0 ? $total : null;
    }

    public function setEstimatedTimeAttribute($value): void
    {
        $minutes = static::parseEstimatedTimeToMinutes($value);

        if ($minutes === null) {
            $this->attributes['estimated_time'] = trim((string) $value);
            return;
        }

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;
        $this->attributes['estimated_time'] = sprintf('%02d:%02d', $hours, $remainingMinutes);
    }

    public function getEstimatedTimeMinutesAttribute(): ?int
    {
        return static::parseEstimatedTimeToMinutes($this->estimated_time);
    }

    public function getNormalizedEstimatedTimeAttribute(): ?string
    {
        $minutes = $this->estimated_time_minutes;
        if (!$minutes) {
            return null;
        }

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;
        return sprintf('%02d:%02d', $hours, $remainingMinutes);
    }

    public function getFormattedEstimatedTimeAttribute(): string
    {
        $minutes = $this->estimated_time_minutes;
        if (!$minutes) {
            return '-';
        }

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;
        $parts = [];

        if ($hours > 0) {
            $parts[] = $hours . ' hr';
        }

        if ($remainingMinutes > 0) {
            $parts[] = $remainingMinutes . ' min';
        }

        return implode(' ', $parts);
    }

    public function getFormattedDistanceKmAttribute(): string
    {
        if ($this->distance_km === null) {
            return '-';
        }

        $distance = (float) $this->distance_km;
        $formatted = fmod($distance, 1.0) === 0.0
            ? (string) (int) $distance
            : rtrim(rtrim(number_format($distance, 1, '.', ''), '0'), '.');

        return $formatted . ' km';
    }

    public function getRouteNameAttribute()
    {
        return $this->origin_dzongkhag . ' → ' . $this->destination_dzongkhag;
    }
}
