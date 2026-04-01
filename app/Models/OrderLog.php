<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderLog extends Model
{
    protected $fillable = [
        'order_id',
        'status_from',
        'status_to',
        'message',
        'payload'
    ];

    protected $casts = [
        'payload' => 'array'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
}
