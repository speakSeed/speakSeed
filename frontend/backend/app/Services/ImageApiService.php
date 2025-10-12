<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ImageApiService
{
    private ?string $unsplashKey;
    private ?string $pexelsKey;
    
    public function __construct()
    {
        $this->unsplashKey = config('services.unsplash.key');
        $this->pexelsKey = config('services.pexels.key');
    }
    
    /**
     * Fetch image URL for a word
     * 
     * @param string $query
     * @return string|null
     */
    public function getImageUrl(string $query): ?string
    {
        $cacheKey = "image_url_{$query}";
        
        return Cache::remember($cacheKey, now()->addDays(7), function () use ($query) {
            // Try Unsplash first
            if ($this->unsplashKey) {
                $url = $this->getUnsplashImage($query);
                if ($url) {
                    return $url;
                }
            }
            
            // Try Pexels as fallback
            if ($this->pexelsKey) {
                $url = $this->getPexelsImage($query);
                if ($url) {
                    return $url;
                }
            }
            
            // Return a placeholder image if no API keys are available
            return "https://via.placeholder.com/400x300?text=" . urlencode($query);
        });
    }
    
    /**
     * Fetch image from Unsplash
     * 
     * @param string $query
     * @return string|null
     */
    private function getUnsplashImage(string $query): ?string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Client-ID {$this->unsplashKey}",
            ])->get('https://api.unsplash.com/search/photos', [
                'query' => $query,
                'per_page' => 1,
                'orientation' => 'landscape',
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                return $data['results'][0]['urls']['regular'] ?? null;
            }
        } catch (\Exception $e) {
            Log::error("Unsplash API error", ['query' => $query, 'error' => $e->getMessage()]);
        }
        
        return null;
    }
    
    /**
     * Fetch image from Pexels
     * 
     * @param string $query
     * @return string|null
     */
    private function getPexelsImage(string $query): ?string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->pexelsKey,
            ])->get('https://api.pexels.com/v1/search', [
                'query' => $query,
                'per_page' => 1,
                'orientation' => 'landscape',
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                return $data['photos'][0]['src']['large'] ?? null;
            }
        } catch (\Exception $e) {
            Log::error("Pexels API error", ['query' => $query, 'error' => $e->getMessage()]);
        }
        
        return null;
    }
}
