<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class DictionaryApiService
{
    private const DICTIONARY_API_URL = 'https://api.dictionaryapi.dev/api/v2/entries/en/';
    private const CACHE_TTL = 604800; // 7 days (aggressive caching to minimize API calls)
    private const RATE_LIMIT_MAX = 100; // Max requests per minute

    public function fetchWordData(string $word): ?array
    {
        $cacheKey = "word_data_{$word}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($word) {
            // Rate limiting protection (skip for CLI/seeding)
            $rateLimitKey = 'dictionary_api_' . (request()?->ip() ?? 'cli');
            
            // Skip rate limiting for CLI operations (seeding)
            if (php_sapi_name() !== 'cli') {
                if (RateLimiter::tooManyAttempts($rateLimitKey, self::RATE_LIMIT_MAX)) {
                    Log::warning("Rate limit exceeded for Dictionary API");
                    return null;
                }
                
                RateLimiter::hit($rateLimitKey, 60); // 100 requests per minute
            }

            try {
                $response = Http::timeout(10)->get(self::DICTIONARY_API_URL . $word);

                if ($response->successful() && !empty($response->json())) {
                    $data = $response->json()[0] ?? null;
                    
                    if ($data) {
                        return $this->parseApiResponse($data);
                    }
                }
            } catch (\Exception $e) {
                Log::error("Dictionary API error for word '{$word}': " . $e->getMessage());
            }

            return null;
        });
    }

    private function parseApiResponse(array $data): array
    {
        $meanings = $data['meanings'] ?? [];
        $firstMeaning = $meanings[0] ?? null;
        $definition = $firstMeaning['definitions'][0]['definition'] ?? '';
        $example = $firstMeaning['definitions'][0]['example'] ?? null;

        // Extract phonetics
        $phonetic = $data['phonetic'] ?? '';
        $audioUrl = null;
        
        if (isset($data['phonetics']) && is_array($data['phonetics'])) {
            foreach ($data['phonetics'] as $phoneticData) {
                if (!empty($phoneticData['audio'])) {
                    $audioUrl = $phoneticData['audio'];
                    if (empty($phonetic) && !empty($phoneticData['text'])) {
                        $phonetic = $phoneticData['text'];
                    }
                    break;
                }
            }
        }

        // Extract synonyms
        $synonyms = [];
        foreach ($meanings as $meaning) {
            if (isset($meaning['synonyms']) && is_array($meaning['synonyms'])) {
                $synonyms = array_merge($synonyms, $meaning['synonyms']);
            }
        }

        // Calculate difficulty based on word length
        $wordText = $data['word'] ?? '';
        $difficulty = $this->calculateDifficulty($wordText);

        return [
            'word' => $wordText,
            'phonetic' => $phonetic,
            'audio_url' => $audioUrl,
            'definition' => $definition,
            'example_sentence' => $example,
            'meanings' => $meanings,
            'synonyms' => array_unique(array_slice($synonyms, 0, 5)),
            'difficulty' => $difficulty,
        ];
    }

    private function calculateDifficulty(string $word): int
    {
        $wordLength = strlen($word);
        
        // Longer words = higher difficulty
        if ($wordLength <= 4) return 1;
        if ($wordLength <= 6) return 2;
        if ($wordLength <= 8) return 3;
        if ($wordLength <= 10) return 4;
        return 5;
    }

    public function fetchMultipleWords(array $words): array
    {
        $results = [];

        foreach ($words as $word) {
            $data = $this->fetchWordData($word);
            if ($data) {
                $results[] = $data;
            }
        }

        return $results;
    }
}

