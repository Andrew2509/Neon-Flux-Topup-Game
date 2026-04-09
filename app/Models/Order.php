<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'product_name',
        'total_price',
        'payment_method',
        'status',
        'payload'
    ];

    protected $casts = [
        'payload' => 'array',
        'total_price' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logs()
    {
        return $this->hasMany(OrderLog::class, 'order_id', 'order_id');
    }

    public function logStatus($message, $to = null, $payload = null)
    {
        $from = $this->status;
        if ($to) {
            $this->update(['status' => $to]);
        }
        
        return $this->logs()->create([
            'status_from' => $from,
            'status_to' => $to ?? $from,
            'message' => $message,
            'payload' => $payload
        ]);
    }

    /**
     * Helper to check if this order received a first purchase bonus.
     */
    public function isFirstPurchaseBonus(): bool
    {
        return (bool) data_get($this->payload, 'first_purchase_bonus.is_eligible', false);
    }

    /**
     * Get base diamonds from payload.
     */
    public function getBaseDiamondsAttribute()
    {
        return data_get($this->payload, 'first_purchase_bonus.base_amount', 0);
    }

    /**
     * Get bonus diamonds from payload.
     */
    public function getBonusDiamondsAttribute()
    {
        return data_get($this->payload, 'first_purchase_bonus.bonus_amount', 0);
    }
}
