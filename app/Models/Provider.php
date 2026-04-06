<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $fillable = ['name', 'provider_id', 'api_key', 'balance', 'status', 'icon', 'mode'];

    /**
     * ENVIRONMENT MODE dari panel admin: hanya nilai "production" = API live (my.ipaymu, api.doku, passport.duitku, Midtrans prod).
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
}
