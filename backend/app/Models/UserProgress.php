<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProgress extends Model
{
    protected $table = 'user_progress';

    protected $fillable = [
        'session_id',
        'total_words',
        'mastered_words',
        'current_streak',
        'longest_streak',
        'last_activity_date',
        'level_progress',
        'accuracy_percentage',
        'total_quizzes',
        'total_reviews',
    ];

    protected $casts = [
        'last_activity_date' => 'date',
        'level_progress' => 'array',
        'accuracy_percentage' => 'float',
    ];

    public function updateStreak(): void
    {
        $today = now()->toDateString();
        $lastActivity = $this->last_activity_date?->toDateString();

        if ($lastActivity === $today) {
            // Already updated today
            return;
        }

        if ($lastActivity === now()->subDay()->toDateString()) {
            // Consecutive day
            $this->current_streak++;
        } else {
            // Streak broken
            $this->current_streak = 1;
        }

        if ($this->current_streak > $this->longest_streak) {
            $this->longest_streak = $this->current_streak;
        }

        $this->last_activity_date = now();
        $this->save();
    }
}

