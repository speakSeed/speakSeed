<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ImageApiService
{
    private const UNSPLASH_API_URL = 'https://api.unsplash.com/search/photos';
    private const PEXELS_API_URL = 'https://api.pexels.com/v1/search';
    private const CACHE_TTL = 604800; // 7 days

    public function fetchImage(string $query): ?string
    {
        $cacheKey = "image_{$query}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($query) {
            // Try Unsplash first
            $imageUrl = $this->fetchFromUnsplash($query);
            
            // Fallback to Pexels
            if (!$imageUrl) {
                $imageUrl = $this->fetchFromPexels($query);
            }

            // Fallback to null - let frontend handle with letter icon
            // (via.placeholder.com has DNS issues)
            return $imageUrl;
        });
    }

    private function fetchFromUnsplash(string $query): ?string
    {
        $apiKey = env('UNSPLASH_ACCESS_KEY');
        
        if (!$apiKey) {
            return null;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders(['Authorization' => "Client-ID {$apiKey}"])
                ->get(self::UNSPLASH_API_URL, [
                    'query' => $query,
                    'per_page' => 1,
                    'orientation' => 'landscape',
                ]);

            if ($response->successful()) {
                $results = $response->json()['results'] ?? [];
                if (!empty($results)) {
                    return $results[0]['urls']['regular'] ?? null;
                }
            }
        } catch (\Exception $e) {
            Log::error("Unsplash API error: " . $e->getMessage());
        }

        return null;
    }

    private function fetchFromPexels(string $query): ?string
    {
        $apiKey = env('PEXELS_API_KEY');
        
        if (!$apiKey) {
            return null;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders(['Authorization' => $apiKey])
                ->get(self::PEXELS_API_URL, [
                    'query' => $query,
                    'per_page' => 1,
                    'orientation' => 'landscape',
                ]);

            if ($response->successful()) {
                $photos = $response->json()['photos'] ?? [];
                if (!empty($photos)) {
                    return $photos[0]['src']['large'] ?? null;
                }
            }
        } catch (\Exception $e) {
            Log::error("Pexels API error: " . $e->getMessage());
        }

        return null;
    }
}

