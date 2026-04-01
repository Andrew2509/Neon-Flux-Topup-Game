<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PaymentMethod;

$methods = [
    ['code' => 'QRIS', 'name' => 'QRIS (TokoVoucher)', 'type' => 'E-Wallet'],
    ['code' => 'OVO', 'name' => 'OVO (TokoVoucher)', 'type' => 'E-Wallet'],
    ['code' => 'DANA', 'name' => 'DANA (TokoVoucher)', 'type' => 'E-Wallet'],
    ['code' => 'LINKAJA', 'name' => 'LinkAja (TokoVoucher)', 'type' => 'E-Wallet'],
    ['code' => 'SHOPEEPAY', 'name' => 'ShopeePay (TokoVoucher)', 'type' => 'E-Wallet'],
    ['code' => 'GOPAY', 'name' => 'GoPay (TokoVoucher)', 'type' => 'E-Wallet'],
    ['code' => 'BCA', 'name' => 'BCA (TokoVoucher)', 'type' => 'Virtual Account'],
    ['code' => 'BNI', 'name' => 'BNI (TokoVoucher)', 'type' => 'Virtual Account'],
    ['code' => 'BRI', 'name' => 'BRI (TokoVoucher)', 'type' => 'Virtual Account'],
    ['code' => 'MANDIRI', 'name' => 'Mandiri (TokoVoucher)', 'type' => 'Virtual Account'],
    ['code' => 'PERMATA', 'name' => 'Permata (TokoVoucher)', 'type' => 'Virtual Account'],
    ['code' => 'ALFAMART', 'name' => 'Alfamart (TokoVoucher)', 'type' => 'Convenience Store'],
    ['code' => 'INDOMARET', 'name' => 'Indomaret (TokoVoucher)', 'type' => 'Convenience Store']
];

foreach ($methods as $m) {
    PaymentMethod::updateOrCreate(
        ['code' => $m['code']],
        array_merge($m, [
            'status' => 'Aktif',
            'provider' => 'TokoVoucher',
            'fee' => 0,
            'image' => 'assets/images/payments/' . strtolower($m['code']) . '.png'
        ])
    );
}

echo "TokoVoucher deposit methods seeded successfully.\n";
