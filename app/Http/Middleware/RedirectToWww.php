<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mengalihkan permintaan ke domain apex (tanpa www) ke host di APP_URL bila APP_URL memakai www.
 * Aktifkan dengan FORCE_WWW=true (disarankan hanya di production).
 */
class RedirectToWww
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('app.force_www')) {
            return $next($request);
        }

        if (! app()->environment('production')) {
            return $next($request);
        }

        $host = strtolower($request->getHost());
        if ($host === 'localhost' || str_ends_with($host, '.localhost') || $host === '127.0.0.1') {
            return $next($request);
        }

        $canonical = parse_url((string) config('app.url'), PHP_URL_HOST);
        if (! is_string($canonical) || $canonical === '') {
            return $next($request);
        }

        $canonical = strtolower($canonical);
        if (! str_starts_with($canonical, 'www.')) {
            return $next($request);
        }

        $apex = substr($canonical, 4);
        if ($host !== $apex) {
            return $next($request);
        }

        $scheme = parse_url((string) config('app.url'), PHP_URL_SCHEME) ?: $request->getScheme();
        $target = $scheme.'://'.$canonical.$request->getRequestUri();

        return redirect()->away($target, 301);
    }
}
