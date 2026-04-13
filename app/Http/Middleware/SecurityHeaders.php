<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Mencegah Clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        
        // Mencegah MIME sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // Proteksi XSS Dasar (Legacy support)
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Kebijakan Referrer
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Permissions Policy (Minimal)
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // HSTS (Mewajibkan HTTPS) - Hanya jika sudah HTTPS
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        return $response;
    }
}
