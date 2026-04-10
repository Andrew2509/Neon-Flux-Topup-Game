<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => []], function () {
    $isMobile = function () {
        $userAgent = request()->header('User-Agent');
        $isTablet = (bool) preg_match('/huawei|harmony|mediapad|matepad|mate\s*pad|honor|pad|playbook|silk|kindle|hp-tablet|ipad|tablet/i', $userAgent) ||
                    ((bool) preg_match('/android/i', $userAgent) && ! (bool) preg_match('/mobile/i', $userAgent));
        $isMobile = (bool) preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent);

        return $isTablet || $isMobile;
    };

    Route::get('/', [App\Http\Controllers\CatalogController::class, 'index'])->name('home');
    Route::get('/track', [App\Http\Controllers\TransactionController::class, 'trackOrder'])->name('track.order');
    Route::get('/topup/berhasil', [App\Http\Controllers\TransactionController::class, 'topupSuccess'])->name('topup.success');
    Route::post('/testimoni', [App\Http\Controllers\TestimonialController::class, 'store'])->name('testimoni.store');
    Route::get('/api/order/{order_id}/poll', [App\Http\Controllers\TransactionController::class, 'pollOrderStatus'])->name('order.poll');
    Route::get('/api/notifications/recent', [App\Http\Controllers\Api\TransactionNotificationController::class, 'getRecent'])->name('api.notifications.recent');
    Route::get('/cek-transaksi', function () {
        return redirect()->route('track.order');
    });
    Route::post('/api/checkout', [App\Http\Controllers\TransactionController::class, 'checkout'])
        ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

    Route::prefix('kalkulator')->name('kalkulator.')->group(function () {
        Route::get('/', [App\Http\Controllers\CalculatorController::class, 'index'])->name('index');
        Route::get('/winrate', [App\Http\Controllers\CalculatorController::class, 'winrate'])->name('winrate');
        Route::get('/magicwheel', [App\Http\Controllers\CalculatorController::class, 'magicwheel'])->name('magicwheel');
        Route::get('/zodiac', [App\Http\Controllers\CalculatorController::class, 'zodiac'])->name('zodiac');
    });

    Route::get('/catalog', function () {
        return redirect()->route('home');
    })->name('catalog');
    Route::get('/syarat-ketentuan', [App\Http\Controllers\StaticPageController::class, 'syaratKetentuan'])->name('syarat-ketentuan');
    Route::get('/kebijakan-privasi', [App\Http\Controllers\StaticPageController::class, 'kebijakanPrivasi'])->name('kebijakan-privasi');
    Route::get('/kebijakan-refund', [App\Http\Controllers\StaticPageController::class, 'kebijakanRefund'])->name('kebijakan-refund');
    Route::get('/faq', [App\Http\Controllers\StaticPageController::class, 'faq'])->name('faq');
    Route::get('/cara-order', [App\Http\Controllers\StaticPageController::class, 'caraOrder'])->name('cara-order');
    Route::get('/leaderboard', [App\Http\Controllers\StaticPageController::class, 'leaderboard'])->name('leaderboard');

    Route::get('/topup/{slug}', [App\Http\Controllers\CatalogController::class, 'showTopup'])->name('topup.game');
    Route::post('/api/voucher/validate', [App\Http\Controllers\VoucherController::class, 'validateVoucher'])->name('voucher.validate');

    Route::post('/checkout', [App\Http\Controllers\TransactionController::class, 'checkout'])->name('checkout');
    Route::get('/checkout', function () {
        return redirect()->route('home');
    });

    Route::get('/debug/duitku', function () {
        $duitku = \App\Models\Provider::where('name', 'like', '%Duitku%')->first();
        if (! $duitku) {
            return response()->json(['error' => 'Provider Duitku not found in DB.']);
        }

        $hostMode = $duitku->usesProductionApi() ? 'passport' : 'sandbox';
        $url = "https://{$hostMode}.duitku.com/webapi/api/merchant/v2/inquiry";

        $data = [
            'provider_mode' => $duitku->mode,
            'duitku_host' => $hostMode,
            'url' => $url,
            'merchantCode' => $duitku->provider_id,
            'has_api_key' => ! empty($duitku->api_key),
            'app_env' => env('APP_ENV'),
            'app_debug' => env('APP_DEBUG'),
        ];

        try {
            // Test 1: Connectivity & Merchant Recognition (POST Inquiry)
            $resp = \Illuminate\Support\Facades\Http::post($url, [
                'merchantCode' => $duitku->provider_id,
                'merchantOrderId' => 'DEBUG-'.time(),
                'paymentAmount' => 10000,
                'productDetails' => 'Debug Test',
                'email' => 'debug@test.com',
                'signature' => md5($duitku->provider_id.'DEBUG-'.time().'10000'.$duitku->api_key),
            ]);
            $data['inquiry_test'] = [
                'method' => 'POST',
                'status' => $resp->status(),
                'body' => $resp->json() ?: $resp->body(),
            ];

            // Test 2: List Payment Methods
            $tz = new \DateTimeZone('Asia/Jakarta');
            $now = new \DateTime('now', $tz);
            $dt = $now->format('Y-m-d H:i:s');

            $pmUrl = "https://{$hostMode}.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod";
            $pmSig = md5($duitku->provider_id.'10000'.$dt.$duitku->api_key);
            $pmResp = \Illuminate\Support\Facades\Http::post($pmUrl, [
                'merchantCode' => $duitku->provider_id,
                'amount' => 10000,
                'datetime' => $dt,
                'signature' => $pmSig,
            ]);
            $data['payment_methods_api'] = [
                'status' => $pmResp->status(),
                'datetime_sent' => $dt,
                'body' => $pmResp->json(),
            ];
        } catch (\Exception $e) {
            $data['diagnostic_error'] = $e->getMessage();
        }

        return response()->json($data);
    });
    Route::post('/api/check-id', [App\Http\Controllers\TransactionController::class, 'checkPlayerId'])
        ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
        ->name('topup.check-id');
    Route::post('/api/duitku/callback', [App\Http\Controllers\TransactionController::class, 'duitkuCallback'])->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
    Route::post('/api/ipaymu/callback', [App\Http\Controllers\TransactionController::class, 'ipaymuCallback'])
        ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
        ->name('api.ipaymu.callback');
    Route::post('/api/doku/callback', [App\Http\Controllers\TransactionController::class, 'dokuCallback'])->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
    Route::post('/api/midtrans/callback', [App\Http\Controllers\TransactionController::class, 'midtransCallback'])->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
    Route::match(['get', 'post'], '/api/tokovoucher/webhook', [App\Http\Controllers\Api\WebhookController::class, 'tokovoucher'])->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

    // Debug Routes
    Route::get('/debug/ip', function () {
        $response = Http::get('https://api.ipify.org?format=json');

        return response()->json([
            'outgoing_ip' => $response->json()['ip'] ?? 'Unknown',
            'user_ip' => request()->ip(),
            'note' => 'IP iPaymu adalah outgoing_ip',
        ]);
    });
    Route::get('/cron/sync-tokovoucher/{token}', [App\Http\Controllers\Api\CronController::class, 'syncTokovoucher'])->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
    Route::post('/admin/cron/register', [App\Http\Controllers\Api\CronController::class, 'registerToCronJobOrg'])->name('admin.cron.register');

    // Admin Routes
    Route::get('/admin-dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.orders');
    Route::post('/admin/orders/{order}/fulfill-tokovoucher', [App\Http\Controllers\Admin\OrderController::class, 'fulfillTokovoucher'])->name('admin.orders.fulfill_tokovoucher');
    Route::delete('/admin/orders/mass-destroy', [App\Http\Controllers\Admin\OrderController::class, 'massDestroy'])->name('admin.orders.mass_destroy');
    Route::delete('/admin/orders/destroy-all', [App\Http\Controllers\Admin\OrderController::class, 'destroyAll'])->name('admin.orders.destroy_all');
    Route::get('/admin/members', [App\Http\Controllers\Admin\MemberController::class, 'index'])->name('admin.members');
    Route::get('/admin/members/create', [App\Http\Controllers\Admin\MemberController::class, 'create'])->name('admin.members.create');
    Route::post('/admin/members', [App\Http\Controllers\Admin\MemberController::class, 'store'])->name('admin.members.store');
    Route::get('/admin/members/{user}', [App\Http\Controllers\Admin\MemberController::class, 'show'])->name('admin.members.show');
    Route::get('/admin/members/{user}/edit', [App\Http\Controllers\Admin\MemberController::class, 'edit'])->name('admin.members.edit');
    Route::put('/admin/members/{user}', [App\Http\Controllers\Admin\MemberController::class, 'update'])->name('admin.members.update');
    Route::delete('/admin/members/{user}', [App\Http\Controllers\Admin\MemberController::class, 'destroy'])->name('admin.members.destroy');
    Route::get('/admin/deposits', [App\Http\Controllers\Admin\DepositController::class, 'index'])->name('admin.deposits');
    Route::get('/admin/categories', [App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('admin.categories');
    Route::get('/admin/services', [App\Http\Controllers\Admin\ServiceController::class, 'index'])->name('admin.services');
    Route::get('/admin/services/ajax-search', [App\Http\Controllers\Admin\ServiceController::class, 'ajaxSearch'])->name('admin.services.ajax_search');
    Route::get('/admin/services/sync-list', [App\Http\Controllers\Admin\ServiceController::class, 'getSyncList'])->name('admin.services.sync_list');
    Route::post('/admin/services/sync', [App\Http\Controllers\Admin\ServiceController::class, 'syncTokoVoucher'])->name('admin.services.sync');
    Route::post('/admin/services/{id}/toggle', [App\Http\Controllers\Admin\ServiceController::class, 'toggle'])->name('admin.services.toggle');
    Route::get('/admin/packages', [App\Http\Controllers\Admin\PackageController::class, 'index'])->name('admin.packages.index');
    Route::get('/admin/packages/operator/{id}', [App\Http\Controllers\Admin\PackageController::class, 'showOperator'])->name('admin.packages.operator');
    Route::get('/admin/packages/operator/{id}/edit', [App\Http\Controllers\Admin\PackageController::class, 'editOperator'])->name('admin.packages.operator.edit');
    Route::put('/admin/packages/operator/{id}', [App\Http\Controllers\Admin\PackageController::class, 'updateOperator'])->name('admin.packages.operator.update');
    Route::post('/admin/packages/operator/{id}/toggle', [App\Http\Controllers\Admin\PackageController::class, 'toggleOperatorStatus'])->name('admin.packages.operator.toggle');
    Route::get('/admin/packages/jenis/{id}', [App\Http\Controllers\Admin\PackageController::class, 'showJenis'])->name('admin.packages.jenis');
    Route::post('/admin/packages/jenis/{id}/toggle', [App\Http\Controllers\Admin\PackageController::class, 'toggleJenisStatus'])->name('admin.packages.jenis.toggle');
    Route::post('/admin/packages/services/{id}/toggle', [App\Http\Controllers\Admin\PackageController::class, 'toggleServiceStatus'])->name('admin.packages.services.toggle');
    Route::delete('/admin/packages/{package}', [App\Http\Controllers\Admin\PackageController::class, 'destroy'])->name('admin.packages.destroy');
    Route::get('/admin/vouchers', [App\Http\Controllers\Admin\VoucherController::class, 'index'])->name('admin.vouchers');
    Route::get('/admin/vouchers/create', [App\Http\Controllers\Admin\VoucherController::class, 'create'])->name('admin.vouchers.create');
    Route::post('/admin/vouchers', [App\Http\Controllers\Admin\VoucherController::class, 'store'])->name('admin.vouchers.store');
    Route::get('/admin/vouchers/{voucher}', [App\Http\Controllers\Admin\VoucherController::class, 'show'])->name('admin.vouchers.show');
    Route::get('/admin/vouchers/{voucher}/edit', [App\Http\Controllers\Admin\VoucherController::class, 'edit'])->name('admin.vouchers.edit');
    Route::put('/admin/vouchers/{voucher}', [App\Http\Controllers\Admin\VoucherController::class, 'update'])->name('admin.vouchers.update');
    Route::delete('/admin/vouchers/{voucher}', [App\Http\Controllers\Admin\VoucherController::class, 'destroy'])->name('admin.vouchers.destroy');
    Route::get('/admin/sliders', [App\Http\Controllers\Admin\SliderController::class, 'index'])->name('admin.sliders');
    Route::get('/admin/sliders/create', [App\Http\Controllers\Admin\SliderController::class, 'create'])->name('admin.sliders.create');
    Route::post('/admin/sliders', [App\Http\Controllers\Admin\SliderController::class, 'store'])->name('admin.sliders.store');
    Route::get('/admin/sliders/{slider}/edit', [App\Http\Controllers\Admin\SliderController::class, 'edit'])->name('admin.sliders.edit');
    Route::put('/admin/sliders/{slider}', [App\Http\Controllers\Admin\SliderController::class, 'update'])->name('admin.sliders.update');
    Route::delete('/admin/sliders/{slider}', [App\Http\Controllers\Admin\SliderController::class, 'destroy'])->name('admin.sliders.destroy');
    Route::get('/admin/withdrawals/bank', [App\Http\Controllers\Admin\WithdrawalController::class, 'bankForm'])->name('admin.withdrawals.bank');
    Route::post('/admin/withdrawals/bank', [App\Http\Controllers\Admin\WithdrawalController::class, 'processBank'])->name('admin.withdrawals.bank.process');
    Route::get('/admin/withdrawals/ewallet', [App\Http\Controllers\Admin\WithdrawalController::class, 'ewalletForm'])->name('admin.withdrawals.ewallet');
    Route::post('/admin/withdrawals/ewallet', [App\Http\Controllers\Admin\WithdrawalController::class, 'processEwallet'])->name('admin.withdrawals.ewallet.process');

    Route::get('/admin/payments', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('admin.payments');
    Route::post('/admin/payments/sync', [App\Http\Controllers\Admin\PaymentController::class, 'syncDuitku'])->name('admin.payments.sync');
    Route::post('/admin/payments/sync-ipaymu', [App\Http\Controllers\Admin\PaymentController::class, 'syncIPaymu'])->name('admin.payments.sync_ipaymu');
    Route::post('/admin/payments/sync-midtrans', [App\Http\Controllers\Admin\PaymentController::class, 'syncMidtrans'])->name('admin.payments.sync_midtrans');
    Route::post('/admin/payments/sync-doku', [App\Http\Controllers\Admin\PaymentController::class, 'syncDoku'])->name('admin.payments.sync_doku');
    Route::get('/admin/payments/create', [App\Http\Controllers\Admin\PaymentController::class, 'create'])->name('admin.payments.create');
    Route::post('/admin/payments', [App\Http\Controllers\Admin\PaymentController::class, 'store'])->name('admin.payments.store');
    Route::get('/admin/payments/{payment}/edit', [App\Http\Controllers\Admin\PaymentController::class, 'edit'])->name('admin.payments.edit');
    Route::put('/admin/payments/{payment}', [App\Http\Controllers\Admin\PaymentController::class, 'update'])->name('admin.payments.update');
    Route::post('/admin/payments/{payment}/toggle', [App\Http\Controllers\Admin\PaymentController::class, 'toggle'])->name('admin.payments.toggle');
    Route::delete('/admin/payments/{payment}', [App\Http\Controllers\Admin\PaymentController::class, 'destroy'])->name('admin.payments.destroy');

    Route::get('/admin/ratings', [App\Http\Controllers\Admin\RatingController::class, 'index'])->name('admin.ratings');
    Route::get('/admin/providers', [App\Http\Controllers\Admin\ProviderController::class, 'index'])->name('admin.providers');
    Route::get('/admin/providers/create', [App\Http\Controllers\Admin\ProviderController::class, 'create'])->name('admin.providers.create');
    Route::post('/admin/providers', [App\Http\Controllers\Admin\ProviderController::class, 'store'])->name('admin.providers.store');
    Route::get('/admin/providers/{provider}/edit', [App\Http\Controllers\Admin\ProviderController::class, 'edit'])->name('admin.providers.edit');
    Route::put('/admin/providers/{provider}', [App\Http\Controllers\Admin\ProviderController::class, 'update'])->name('admin.providers.update');
    Route::delete('/admin/providers/{provider}', [App\Http\Controllers\Admin\ProviderController::class, 'destroy'])->name('admin.providers.destroy');
    Route::post('/admin/providers/{provider}/balance', [App\Http\Controllers\Admin\ProviderController::class, 'syncBalance'])->name('admin.providers.balance');
    Route::get('/admin/providers/{provider}/deposit', [App\Http\Controllers\Admin\ProviderController::class, 'showDepositForm'])->name('admin.providers.deposit.form');
    Route::post('/admin/providers/{provider}/deposit', [App\Http\Controllers\Admin\ProviderController::class, 'processDeposit'])->name('admin.providers.deposit.process');
    Route::get('/admin/tokovoucher/categories', [\App\Http\Controllers\Admin\TokovoucherController::class, 'categories'])->name('admin.tokovoucher.categories');
    Route::post('/admin/tokovoucher/categories/{id}/sync', [\App\Http\Controllers\Admin\TokovoucherController::class, 'syncCategory'])->name('admin.tokovoucher.sync');
    Route::post('/admin/tokovoucher/categories/{id}/toggle', [\App\Http\Controllers\Admin\TokovoucherController::class, 'toggleProviderCategory'])->name('admin.tokovoucher.toggle');

    Route::get('/admin/logo-generator', [\App\Http\Controllers\Admin\LogoGeneratorController::class, 'index'])->name('admin.logo-generator.index');
    Route::post('/admin/logo-generator/generate', [\App\Http\Controllers\Admin\LogoGeneratorController::class, 'generate'])->name('admin.logo-generator.generate');

    Route::get('/admin/settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('admin.settings');
    Route::post('/admin/settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('admin.settings.update');

    // Authentication Routes
    Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->middleware('throttle:auth');
    Route::get('/register', [App\Http\Controllers\AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [App\Http\Controllers\AuthController::class, 'register'])->middleware('throttle:auth');
    Route::get('/forgot-password', [App\Http\Controllers\AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [App\Http\Controllers\AuthController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:otp');
    Route::get('/verify-otp', [App\Http\Controllers\AuthController::class, 'showVerifyOtp'])->name('verify.otp');
    Route::post('/verify-otp', [App\Http\Controllers\AuthController::class, 'verifyOtp'])->middleware('throttle:otp');
    Route::post('/resend-otp', [App\Http\Controllers\AuthController::class, 'resendOtp'])->name('resend.otp')->middleware('throttle:otp');
    Route::get('/reset-password-phone', [App\Http\Controllers\AuthController::class, 'showResetPasswordForm'])->name('password.reset.phone');
    Route::post('/reset-password-phone', [App\Http\Controllers\AuthController::class, 'resetPassword']);
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

    // User Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/user/dashboard', [App\Http\Controllers\UserController::class, 'index'])->name('user.dashboard');
        Route::get('/user/profile', [App\Http\Controllers\UserController::class, 'profile'])->name('user.profile');
        Route::post('/user/profile', [App\Http\Controllers\UserController::class, 'updateProfile'])->name('user.profile.update');
        Route::post('/user/password', [App\Http\Controllers\UserController::class, 'updatePassword'])->name('user.password.update');
        Route::get('/user/riwayat', [App\Http\Controllers\UserController::class, 'riwayat'])->name('user.riwayat');
        Route::get('/user/deposit', [App\Http\Controllers\UserController::class, 'deposit'])->name('user.deposit');
        Route::post('/user/deposit', [App\Http\Controllers\UserController::class, 'storeDeposit'])->name('user.deposit.store');
        Route::get('/user/deposit-history', [App\Http\Controllers\UserController::class, 'riwayatDeposit'])->name('user.deposit.history');
    });

    // UI Dev Routes (Aliases for backward compatibility if needed)
    Route::get('/login-ui', fn () => redirect()->route('login'));
    Route::get('/register-ui', fn () => redirect()->route('register'));

    // Smart Search API
    Route::get('/api/v1/search', [\App\Http\Controllers\Api\SearchController::class, 'search'])->name('api.search');

    // JWT Test Routes
    Route::prefix('api/jwt')->group(function () {
        Route::get('/generate', function (\App\Services\JwtService $jwt) {
            $token = $jwt->generateToken(['user_id' => 1, 'email' => 'admin@princepay.com']);

            return response()->json(['token' => $token]);
        });

        Route::get('/verify', function (\Illuminate\Http\Request $request) {
            return response()->json([
                'message' => 'Token is valid!',
                'data' => $request->attributes->get('jwt_payload'),
            ]);
        })->middleware('jwt');
    });

    // JWKS Endpoint
    Route::get('/.well-known/jwks.json', [App\Http\Controllers\Api\JwksController::class, 'index']);

});
