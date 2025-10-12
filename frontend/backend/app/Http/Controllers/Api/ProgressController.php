<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserProgress;
use App\Models\UserWord;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

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
        
        $progress = UserProgress::where('session_id', $sessionId)->first();
        
        if (!$progress) {
            // Create default progress
            $progress = UserProgress::create([
                'session_id' => $sessionId,
                'level' => 'A1',
                'total_words' => 0,
                'mastered_words' => 0,
                'current_streak' => 0,
                'longest_streak' => 0,
            ]);
        }
        
        // Get additional stats
        $userWords = UserWord::where('session_id', $sessionId)->get();
        $masteredCount = $userWords->where('status', 'mastered')->count();
        
        $progress->total_words = $userWords->count();
        $progress->mastered_words = $masteredCount;
        $progress->save();
        
        return response()->json($progress);
    }
    
    /**
     * Update progress
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
            'level' => 'sometimes|in:A1,A2,B1,B2,C1,C2',
        ]);
        
        $sessionId = $request->input('session_id');
        
        $progress = UserProgress::firstOrCreate(
            ['session_id' => $sessionId],
            [
                'level' => 'A1',
                'total_words' => 0,
                'mastered_words' => 0,
                'current_streak' => 0,
                'longest_streak' => 0,
            ]
        );
        
        // Update level if provided
        if ($request->has('level')) {
            $progress->level = $request->input('level');
        }
        
        // Update activity and streak
        $now = Carbon::now();
        $lastActivity = $progress->last_activity ? Carbon::parse($progress->last_activity) : null;
        
        if (!$lastActivity || $lastActivity->isToday()) {
            // Same day, don't update streak
        } elseif ($lastActivity->isYesterday()) {
            // Consecutive day
            $progress->current_streak++;
            if ($progress->current_streak > $progress->longest_streak) {
                $progress->longest_streak = $progress->current_streak;
            }
        } else {
            // Streak broken
            $progress->current_streak = 1;
        }
        
        $progress->last_activity = $now;
        $progress->save();
        
        return response()->json($progress);
    }
    
    /**
     * Get progress statistics by level
     */
    public function statistics(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
        ]);
        
        $sessionId = $request->input('session_id');
        
        $progress = UserProgress::where('session_id', $sessionId)->first();
        $userWords = UserWord::with('word')->where('session_id', $sessionId)->get();
        
        // Group by level
        $statsByLevel = $userWords->groupBy(function ($userWord) {
            return $userWord->word->level;
        })->map(function ($words, $level) {
            return [
                'level' => $level,
                'total' => $words->count(),
                'mastered' => $words->where('status', 'mastered')->count(),
                'learning' => $words->where('status', 'learning')->count(),
            ];
        })->values();
        
        return response()->json([
            'progress' => $progress,
            'by_level' => $statsByLevel,
            'total_words' => $userWords->count(),
            'mastered_words' => $userWords->where('status', 'mastered')->count(),
            'learning_words' => $userWords->where('status', 'learning')->count(),
        ]);
    }
}
