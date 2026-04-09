<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['category_id', 'type', 'name', 'provider', 'product_code', 'cost', 'price', 'status'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class);
    }

    public function productJenis()
    {
        return $this->belongsTo(ProductJenis::class, 'product_jenis_id');
    }
    
    /**
     * Calculate price based on cost and global settings.
     */
    public static function calculatePrice($cost, $marginPercent)
    {
        return (float)ceil($cost * (1 + ($marginPercent / 100)));
    }

    /**
     * Recalculate all service prices based on current margin settings.
     */
    public static function recalculateAllPrices()
    {
        $marginPublic = SiteSetting::where('key', 'margin_public')->value('value') ?? 10;

        // Optimization: Use a single raw SQL query to update all prices at once.
        // Formula: CEIL(cost * (1 + margin/100)) - REMOVED transaction_fee
        \Illuminate\Support\Facades\DB::statement(
            "UPDATE services SET price = CEIL(cost * (1 + (? / 100))) WHERE status != 'Nonaktif'",
            [$marginPublic]
        );
    }
}
