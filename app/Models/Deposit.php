<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $fillable = [
        'deposit_id',
        'user_id',
        'amount',
        'method',
        'status',
        'payload'
    ];

    protected $casts = [
        'payload' => 'array',
        'amount' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
