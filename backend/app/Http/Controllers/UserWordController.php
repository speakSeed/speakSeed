<?php

namespace App\Http\Controllers;

use App\Models\UserWord;
use App\Models\Word;
use App\Services\SpacedRepetitionService;
use App\Http\Resources\UserWordResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserWordController extends Controller
{
    public function __construct(
        private SpacedRepetitionService $spacedRepetitionService
    ) {}

    /**
     * Get user's saved words
     */
    public function index(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'status' => 'nullable|in:new,learning,mastered',
        ]);

        $sessionId = $request->input('session_id');
        $query = UserWord::where('session_id', $sessionId)->with('word');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $words = $query->orderBy('created_at', 'desc')->get();

        return UserWordResource::collection($words);
    }

    /**
     * Save word for learning
     */
    public function store(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'word_id' => 'required|exists:words,id',
        ]);

        // Check if already exists
        $existing = UserWord::where('session_id', $request->session_id)
            ->where('word_id', $request->word_id)
            ->first();

        if ($existing) {
            return new UserWordResource($existing->load('word'));
        }

        // Create new user word
        $userWord = new UserWord([
            'session_id' => $request->session_id,
            'word_id' => $request->word_id,
        ]);

        $userWord = $this->spacedRepetitionService->initializeWord($userWord);
        $userWord->save();

        return (new UserWordResource($userWord->load('word')))->response()->setStatusCode(201);
    }

    /**
     * Update word status/review
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'correct' => 'required|boolean',
            'time_spent' => 'integer|min:0',
            'attempts' => 'integer|min:1',
        ]);

        $userWord = UserWord::findOrFail($id);

        // Convert performance to quality score
        $quality = $this->spacedRepetitionService->performanceToQuality(
            $request->correct,
            $request->get('time_spent', 0),
            $request->get('attempts', 1)
        );

        // Update review schedule
        $userWord = $this->spacedRepetitionService->updateReviewSchedule($userWord, $quality);
        $userWord->save();

        return new UserWordResource($userWord->load('word'));
    }

    /**
     * Get words due for review
     */
    public function getDueForReview(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
        ]);

        $sessionId = $request->input('session_id');
        $words = $this->spacedRepetitionService->getWordsForReview($sessionId);

        return UserWordResource::collection($words);
    }

    /**
     * Delete a user word
     */
    public function destroy(int $id): JsonResponse
    {
        $userWord = UserWord::findOrFail($id);
        $userWord->delete();

        return response()->json(['message' => 'Word removed successfully']);
    }
}

