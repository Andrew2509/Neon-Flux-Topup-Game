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
     * Calculate price based on cost, margin settings, and gateway fees.
     * Formula: MAX( (Cost * Margin) + FlatFee, (Cost * Margin) / (1 - PercentFee) )
     * This ensures the merchant receives (Cost * Margin) net after gateway fees.
     */
    public static function calculatePrice($cost, $marginPercent)
    {
        $marginMultiplier = 1 + ($marginPercent / 100);
        $desiredNet = $cost * $marginMultiplier;

        // Fetch gateway fees from settings or use defaults based on iPaymu structure
        $feeFlat = SiteSetting::where('key', 'gateway_fee_flat')->value('value') ?? 4500;
        $feePercent = SiteSetting::where('key', 'gateway_fee_percent')->value('value') ?? 2.5;
        
        $priceWithFlat = $desiredNet + $feeFlat;
        $priceWithPercent = $desiredNet / (1 - ($feePercent / 100));

        // Use the higher price to ensure cost + profit are covered
        $finalPrice = max($priceWithFlat, $priceWithPercent);

        return (float)ceil($finalPrice);
    }

    /**
     * Recalculate all service prices based on current margin and gateway fee settings.
     */
    public static function recalculateAllPrices()
    {
        $marginPublic = SiteSetting::where('key', 'margin_public')->value('value') ?? 10;
        $feeFlat = SiteSetting::where('key', 'gateway_fee_flat')->value('value') ?? 4500;
        $feePercent = SiteSetting::where('key', 'gateway_fee_percent')->value('value') ?? 2.5;

        $marginMultiplier = 1 + ($marginPublic / 100);
        $divisor = 1 - ($feePercent / 100);

        // Optimization: Use GREATEST in SQL to choose the higher price between flat fee and percentage fee paths.
        // Formula: CEIL(GREATEST( (cost * margin) + flat, (cost * margin) / percent_divisor ))
        \Illuminate\Support\Facades\DB::statement(
            "UPDATE services 
             SET price = CEIL(GREATEST( (cost * ?) + ?, (cost * ?) / ? )) 
             WHERE status != 'Nonaktif'",
            [$marginMultiplier, $feeFlat, $marginMultiplier, $divisor]
        );
    }
}
