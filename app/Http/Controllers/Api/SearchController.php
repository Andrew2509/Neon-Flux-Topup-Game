<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Search for games (categories).
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $results = Category::where('status', 'Aktif')
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('slug', 'LIKE', "%{$query}%")
                  ->orWhere('type', 'LIKE', "%{$query}%");
            })
            ->select('id', 'name', 'slug', 'icon', 'type', 'platform')
            ->orderBy('is_popular', 'desc')
            ->limit(8)
            ->get()
            ->map(function($game) {
                return [
                    'name' => $game->name,
                    'slug' => route('topup.game', $game->slug),
                    'icon' => $game->icon ?: 'https://ui-avatars.com/api/?name=' . urlencode($game->name) . '&background=random&color=fff',
                    'type' => $game->type,
                    'platform' => $game->platform,
                ];
            });

        return response()->json($results);
    }
}
