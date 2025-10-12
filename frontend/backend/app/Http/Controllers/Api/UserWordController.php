<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserWord;
use App\Models\Word;
use App\Services\SpacedRepetitionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserWordController extends Controller
{
    public function __construct(
        private SpacedRepetitionService $spacedRepetitionService
    ) {}
    
    /**
     * Get all user words
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
        ]);
        
        $sessionId = $request->input('session_id');
        
        $userWords = UserWord::with('word')
            ->where('session_id', $sessionId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json($userWords);
    }
    
    /**
     * Add word to learning list
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
            'word_id' => 'required|exists:words,id',
        ]);
        
        $sessionId = $request->input('session_id');
        $wordId = $request->input('word_id');
        
        // Check if word already added
        $existingUserWord = UserWord::where('session_id', $sessionId)
            ->where('word_id', $wordId)
            ->first();
        
        if ($existingUserWord) {
            return response()->json($existingUserWord);
        }
        
        $userWord = UserWord::create([
            'session_id' => $sessionId,
            'word_id' => $wordId,
            'status' => 'learning',
            'repetitions' => 0,
            'interval' => 1,
            'ease_factor' => 2.5,
        ]);
        
        $userWord->load('word');
        
        return response()->json($userWord, 201);
    }
    
    /**
     * Remove word from learning list
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
        ]);
        
        $sessionId = $request->input('session_id');
        
        $userWord = UserWord::where('id', $id)
            ->where('session_id', $sessionId)
            ->firstOrFail();
        
        $userWord->delete();
        
        return response()->json(['message' => 'Word removed successfully']);
    }
    
    /**
     * Update word review (spaced repetition)
     */
    public function updateReview(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
            'quality' => 'required|integer|min:0|max:5',
        ]);
        
        $sessionId = $request->input('session_id');
        $quality = $request->input('quality');
        
        $userWord = UserWord::where('id', $id)
            ->where('session_id', $sessionId)
            ->firstOrFail();
        
        $userWord = $this->spacedRepetitionService->calculateNextReview($userWord, $quality);
        $userWord->save();
        
        $userWord->load('word');
        
        return response()->json($userWord);
    }
    
    /**
     * Get words for review
     */
    public function getForReview(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
        ]);
        
        $sessionId = $request->input('session_id');
        
        $words = $this->spacedRepetitionService->getWordsForReview($sessionId);
        
        return response()->json($words);
    }
}
