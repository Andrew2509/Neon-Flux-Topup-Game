<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin Neon Flux',
            'email' => 'admin@neonflux.id',
            'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Member Test',
            'email' => 'member@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'member',
        ]);
    }
}
