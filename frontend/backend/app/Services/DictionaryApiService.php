<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DictionaryApiService
{
    private string $baseUrl;
    
    public function __construct()
    {
        $this->baseUrl = config('services.dictionary.url', 'https://api.dictionaryapi.dev/api/v2/entries/en');
    }
    
    /**
     * Fetch word data from dictionary API
     * 
     * @param string $word
     * @return array|null
     */
    public function getWordData(string $word): ?array
    {
        $cacheKey = "dictionary_word_{$word}";
        
        return Cache::remember($cacheKey, now()->addDays(7), function () use ($word) {
            try {
                $response = Http::get("{$this->baseUrl}/{$word}");
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (!empty($data) && is_array($data)) {
                        return $this->formatWordData($data[0]);
                    }
                }
                
                return null;
            } catch (\Exception $e) {
                Log::error("Dictionary API error for word: {$word}", ['error' => $e->getMessage()]);
                return null;
            }
        });
    }
    
    /**
     * Format API response data
     * 
     * @param array $data
     * @return array
     */
    private function formatWordData(array $data): array
    {
        $meaning = $data['meanings'][0] ?? [];
        $definition = $meaning['definitions'][0] ?? [];
        
        return [
            'word' => $data['word'] ?? '',
            'phonetic' => $data['phonetic'] ?? ($data['phonetics'][0]['text'] ?? null),
            'audio_url' => $this->extractAudioUrl($data['phonetics'] ?? []),
            'definition' => $definition['definition'] ?? '',
            'example_sentence' => $definition['example'] ?? null,
        ];
    }
    
    /**
     * Extract audio URL from phonetics array
     * 
     * @param array $phonetics
     * @return string|null
     */
    private function extractAudioUrl(array $phonetics): ?string
    {
        foreach ($phonetics as $phonetic) {
            if (!empty($phonetic['audio'])) {
                return $phonetic['audio'];
            }
        }
        
        return null;
    }
}
