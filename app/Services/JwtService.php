<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;
use Illuminate\Support\Facades\Storage;

class JwtService
{
    /**
     * @var string
     */
    protected $privateKey;

    /**
     * @var string
     */
    protected $publicKey;

    /**
     * @var string
     */
    protected $algorithm;

    public function __construct()
    {
        // Load RSA keys
        $this->privateKey = file_get_contents(storage_path('app/private.pem'));
        $this->publicKey = file_get_contents(storage_path('app/public.pem'));

        $this->algorithm = 'RS256';
    }

    /**
     * Generate a new JWT token using RS256
     *
     * @param array $payload
     * @param int $expiry Seconds from now
     * @return string
     */
    public function generateToken(array $payload, int $expiry = 3600): string
    {
        $now = time();
        $payload['iat'] = $now;
        $payload['exp'] = $now + $expiry;
        $payload['iss'] = env('APP_URL', 'https://www.neonflux.my.id');

        return JWT::encode($payload, $this->privateKey, $this->algorithm);
    }

    /**
     * Decode and validate a JWT token using RS256 public key
     *
     * @param string $token
     * @return object|null
     */
    public function decodeToken(string $token)
    {
        try {
            return JWT::decode($token, new Key($this->publicKey, $this->algorithm));
        } catch (Exception $e) {
            // Handle expired or invalid token
            return null;
        }
    }

    /**
     * Get the public key details for JWKS
     *
     * @return array
     */
    public function getPublicKeyDetails(): array
    {
        $details = openssl_pkey_get_details(openssl_pkey_get_public($this->publicKey));
        return $details['rsa'];
    }
}
