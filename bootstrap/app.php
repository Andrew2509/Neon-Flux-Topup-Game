<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(prepend: [
            \App\Http\Middleware\RedirectToWww::class,
        ]);

        $middleware->alias([
            'jwt' => \App\Http\Middleware\JwtMiddleware::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'login',
            'register',
            'logout',
            'verify-otp',
            'resend-otp',
            'forgot-password',
            'reset-password-phone',
            '.well-known/jwks.json',
            'api/duitku/callback',
            'api/ipaymu/callback',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
