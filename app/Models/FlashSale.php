<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashSale extends Model
{
    protected $fillable = [
        'service_id',
        'discount_price',
        'start_time',
        'end_time',
        'status',
        'stock'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'discount_price' => 'float',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Scope a query to only include active flash sales.
     */
    public function scopeActive($query)
    {
        return $query->where('flash_sales.status', 'Aktif')
            ->where('flash_sales.start_time', '<=', now())
            ->where('flash_sales.end_time', '>=', now());
    }
}
