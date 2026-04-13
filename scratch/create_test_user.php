<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use App\Models\User;
use Illuminate\Support\Facades\Artisan;

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$user = User::firstOrCreate(['email' => 'tester@example.com'], [
    'name' => 'Tester Account',
    'password' => bcrypt('password'),
    'phone' => '08123456789'
]);

$user->balance = 500000;
$user->save();

echo "User created/updated: {$user->email} with balance {$user->balance}\n";
