<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\JwtService;
use Symfony\Component\HttpFoundation\Response;

class JwtMiddleware
{
    protected $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Token not provided'], 401);
        }

        $decoded = $this->jwtService->decodeToken($token);

        if (!$decoded) {
            return response()->json(['message' => 'Invalid or expired token'], 401);
        }

        // Add decoded data to request for easier access in controllers
        $request->attributes->set('jwt_payload', $decoded);

        return $next($request);
    }
}
