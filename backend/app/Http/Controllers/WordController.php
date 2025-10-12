<?php

namespace App\Http\Controllers;

use App\Models\Word;
use App\Services\DictionaryApiService;
use App\Services\ImageApiService;
use App\Http\Resources\WordResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WordController extends Controller
{
    public function __construct(
        private DictionaryApiService $dictionaryService,
        private ImageApiService $imageService
    ) {}

    /**
     * Get words by level with pagination
     */
    public function getByLevel(Request $request, string $level)
    {
        $request->validate([
            'per_page' => 'integer|min:1|max:100',
            'difficulty' => 'integer|min:1|max:5',
        ]);

        $query = Word::byLevel(strtoupper($level));

        if ($request->has('difficulty')) {
            $query->byDifficulty($request->difficulty);
        }

        $words = $query->inRandomOrder()
            ->paginate($request->get('per_page', 20));

        return WordResource::collection($words);
    }

    /**
     * Get single word details
     */
    public function show(int $id)
    {
        $word = Word::findOrFail($id);

        return new WordResource($word);
    }

    /**
     * Fetch word from external API and optionally save it
     */
    public function fetchWord(Request $request)
    {
        $request->validate([
            'word' => 'required|string|max:100',
            'level' => 'required|in:A1,A2,B1,B2,C1,C2',
            'difficulty' => 'integer|min:1|max:5',
        ]);

        $wordText = strtolower(trim($request->word));

        // Check if word already exists
        $existingWord = Word::where('word', $wordText)->first();
        if ($existingWord) {
            return new WordResource($existingWord);
        }

        // Fetch from API
        $wordData = $this->dictionaryService->fetchWordData($wordText);
        
        if (!$wordData) {
            return response()->json([
                'error' => 'Word not found in dictionary'
            ], 404);
        }

        // Fetch image
        $imageUrl = $this->imageService->fetchImage($wordText);
        $wordData['image_url'] = $imageUrl;

        // Save to database
        $word = Word::create([
            'word' => $wordText,
            'level' => $request->level,
            'difficulty' => $request->get('difficulty', 1),
            'definition' => $wordData['definition'],
            'phonetic' => $wordData['phonetic'],
            'audio_url' => $wordData['audio_url'],
            'image_url' => $imageUrl,
            'example_sentence' => $wordData['example_sentence'],
            'meanings' => $wordData['meanings'],
            'synonyms' => $wordData['synonyms'],
        ]);

        return (new WordResource($word))->response()->setStatusCode(201);
    }

    /**
     * Get random words for initial training
     */
    public function getRandomWords(Request $request)
    {
        $request->validate([
            'level' => 'required|in:A1,A2,B1,B2,C1,C2',
            'count' => 'integer|min:1|max:50',
        ]);

        $words = Word::byLevel($request->level)
            ->inRandomOrder()
            ->limit($request->get('count', 10))
            ->get();

        return WordResource::collection($words);
    }

    /**
     * Get single random word for training
     */
    public function getRandom(Request $request)
    {
        $request->validate([
            'level' => 'required|in:A1,A2,B1,B2,C1,C2',
        ]);

        $word = Word::where('level', strtoupper($request->input('level')))
            ->inRandomOrder()
            ->first();

        if (!$word) {
            return response()->json([
                'error' => 'No words found for this level'
            ], 404);
        }

        return new WordResource($word);
    }

    /**
     * Fetch and store a new random word for a level on-demand (UNLIMITED WORDS)
     * Always tries to fetch new words from the expanded 100-word list
     */
    public function fetchRandomForLevel(Request $request, string $level)
    {
        $request->validate([
            'level' => 'nullable|in:A1,A2,B1,B2,C1,C2',
        ]);

        $level = strtoupper($level);

        // Fetch word list from seeder (now has 100 words per level)
        $seeder = new \Database\Seeders\WordSeeder();
        $wordList = $seeder->getWordsForLevel($level);
        
        // Shuffle to get random order
        shuffle($wordList);
        
        // Try to find and fetch new words first
        foreach ($wordList as $wordText) {
            if (!Word::where('word', $wordText)->exists()) {
                $wordData = $this->dictionaryService->fetchWordData($wordText);
                
                if ($wordData) {
                    $imageUrl = $this->imageService->fetchImage($wordText);
                    
                    $word = Word::create([
                        'word' => $wordText,
                        'level' => $level,
                        'difficulty' => $wordData['difficulty'] ?? 3,
                        'definition' => $wordData['definition'],
                        'phonetic' => $wordData['phonetic'],
                        'audio_url' => $wordData['audio_url'],
                        'image_url' => $imageUrl,
                        'example_sentence' => $wordData['example_sentence'],
                        'meanings' => $wordData['meanings'],
                        'synonyms' => $wordData['synonyms'],
                    ]);
                    
                    return new WordResource($word);
                }
            }
        }

        // All words from list are already in database
        // Return a random existing word
        $word = Word::where('level', $level)->inRandomOrder()->first();
        
        if (!$word) {
            return response()->json([
                'error' => 'No words found for this level'
            ], 404);
        }
        
        return new WordResource($word);
    }
}

