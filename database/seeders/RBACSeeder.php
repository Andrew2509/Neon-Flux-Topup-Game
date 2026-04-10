<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RBACSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'Akses Dashboard', 'slug' => 'akses-dashboard'],
            ['name' => 'Kelola Pesanan', 'slug' => 'kelola-pesanan'],
            ['name' => 'Kelola Member', 'slug' => 'kelola-member'],
            ['name' => 'Kelola Deposit', 'slug' => 'kelola-deposit'],
            ['name' => 'Kelola Kategori', 'slug' => 'kelola-kategori'],
            ['name' => 'Kelola Layanan', 'slug' => 'kelola-layanan'],
            ['name' => 'Kelola Paket', 'slug' => 'kelola-paket'],
            ['name' => 'Kelola TokoVoucher', 'slug' => 'kelola-tokovoucher'],
            ['name' => 'Kelola Logo Generator', 'slug' => 'kelola-logo'],
            ['name' => 'Kelola Voucher', 'slug' => 'kelola-voucher'],
            ['name' => 'Kelola Slider Banner', 'slug' => 'kelola-slider'],
            ['name' => 'Kelola Pembayaran', 'slug' => 'kelola-pembayaran'],
            ['name' => 'Kelola Penarikan', 'slug' => 'kelola-penarikan'],
            ['name' => 'Lihat Rating', 'slug' => 'view-rating'],
            ['name' => 'Kelola Provider', 'slug' => 'kelola-provider'],
            ['name' => 'Kelola Setting Website', 'slug' => 'kelola-setting'],
            ['name' => 'Manajemen User & Akses', 'slug' => 'manajemen-user-akses'],
        ];

        foreach ($permissions as $permission) {
            \App\Models\Permission::updateOrCreate(['slug' => $permission['slug']], $permission);
        }

        $superAdmin = \App\Models\Role::where('slug', 'super-admin')->first();
        if ($superAdmin) {
            $superAdmin->permissions()->sync(\App\Models\Permission::all());
        }

        $memberRole = \App\Models\Role::where('slug', 'member')->first();
        // Members don't usually need permissions in this system but we can add later if needed.
    }
}
