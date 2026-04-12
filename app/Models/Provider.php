<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $fillable = ['name', 'provider_id', 'api_key', 'balance', 'status', 'icon', 'mode'];

    /**
     * ENVIRONMENT MODE dari panel admin: hanya nilai "production" = API live (my.ipaymu).
     * Sandbox / Development atau kosong = environment uji.
     */
    public function usesProductionApi(): bool
    {
        return strtolower(trim((string) ($this->mode ?? ''))) === 'production';
    }

    /**
     * Baris kredensial iPaymu untuk API (nama persis "iPaymu" atau mengandung "ipaymu").
     */
    public static function forIpaymu(): ?self
    {
        return static::whereRaw('LOWER(TRIM(name)) = ?', ['ipaymu'])->first()
            ?? static::whereRaw('LOWER(name) LIKE ?', ['%ipaymu%'])->orderBy('id')->first();
    }

    /**
     * Baris kredensial TokoVoucher.
     */
    public static function forTokovoucher(): ?self
    {
        return static::where('name', 'like', '%Toko%')
            ->orWhere('name', 'like', '%tokovoucher%')
            ->orderBy('id')
            ->first();
    }

    /**
     * Calculate safe nominal limit based on available balance.
     * Logic:
     * - < 1M: 50%
     * - 1M - 2M: Balance - 500k
     * - > 2M: 75%
     */
    public function getSafeNominalLimit(): float
    {
        $balance = (float) $this->balance;

        if ($balance >= 2000000) {
            return $balance * 0.75;
        }

        if ($balance >= 1000000) {
            return $balance - 500000;
        }

        return $balance * 0.5;
    }
}
