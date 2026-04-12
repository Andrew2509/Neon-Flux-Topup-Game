<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->is('admin*') && !$request->is('api*')) {
            $ip = $request->ip();
            $userAgent = $request->userAgent();
            $url = $request->fullUrl();
            $userId = auth()->id();

            // Find or create visitor for this IP today
            $visitor = \App\Models\Visitor::where('ip_address', $ip)
                ->whereDate('created_at', now()->toDateString())
                ->first();

            if (!$visitor) {
                // Fetch Geolocation
                $locationData = $this->getLocationData($ip);

                $visitor = \App\Models\Visitor::create([
                    'ip_address' => $ip,
                    'user_agent' => $userAgent,
                    'url' => $url,
                    'user_id' => $userId,
                    'city' => $locationData['city'] ?? 'Unknown',
                    'region' => $locationData['region'] ?? 'Unknown',
                    'country' => $locationData['country'] ?? 'Unknown',
                    'country_code' => $locationData['countryCode'] ?? '??',
                    'last_active_at' => now(),
                ]);
            } else {
                // Just update active timestamp and url
                $visitor->update([
                    'url' => $url,
                    'user_id' => $userId ?: $visitor->user_id,
                    'last_active_at' => now(),
                ]);
            }

            // Cleanup task: occasionally delete old history (e.g. 1% of requests)
            if (rand(1, 100) === 1) {
                \App\Models\Visitor::where('created_at', '<', now()->subDays(30))->delete();
            }
        }

        return $next($request);
    }

    private function getLocationData($ip)
    {
        if ($ip === '127.0.0.1') {
            return ['city' => 'Localhost', 'region' => 'Local', 'country' => 'Local', 'countryCode' => '??'];
        }

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(3)->get("http://ip-api.com/json/{$ip}");
            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            // Log or ignore
        }

        return [];
    }
}
