<?php

namespace App\Services;

use App\Models\UserWord;
use Carbon\Carbon;

class SpacedRepetitionService
{
    /**
     * Calculate next review using SM-2 algorithm
     * 
     * @param UserWord $userWord
     * @param int $quality Rating from 0-5 (0=complete blackout, 5=perfect response)
     * @return UserWord
     */
    public function calculateNextReview(UserWord $userWord, int $quality): UserWord
    {
        // Ensure quality is within valid range
        $quality = max(0, min(5, $quality));
        
        // If quality < 3, reset repetitions
        if ($quality < 3) {
            $userWord->repetitions = 0;
            $userWord->interval = 1;
        } else {
            if ($userWord->repetitions == 0) {
                $userWord->interval = 1;
            } elseif ($userWord->repetitions == 1) {
                $userWord->interval = 6;
            } else {
                $userWord->interval = round($userWord->interval * $userWord->ease_factor);
            }
            
            $userWord->repetitions++;
        }
        
        // Calculate new ease factor
        $userWord->ease_factor = max(
            1.3,
            $userWord->ease_factor + (0.1 - (5 - $quality) * (0.08 + (5 - $quality) * 0.02))
        );
        
        // Set next review date
        $userWord->next_review_date = Carbon::now()->addDays($userWord->interval);
        $userWord->review_count++;
        
        // Update status based on repetitions
        if ($userWord->repetitions >= 5 && $quality >= 4) {
            $userWord->status = 'mastered';
        }
        
        return $userWord;
    }
    
    /**
     * Get words due for review
     * 
     * @param string $sessionId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWordsForReview(string $sessionId)
    {
        return UserWord::with('word')
            ->where('session_id', $sessionId)
            ->where(function ($query) {
                $query->whereNull('next_review_date')
                    ->orWhere('next_review_date', '<=', Carbon::now());
            })
            ->orderBy('next_review_date', 'asc')
            ->get();
    }
}
