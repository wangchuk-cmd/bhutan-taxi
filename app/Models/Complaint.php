<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $table = 'complaints';

    protected $fillable = [
        'user_id',
        'trip_id',
        'type',
        'subject',
        'message',
        'admin_response',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
