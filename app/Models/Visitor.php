<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'url',
        'city',
        'region',
        'country',
        'country_code',
        'last_active_at'
    ];

    protected $casts = [
        'last_active_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOnline($query)
    {
        return $query->where('last_active_at', '>=', now()->subMinutes(5));
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', now()->toDateString());
    }
}
