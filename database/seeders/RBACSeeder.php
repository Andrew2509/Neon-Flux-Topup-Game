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
            ['name' => 'Kelola Produk', 'slug' => 'kelola-produk'],
            ['name' => 'Kelola Voucher', 'slug' => 'kelola-voucher'],
            ['name' => 'Kelola Pembayaran', 'slug' => 'kelola-pembayaran'],
            ['name' => 'Kelola Setting', 'slug' => 'kelola-setting'],
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
