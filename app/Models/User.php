<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'phone_number',
        'email',
        'google_id',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function driver()
    {
        return $this->hasOne(Driver::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'passenger_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isDriver()
    {
        return $this->role === 'driver';
    }

    public function isPassenger()
    {
        return $this->role === 'passenger';
    }
}
