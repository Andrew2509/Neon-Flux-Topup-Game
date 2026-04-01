<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LogoGeneratorController extends Controller
{
    public function index()
    {
        return view('admin.logo.index');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string',
        ]);

        $apiKey = env('GOOGLE_AI_KEY');
        if (!$apiKey) {
            return response()->json(['error' => 'API Key Google AI belum dikonfigurasi di file .env'], 500);
        }

        try {
            $response = Http::timeout(60)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/imagen-3.0-generate-001:predict?key={$apiKey}",
                [
                    'instances' => [
                        ['prompt' => $request->prompt]
                    ],
                    'parameters' => [
                        'sampleCount' => 1,
                    ],
                ]
            );

            if (!$response->successful()) {
                Log::error('Imagen API Error: ' . $response->body());
                return response()->json(['error' => 'Gagal menghubungi server AI: ' . ($response->json()['error']['message'] ?? 'Unknown Error')], 500);
            }

            $result = $response->json();
            
            if (isset($result['predictions']) && isset($result['predictions'][0]['bytesBase64Encoded'])) {
                return response()->json([
                    'image' => 'data:image/png;base64,' . $result['predictions'][0]['bytesBase64Encoded']
                ]);
            }

            return response()->json(['error' => 'Format gambar tidak valid dari AI'], 500);

        } catch (\Exception $e) {
            Log::error('Logo Generator Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }
}
