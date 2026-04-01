<?php

namespace Database\Seeders;

use App\Models\Provider;
use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Provider::updateOrCreate(
            ['name' => 'Orbit WhatsApp'],
            [
                'provider_id' => 'Orbit-API',
                'api_key' => env('ORBIT_WA_API_KEY', 'default_key'),
                'balance' => 0,
                'status' => 'Aktif',
                'icon' => 'chat_paste',
            ]
        );
    }
}
