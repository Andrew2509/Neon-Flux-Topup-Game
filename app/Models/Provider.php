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

    /**
     * Cek apakah provider ini adalah TokoVoucher.
     */
    public function isTokovoucher(): bool
    {
        $name = strtolower(trim((string) ($this->name ?? '')));
        return str_contains($name, 'toko') || str_contains($name, 'tokovoucher');
    }

    /**
     * Resolusi provider TokoVoucher yang sedang aktif.
     */
    public static function resolveTokovoucher(): ?self
    {
        return static::where('status', 'Aktif')
            ->where(function ($q) {
                $q->where('name', 'LIKE', '%Toko%')
                  ->orWhere('name', 'LIKE', '%tokovoucher%');
            })
            ->first();
    }

    /**
     * Hitung batas maksimum nominal produk yang boleh ditampilkan berdasarkan saldo.
     *
     * Aturan:
     *  - Saldo < 500.000        → cap 50% (saldo rendah, lebih konservatif)
     *  - 500.000 ≤ saldo < 2.000.000 → cap 60%
     *  - Saldo ≥ 2.000.000     → cap 75%
     *
     * Return null jika saldo tidak tersedia atau provider tidak ditemukan.
     *
     * @return float|null  Batas nominal maksimum dalam Rupiah, atau null = tidak ada batasan
     */
    public function maxAllowedNominal(): ?float
    {
        $balance = (float) ($this->balance ?? 0);

        if ($balance <= 0) {
            // Saldo nol atau tidak diketahui → sembunyikan semua (kembalikan 0)
            return 0.0;
        }

        if ($balance < 500_000) {
            $ratio = 0.50;
        } elseif ($balance < 2_000_000) {
            $ratio = 0.60;
        } else {
            $ratio = 0.75;
        }

        return floor($balance * $ratio);
    }
}
