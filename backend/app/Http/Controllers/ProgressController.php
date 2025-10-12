<?php

namespace App\Http\Controllers;

use App\Models\UserProgress;
use App\Models\UserWord;
use App\Http\Resources\UserProgressResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProgressController extends Controller
{
    /**
     * Get user progress statistics
     */
    public function show(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
        ]);

        $sessionId = $request->input('session_id');
        
        $progress = UserProgress::firstOrCreate(
            ['session_id' => $sessionId],
            [
                'total_words' => 0,
                'mastered_words' => 0,
                'current_streak' => 0,
                'longest_streak' => 0,
                'accuracy_percentage' => 0,
                'total_quizzes' => 0,
                'total_reviews' => 0,
                'level_progress' => [],
            ]
        );

        // Get additional statistics
        $userWords = UserWord::where('session_id', $sessionId)->with('word')->get();
        
        $statistics = [
            'progress' => new UserProgressResource($progress),
            'wordsByStatus' => [
                'new' => $userWords->where('status', 'new')->count(),
                'learning' => $userWords->where('status', 'learning')->count(),
                'mastered' => $userWords->where('status', 'mastered')->count(),
            ],
            'wordsByLevel' => $userWords->groupBy('word.level')->map->count(),
            'dueForReview' => $userWords->filter(function ($uw) {
                return $uw->next_review_date && $uw->next_review_date <= now();
            })->count(),
        ];

        return response()->json($statistics);
    }

    /**
     * Update progress
     */
    public function update(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'quiz_completed' => 'boolean',
            'review_completed' => 'boolean',
        ]);

        $progress = UserProgress::firstOrCreate(
            ['session_id' => $request->session_id],
        );

        // Update streak
        $progress->updateStreak();

        // Calculate statistics from user_words
        $userWords = UserWord::where('session_id', $request->session_id)->get();
        $progress->total_words = $userWords->count();
        $progress->mastered_words = $userWords->where('status', 'mastered')->count();

        // Calculate accuracy
        $totalReviews = $userWords->sum('review_count');
        $correctAnswers = $userWords->sum('correct_count');
        if ($totalReviews > 0) {
            $progress->accuracy_percentage = ($correctAnswers / $totalReviews) * 100;
        }

        // Update counters
        if ($request->get('quiz_completed')) {
            $progress->total_quizzes++;
        }
        if ($request->get('review_completed')) {
            $progress->total_reviews++;
        }

        // Calculate level progress
        $levelProgress = $userWords->groupBy('word.level')
            ->map(function ($words) {
                return [
                    'total' => $words->count(),
                    'mastered' => $words->where('status', 'mastered')->count(),
                ];
            });
        $progress->level_progress = $levelProgress->toArray();

        $progress->save();

        return new UserProgressResource($progress);
    }
}

