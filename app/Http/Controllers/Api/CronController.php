<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\SiteSetting;

class CronController extends Controller
{
    /**
     * Trigger Tokovoucher synchronization via HTTP.
     * Useful for services like cron-job.org
     */
    public function syncTokovoucher(Request $request, $token)
    {
        $configToken = config('app.cron_token') ?? env('CRON_TOKEN');

        if (!$configToken || $token !== $configToken) {
            Log::warning('Unauthorized Cron Attempt detected.', [
                'ip' => $request->ip(),
                'token_received' => $token
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Invalid token.'
            ], 403);
        }

        try {
            Log::info('Cron Job: Starting TokoVoucher Sync...');

            // Execute the Artisan command
            Artisan::call('sync:tokovoucher');
            $output = Artisan::output();

            Log::info('Cron Job: TokoVoucher Sync Finished.', ['output' => $output]);

            return response()->json([
                'status' => 'success',
                'message' => 'Synchronization triggered successfully.',
                'details' => trim($output)
            ]);

        } catch (\Exception $e) {
            Log::error('Cron Job Failed:', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Synchronization failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Register a new cron job to cron-job.org via API
     */
    public function registerToCronJobOrg(Request $request)
    {
        $apiKey = $request->input('cron_job_api_key');

        if (!$apiKey) {
            return back()->with('error', 'API Key cron-job.org diperlukan.');
        }

        // Save API Key to settings
        SiteSetting::updateOrCreate(['key' => 'cronjob_api_key'], ['value' => $apiKey]);

        $token = config('app.cron_token') ?? env('CRON_TOKEN');
        $callbackUrl = $request->getSchemeAndHttpHost() . '/cron/sync-tokovoucher/' . $token;

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->put('https://api.cron-job.org/jobs', [
                    'job' => [
                        'url' => $callbackUrl,
                        'enabled' => true,
                        'title' => 'PrincePay - TokoVoucher Auto Sync',
                        'saveResponses' => true,
                        'schedule' => [
                            'timezone' => 'Asia/Jakarta',
                            'expiresAt' => 0,
                            'hours' => [-1],
                            'mdays' => [-1],
                            'minutes' => [0, 15, 30, 45], // Setiap 15 menit
                            'months' => [-1],
                            'wdays' => [-1]
                        ]
                    ]
                ]);

            if ($response->successful()) {
                $jobId = $response->json('jobId');
                SiteSetting::updateOrCreate(['key' => 'cronjob_job_id'], ['value' => $jobId]);
                return back()->with('success', 'Berhasil mendaftarkan jadwal otomatis ke cron-job.org! Job ID: ' . $jobId);
            }

            $errorData = $response->json();
            $detailedError = $errorData['message'] ?? 'Unknown Error';

            // Helpful hint for 403
            if ($response->status() === 403) {
                $detailedError = "Akses Ditolak (IP Restriction). Pastikan kolom 'Allowed IP addresses' di Console cron-job.org dalam keadaan KOSONG.";
            }

            Log::error('cron-job.org Registration Failed:', [
                'status' => $response->status(),
                'response' => $errorData,
                'url' => $callbackUrl
            ]);

            return back()->with('error', 'Gagal mendaftar (HTTP ' . $response->status() . '): ' . $detailedError);

        } catch (\Exception $e) {

        } catch (\Exception $e) {
            Log::error('Cron Job Registration Exception:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}
