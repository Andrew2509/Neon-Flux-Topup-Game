<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\JwtService;
use Illuminate\Http\JsonResponse;

class JwksController extends Controller
{
    protected $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    /**
     * Return the JSON Web Key Set (JWKS)
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $details = $this->jwtService->getPublicKeyDetails();

        // Standard JWKS format
        // n and e must be Base64URL encoded
        return response()->json([
            'keys' => [
                [
                    'kty' => 'RSA',
                    'use' => 'sig',
                    'alg' => 'RS256',
                    'kid' => 'princepay-key-1', // You can change this ID
                    'n' => $this->base64UrlEncode($details['n']),
                    'e' => $this->base64UrlEncode($details['e']),
                ]
            ]
        ]);
    }

    /**
     * Base64URL encoding helper
     *
     * @param string $data
     * @return string
     */
    private function base64UrlEncode(string $data): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }
}
