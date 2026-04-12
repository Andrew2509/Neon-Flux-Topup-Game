<?php

namespace Database\Seeders;

use App\Models\Rating;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bersihkan data lama agar nama-nama sebelumnya tidak menumpuk
        Rating::truncate();

        $testimonials = [
            [
                'product_name' => 'Mobile Legends',
                'stars' => 5,
                'comment' => 'Prosesnya cepet banget, gak sampe 5 menit diamond udah masuk. Mantap NeonFlux!',
                'author_nickname' => 'Ahmad Fauzi',
            ],
            [
                'product_name' => 'Free Fire',
                'stars' => 5,
                'comment' => 'Awalnya ragu, tapi ternyata beneran aman dan murah. Langganan di sini terus deh.',
                'author_nickname' => 'Siti Aminah',
            ],
            [
                'product_name' => 'Valorant',
                'stars' => 5,
                'comment' => 'Gila sih harganya paling murah se-Indonesia. Adminnya juga ramah.',
                'author_nickname' => 'Rizky Pratama',
            ],
            [
                'product_name' => 'Genshin Impact',
                'stars' => 5,
                'comment' => 'Topup Genshin di sini paling worth it. Bonusnya banyak!',
                'author_nickname' => 'Dwi Cahyo',
            ],
            [
                'product_name' => 'Mobile Legends',
                'stars' => 4,
                'comment' => 'Recommended banget buat yang mau top up game. Gak pake ribet.',
                'author_nickname' => 'Budi Santoso',
            ],
            [
                'product_name' => 'Steam Wallet',
                'stars' => 5,
                'comment' => 'Situsnya user friendly, pembayarannya juga lengkap. Top pokoknya.',
                'author_nickname' => 'Putri Indah',
            ],
            [
                'product_name' => 'Mobile Legends',
                'stars' => 5,
                'comment' => 'Pelayanan bintang 5, sukses terus buat NeonFlux!',
                'author_nickname' => 'Aditya Wijaya',
            ],
            [
                'product_name' => 'PUBG Mobile',
                'stars' => 5,
                'comment' => 'Asli murah banget UC nya, auto borong skin!',
                'author_nickname' => 'Maya Sari',
            ],
            [
                'product_name' => 'Call of Duty Mobile',
                'stars' => 5,
                'comment' => 'CP langsung masuk hitungan detik. Gak nyesel top up di sini.',
                'author_nickname' => 'Eko Prasetyo',
            ],
            [
                'product_name' => 'Roblox',
                'stars' => 4,
                'comment' => 'Robux nya murah-murah, beli banyak tetep hemat.',
                'author_nickname' => 'Ani Lestari',
            ],
        ];

        foreach ($testimonials as $data) {
            Rating::create(array_merge($data, [
                'is_visible' => true,
                'order_id' => 'SEED-' . strtoupper(bin2hex(random_bytes(4))),
            ]));
        }
    }
}
