<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $provider = \App\Models\Provider::where('name', 'like', '%whatsapp%')
            ->orWhere('name', 'like', '%orbit%')
            ->first();

        $this->baseUrl = env('ORBIT_WA_BASE_URL', 'https://orbit-whatsapp-api.vercel.app/api/v1');
        $this->apiKey = $provider->api_key ?? env('ORBIT_WA_API_KEY');
    }

    /**
     * Send a text message to a WhatsApp number.
     *
     * @param string $to
     * @param string $message
     * @return array
     */
    public function sendMessage($to, $message)
    {
        // Format number: ensure it starts with country code (e.g., 62 for Indonesia)
        $to = $this->formatNumber($to);

        try {
            $response = Http::withToken($this->apiKey)
                ->post("{$this->baseUrl}/messages/send", [
                    'to' => $to,
                    'message' => $message,
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            Log::error('Orbit WA API Error: ' . $response->body());
            return [
                'success' => false,
                'error' => $response->json('message') ?? 'Unknown error',
            ];
        } catch (\Exception $e) {
            Log::error('Orbit WA Service Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Format phone number to international format (starting with 62 for Indonesia).
     *
     * @param string $number
     * @return string
     */
    protected function formatNumber($number)
    {
        $number = preg_replace('/[^0-9]/', '', $number);

        if (str_starts_with($number, '0')) {
            $number = '62' . substr($number, 1);
        } elseif (str_starts_with($number, '8')) {
            $number = '62' . $number;
        }

        return $number;
    }
}
