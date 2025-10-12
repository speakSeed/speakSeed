<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Word;
use App\Services\DictionaryApiService;
use App\Services\ImageApiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WordController extends Controller
{
    public function __construct(
        private DictionaryApiService $dictionaryService,
        private ImageApiService $imageService
    ) {}
    
    /**
     * Get words by level (paginated)
     */
    public function getByLevel(Request $request, string $level): JsonResponse
    {
        $request->validate([
            'per_page' => 'integer|min:1|max:100',
        ]);
        
        $perPage = $request->input('per_page', 10);
        
        $words = Word::where('level', strtoupper($level))
            ->orderBy('difficulty')
            ->paginate($perPage);
        
        return response()->json($words);
    }
    
    /**
     * Get single word details
     */
    public function show(int $id): JsonResponse
    {
        $word = Word::findOrFail($id);
        return response()->json($word);
    }
    
    /**
     * Fetch word data from external API and store it
     */
    public function fetchAndStore(Request $request): JsonResponse
    {
        $request->validate([
            'word' => 'required|string|max:255',
            'level' => 'required|in:A1,A2,B1,B2,C1,C2',
        ]);
        
        $wordText = strtolower($request->input('word'));
        $level = $request->input('level');
        
        // Check if word already exists
        $existingWord = Word::where('word', $wordText)->first();
        if ($existingWord) {
            return response()->json($existingWord);
        }
        
        // Fetch from dictionary API
        $wordData = $this->dictionaryService->getWordData($wordText);
        if (!$wordData) {
            return response()->json([
                'error' => 'Word not found in dictionary'
            ], 404);
        }
        
        // Fetch image
        $imageUrl = $this->imageService->getImageUrl($wordText);
        
        // Create word
        $word = Word::create([
            'word' => $wordText,
            'level' => $level,
            'definition' => $wordData['definition'],
            'phonetic' => $wordData['phonetic'],
            'audio_url' => $wordData['audio_url'],
            'image_url' => $imageUrl,
            'example_sentence' => $wordData['example_sentence'],
            'difficulty' => 1,
        ]);
        
        return response()->json($word, 201);
    }
    
    /**
     * Get random word for training
     */
    public function random(Request $request): JsonResponse
    {
        $request->validate([
            'level' => 'required|in:A1,A2,B1,B2,C1,C2',
        ]);
        
        $level = $request->input('level');
        
        $word = Word::where('level', $level)
            ->inRandomOrder()
            ->first();
        
        if (!$word) {
            return response()->json([
                'error' => 'No words found for this level'
            ], 404);
        }
        
        return response()->json($word);
    }
}
