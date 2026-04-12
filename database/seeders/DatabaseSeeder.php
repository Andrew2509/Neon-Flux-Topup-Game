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
        \App\Models\User::create([
            'name' => 'Admin Neon Flux',
            'email' => 'admin@neonflux.id',
            'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        \App\Models\User::create([
            'name' => 'Member Test',
            'email' => 'member@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'member',
            'email_verified_at' => now(),
        ]);

        $this->call([
            RatingSeeder::class,
        ]);
    }
}
