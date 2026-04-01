<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }


        if (!app()->runningInConsole() || \Illuminate\Support\Facades\Schema::hasTable('payment_methods')) {
            \Illuminate\Support\Facades\View::share('footerPayments', \App\Models\PaymentMethod::where('status', 'Aktif')->take(12)->get());
        }

        // Define Auth Rate Limiter (5 attempts per minute)
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Define OTP Rate Limiter (3 attempts per minute)
        RateLimiter::for('otp', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });
    }
}
