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
    Route::get('/payment/{order_id}', [App\Http\Controllers\TransactionController::class, 'showPaymentPage'])->name('order.payment');
    Route::post('/order/{order_id}/cancel', [App\Http\Controllers\TransactionController::class, 'cancelOrder'])->name('order.cancel');
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

    Route::post('/api/check-id', [App\Http\Controllers\TransactionController::class, 'checkPlayerId'])
        ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
        ->name('topup.check-id');
    Route::post('/api/ipaymu/callback', [App\Http\Controllers\TransactionController::class, 'ipaymuCallback'])
        ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
        ->name('api.ipaymu.callback');
    Route::match(['get', 'post'], '/api/tokovoucher/webhook', [App\Http\Controllers\Api\WebhookController::class, 'tokovoucher'])->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

    Route::get('/cron/sync-tokovoucher/{token}', [App\Http\Controllers\Api\CronController::class, 'syncTokovoucher'])->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
    Route::post('/admin/cron/register', [App\Http\Controllers\Api\CronController::class, 'registerToCronJobOrg'])->name('admin.cron.register');

    // Admin Routes
    // Admin Dashboard
    Route::middleware(['auth', 'permission:akses-dashboard'])->get('/admin-dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');

    // Admin Orders
    Route::middleware(['auth', 'permission:kelola-pesanan'])->group(function () {
        Route::get('/admin/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.orders');
        Route::post('/admin/orders/{order}/fulfill-tokovoucher', [App\Http\Controllers\Admin\OrderController::class, 'fulfillTokovoucher'])->name('admin.orders.fulfill_tokovoucher');
        Route::delete('/admin/orders/mass-destroy', [App\Http\Controllers\Admin\OrderController::class, 'massDestroy'])->name('admin.orders.mass_destroy');
        Route::delete('/admin/orders/destroy-all', [App\Http\Controllers\Admin\OrderController::class, 'destroyAll'])->name('admin.orders.destroy_all');
    });
    // Admin Members
    Route::middleware(['auth', 'permission:kelola-member'])->group(function () {
        Route::get('/admin/members', [App\Http\Controllers\Admin\MemberController::class, 'index'])->name('admin.members');
        Route::get('/admin/members/create', [App\Http\Controllers\Admin\MemberController::class, 'create'])->name('admin.members.create');
        Route::post('/admin/members', [App\Http\Controllers\Admin\MemberController::class, 'store'])->name('admin.members.store');
        Route::get('/admin/members/{user}', [App\Http\Controllers\Admin\MemberController::class, 'show'])->name('admin.members.show');
        Route::get('/admin/members/{user}/edit', [App\Http\Controllers\Admin\MemberController::class, 'edit'])->name('admin.members.edit');
        Route::put('/admin/members/{user}', [App\Http\Controllers\Admin\MemberController::class, 'update'])->name('admin.members.update');
        Route::delete('/admin/members/{user}', [App\Http\Controllers\Admin\MemberController::class, 'destroy'])->name('admin.members.destroy');
    });
    // Admin Deposits
    Route::middleware(['auth', 'permission:kelola-deposit'])->get('/admin/deposits', [App\Http\Controllers\Admin\DepositController::class, 'index'])->name('admin.deposits');

    // Admin Categories
    Route::middleware(['auth', 'permission:kelola-kategori'])->get('/admin/categories', [App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('admin.categories');

    // Admin Services
    Route::middleware(['auth', 'permission:kelola-layanan'])->group(function () {
        Route::get('/admin/services', [App\Http\Controllers\Admin\ServiceController::class, 'index'])->name('admin.services');
        Route::get('/admin/services/ajax-search', [App\Http\Controllers\Admin\ServiceController::class, 'ajaxSearch'])->name('admin.services.ajax_search');
        Route::get('/admin/services/sync-list', [App\Http\Controllers\Admin\ServiceController::class, 'getSyncList'])->name('admin.services.sync_list');
        Route::post('/admin/services/sync', [App\Http\Controllers\Admin\ServiceController::class, 'syncTokoVoucher'])->name('admin.services.sync');
        Route::post('/admin/services/{id}/toggle', [App\Http\Controllers\Admin\ServiceController::class, 'toggle'])->name('admin.services.toggle');
    });
    // Admin Packages
    Route::middleware(['auth', 'permission:kelola-paket'])->group(function () {
        Route::get('/admin/packages', [App\Http\Controllers\Admin\PackageController::class, 'index'])->name('admin.packages.index');
        Route::get('/admin/packages/operator/{id}', [App\Http\Controllers\Admin\PackageController::class, 'showOperator'])->name('admin.packages.operator');
        Route::get('/admin/packages/operator/{id}/edit', [App\Http\Controllers\Admin\PackageController::class, 'editOperator'])->name('admin.packages.operator.edit');
        Route::put('/admin/packages/operator/{id}', [App\Http\Controllers\Admin\PackageController::class, 'updateOperator'])->name('admin.packages.operator.update');
        Route::post('/admin/packages/operator/{id}/toggle', [App\Http\Controllers\Admin\PackageController::class, 'toggleOperatorStatus'])->name('admin.packages.operator.toggle');
        Route::get('/admin/packages/jenis/{id}', [App\Http\Controllers\Admin\PackageController::class, 'showJenis'])->name('admin.packages.jenis');
        Route::post('/admin/packages/jenis/{id}/toggle', [App\Http\Controllers\Admin\PackageController::class, 'toggleJenisStatus'])->name('admin.packages.jenis.toggle');
        Route::post('/admin/packages/services/{id}/toggle', [App\Http\Controllers\Admin\PackageController::class, 'toggleServiceStatus'])->name('admin.packages.services.toggle');
        Route::delete('/admin/packages/{package}', [App\Http\Controllers\Admin\PackageController::class, 'destroy'])->name('admin.packages.destroy');
    });
    // Admin Vouchers
    Route::middleware(['auth', 'permission:kelola-voucher'])->group(function () {
        Route::get('/admin/vouchers', [App\Http\Controllers\Admin\VoucherController::class, 'index'])->name('admin.vouchers');
        Route::get('/admin/vouchers/create', [App\Http\Controllers\Admin\VoucherController::class, 'create'])->name('admin.vouchers.create');
        Route::post('/admin/vouchers', [App\Http\Controllers\Admin\VoucherController::class, 'store'])->name('admin.vouchers.store');
        Route::get('/admin/vouchers/{voucher}', [App\Http\Controllers\Admin\VoucherController::class, 'show'])->name('admin.vouchers.show');
        Route::get('/admin/vouchers/{voucher}/edit', [App\Http\Controllers\Admin\VoucherController::class, 'edit'])->name('admin.vouchers.edit');
        Route::put('/admin/vouchers/{voucher}', [App\Http\Controllers\Admin\VoucherController::class, 'update'])->name('admin.vouchers.update');
        Route::delete('/admin/vouchers/{voucher}', [App\Http\Controllers\Admin\VoucherController::class, 'destroy'])->name('admin.vouchers.destroy');
    });
    // Admin Sliders
    Route::middleware(['auth', 'permission:kelola-slider'])->group(function () {
        Route::get('/admin/sliders', [App\Http\Controllers\Admin\SliderController::class, 'index'])->name('admin.sliders');
        Route::get('/admin/sliders/create', [App\Http\Controllers\Admin\SliderController::class, 'create'])->name('admin.sliders.create');
        Route::post('/admin/sliders', [App\Http\Controllers\Admin\SliderController::class, 'store'])->name('admin.sliders.store');
        Route::get('/admin/sliders/{slider}/edit', [App\Http\Controllers\Admin\SliderController::class, 'edit'])->name('admin.sliders.edit');
        Route::put('/admin/sliders/{slider}', [App\Http\Controllers\Admin\SliderController::class, 'update'])->name('admin.sliders.update');
        Route::delete('/admin/sliders/{slider}', [App\Http\Controllers\Admin\SliderController::class, 'destroy'])->name('admin.sliders.destroy');
    });
    // Admin Withdrawals
    Route::middleware(['auth', 'permission:kelola-penarikan'])->group(function () {
        Route::get('/admin/withdrawals/bank', [App\Http\Controllers\Admin\WithdrawalController::class, 'bankForm'])->name('admin.withdrawals.bank');
        Route::post('/admin/withdrawals/bank', [App\Http\Controllers\Admin\WithdrawalController::class, 'processBank'])->name('admin.withdrawals.bank.process');
        Route::get('/admin/withdrawals/ewallet', [App\Http\Controllers\Admin\WithdrawalController::class, 'ewalletForm'])->name('admin.withdrawals.ewallet');
        Route::post('/admin/withdrawals/ewallet', [App\Http\Controllers\Admin\WithdrawalController::class, 'processEwallet'])->name('admin.withdrawals.ewallet.process');
    });

    // Admin Payments
    Route::middleware(['auth', 'permission:kelola-pembayaran'])->group(function () {
        Route::get('/admin/payments', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('admin.payments');
        Route::post('/admin/payments/sync-ipaymu', [App\Http\Controllers\Admin\PaymentController::class, 'syncIPaymu'])->name('admin.payments.sync_ipaymu');
        Route::get('/admin/payments/create', [App\Http\Controllers\Admin\PaymentController::class, 'create'])->name('admin.payments.create');
        Route::post('/admin/payments', [App\Http\Controllers\Admin\PaymentController::class, 'store'])->name('admin.payments.store');
        Route::get('/admin/payments/{payment}/edit', [App\Http\Controllers\Admin\PaymentController::class, 'edit'])->name('admin.payments.edit');
        Route::put('/admin/payments/{payment}', [App\Http\Controllers\Admin\PaymentController::class, 'update'])->name('admin.payments.update');
        Route::post('/admin/payments/{payment}/toggle', [App\Http\Controllers\Admin\PaymentController::class, 'toggle'])->name('admin.payments.toggle');
        Route::delete('/admin/payments/{payment}', [App\Http\Controllers\Admin\PaymentController::class, 'destroy'])->name('admin.payments.destroy');
    });

    // Admin Ratings & Providers
    Route::middleware(['auth', 'permission:view-rating'])->get('/admin/ratings', [App\Http\Controllers\Admin\RatingController::class, 'index'])->name('admin.ratings');
    Route::middleware(['auth', 'permission:kelola-provider'])->group(function () {
        Route::get('/admin/providers', [App\Http\Controllers\Admin\ProviderController::class, 'index'])->name('admin.providers');
        Route::get('/admin/providers/create', [App\Http\Controllers\Admin\ProviderController::class, 'create'])->name('admin.providers.create');
        Route::post('/admin/providers', [App\Http\Controllers\Admin\ProviderController::class, 'store'])->name('admin.providers.store');
        Route::get('/admin/providers/{provider}/edit', [App\Http\Controllers\Admin\ProviderController::class, 'edit'])->name('admin.providers.edit');
        Route::put('/admin/providers/{provider}', [App\Http\Controllers\Admin\ProviderController::class, 'update'])->name('admin.providers.update');
        Route::delete('/admin/providers/{provider}', [App\Http\Controllers\Admin\ProviderController::class, 'destroy'])->name('admin.providers.destroy');
        Route::post('/admin/providers/{provider}/balance', [App\Http\Controllers\Admin\ProviderController::class, 'syncBalance'])->name('admin.providers.balance');
        Route::get('/admin/providers/{provider}/deposit', [App\Http\Controllers\Admin\ProviderController::class, 'showDepositForm'])->name('admin.providers.deposit.form');
        Route::post('/admin/providers/{provider}/deposit', [App\Http\Controllers\Admin\ProviderController::class, 'processDeposit'])->name('admin.providers.deposit.process');
    });
    // Admin TokoVoucher
    Route::middleware(['auth', 'permission:kelola-tokovoucher'])->group(function () {
        Route::get('/admin/tokovoucher/categories', [\App\Http\Controllers\Admin\TokovoucherController::class, 'categories'])->name('admin.tokovoucher.categories');
        Route::post('/admin/tokovoucher/categories/{id}/sync', [\App\Http\Controllers\Admin\TokovoucherController::class, 'syncCategory'])->name('admin.tokovoucher.sync');
        Route::post('/admin/tokovoucher/categories/{id}/toggle', [\App\Http\Controllers\Admin\TokovoucherController::class, 'toggleProviderCategory'])->name('admin.tokovoucher.toggle');
    });

    // Admin Logo Generator
    Route::middleware(['auth', 'permission:kelola-logo'])->group(function () {
        Route::get('/admin/logo-generator', [\App\Http\Controllers\Admin\LogoGeneratorController::class, 'index'])->name('admin.logo-generator.index');
        Route::post('/admin/logo-generator/generate', [\App\Http\Controllers\Admin\LogoGeneratorController::class, 'generate'])->name('admin.logo-generator.generate');
    });

    // Admin Settings
    Route::middleware(['auth', 'permission:kelola-setting'])->group(function () {
        Route::get('/admin/settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('admin.settings');
        Route::post('/admin/settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('admin.settings.update');
    });

    // Admin Visitor Tracking
    Route::middleware(['auth', 'permission:view-visitors'])->group(function () {
        Route::get('/admin/visitors', [App\Http\Controllers\Admin\VisitorController::class, 'index'])->name('admin.visitors');
    });

    // Management User & Role Routes
    Route::middleware(['auth', 'permission:manajemen-user-akses'])->prefix('admin/management')->name('admin.management.')->group(function () {
        // User Management
        Route::get('/user', [App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('user.index');
        Route::post('/user', [App\Http\Controllers\Admin\UserManagementController::class, 'store'])->name('user.store');
        Route::put('/user/{user}', [App\Http\Controllers\Admin\UserManagementController::class, 'update'])->name('user.update');
        Route::post('/user/{user}/toggle', [App\Http\Controllers\Admin\UserManagementController::class, 'toggleStatus'])->name('user.toggle');
        Route::delete('/user/{user}', [App\Http\Controllers\Admin\UserManagementController::class, 'destroy'])->name('user.destroy');

        // Role Management
        Route::get('/role', [App\Http\Controllers\Admin\RoleController::class, 'index'])->name('role.index');
        Route::get('/role/create', [App\Http\Controllers\Admin\RoleController::class, 'create'])->name('role.create');
        Route::post('/role', [App\Http\Controllers\Admin\RoleController::class, 'store'])->name('role.store');
        Route::get('/role/{role}/edit', [App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('role.edit');
        Route::put('/role/{role}', [App\Http\Controllers\Admin\RoleController::class, 'update'])->name('role.update');
        Route::delete('/role/{role}', [App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('role.destroy');
    });

    // Authentication Routes (Google Only)
    Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
    Route::get('/auth/google', [App\Http\Controllers\AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [App\Http\Controllers\AuthController::class, 'handleGoogleCallback']);
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

    // Profile Completion (WhatsApp)
    Route::middleware(['auth'])->group(function () {
        Route::get('/profile/complete', [App\Http\Controllers\UserController::class, 'showCompleteProfile'])->name('profile.complete');
        Route::post('/profile/complete', [App\Http\Controllers\UserController::class, 'storeCompleteProfile'])->name('profile.complete.store');
    });

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
    Route::get('/register-ui', fn () => redirect()->route('login'));

    // Smart Search API
    Route::get('/api/v1/search', [\App\Http\Controllers\Api\SearchController::class, 'search'])->name('api.search');



    // JWKS Endpoint
    Route::get('/.well-known/jwks.json', [App\Http\Controllers\Api\JwksController::class, 'index']);

});
