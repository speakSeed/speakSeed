<?php

namespace App\Services;

use App\Models\UserWord;
use Carbon\Carbon;

/**
 * Implements the SuperMemo 2 (SM-2) Spaced Repetition Algorithm
 * 
 * The algorithm calculates the optimal time intervals between reviews
 * based on the user's performance.
 */
class SpacedRepetitionService
{
    // Quality of response (0-5 scale)
    private const QUALITY_PERFECT = 5;
    private const QUALITY_CORRECT_EASY = 4;
    private const QUALITY_CORRECT_HARD = 3;
    private const QUALITY_INCORRECT_REMEMBERED = 2;
    private const QUALITY_INCORRECT_FORGOTTEN = 1;
    private const QUALITY_COMPLETE_BLACKOUT = 0;

    /**
     * Update review schedule based on user's performance
     * 
     * @param UserWord $userWord
     * @param int $quality Response quality (0-5)
     * @return UserWord
     */
    public function updateReviewSchedule(UserWord $userWord, int $quality): UserWord
    {
        $quality = max(0, min(5, $quality)); // Ensure quality is between 0-5

        // Calculate new easiness factor
        $newEF = $this->calculateEasinessFactor($userWord->easiness_factor, $quality);
        $userWord->easiness_factor = max(1.3, $newEF); // Minimum EF is 1.3

        // Update review count
        $userWord->review_count++;

        // Calculate new interval
        if ($quality < 3) {
            // Reset if answer was incorrect
            $userWord->interval = 1;
            $userWord->status = 'learning';
            $userWord->incorrect_count++;
        } else {
            // Successful recall
            $userWord->correct_count++;
            
            if ($userWord->review_count === 1) {
                $userWord->interval = 1;
            } elseif ($userWord->review_count === 2) {
                $userWord->interval = 6;
            } else {
                $userWord->interval = (int)ceil($userWord->interval * $userWord->easiness_factor);
            }

            // Update status based on performance
            if ($userWord->interval >= 21 && $userWord->easiness_factor >= 2.5) {
                $userWord->status = 'mastered';
            } else {
                $userWord->status = 'learning';
            }
        }

        // Set next review date
        $userWord->next_review_date = Carbon::now()->addDays($userWord->interval);

        return $userWord;
    }

    /**
     * Calculate new easiness factor using SM-2 algorithm
     * 
     * EF' = EF + (0.1 - (5 - q) * (0.08 + (5 - q) * 0.02))
     * 
     * @param float $currentEF Current easiness factor
     * @param int $quality Response quality
     * @return float
     */
    private function calculateEasinessFactor(float $currentEF, int $quality): float
    {
        return $currentEF + (0.1 - (5 - $quality) * (0.08 + (5 - $quality) * 0.02));
    }

    /**
     * Convert quiz performance to quality score
     * 
     * @param bool $correct Whether answer was correct
     * @param int $timeSpent Time spent in seconds
     * @param int $attempts Number of attempts
     * @return int Quality score (0-5)
     */
    public function performanceToQuality(bool $correct, int $timeSpent = 0, int $attempts = 1): int
    {
        if (!$correct) {
            return $attempts > 2 ? self::QUALITY_COMPLETE_BLACKOUT : self::QUALITY_INCORRECT_FORGOTTEN;
        }

        // Correct answer - determine quality based on time and attempts
        if ($attempts === 1) {
            if ($timeSpent < 5) {
                return self::QUALITY_PERFECT;
            } elseif ($timeSpent < 15) {
                return self::QUALITY_CORRECT_EASY;
            } else {
                return self::QUALITY_CORRECT_HARD;
            }
        }

        return self::QUALITY_CORRECT_HARD;
    }

    /**
     * Get words due for review for a session
     * 
     * @param string $sessionId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWordsForReview(string $sessionId, int $limit = 20)
    {
        return UserWord::bySession($sessionId)
            ->dueForReview()
            ->with('word')
            ->orderBy('next_review_date', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Initialize a new word for learning
     * 
     * @param UserWord $userWord
     * @return UserWord
     */
    public function initializeWord(UserWord $userWord): UserWord
    {
        $userWord->status = 'new';
        $userWord->easiness_factor = 2.5;
        $userWord->interval = 0;
        $userWord->next_review_date = Carbon::now()->addDay();
        
        return $userWord;
    }
}

