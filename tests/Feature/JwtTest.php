<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\JwtService;

class JwtTest extends TestCase
{
    /**
     * Test JWT generation and successful verification.
     */
    public function test_jwt_generation_and_verification(): void
    {
        $jwtService = new JwtService();
        $payload = ['user_id' => 999, 'email' => 'test@example.com'];
        $token = $jwtService->generateToken($payload);

        $this->assertIsString($token);

        $response = $this->getJson('/api/jwt/verify', [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Token is valid!')
            ->assertJsonPath('data.user_id', 999);
    }

    /**
     * Test failure with invalid token.
     */
    public function test_jwt_verification_fails_with_invalid_token(): void
    {
        $response = $this->getJson('/api/jwt/verify', [
            'Authorization' => 'Bearer invalid-token'
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Invalid or expired token');
    }

    /**
     * Test failure with missing token.
     */
    public function test_jwt_verification_fails_without_token(): void
    {
        $response = $this->getJson('/api/jwt/verify');

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Token not provided');
    }
}
