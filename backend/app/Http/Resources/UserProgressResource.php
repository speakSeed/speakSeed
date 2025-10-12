<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProgressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sessionId' => $this->session_id,
            'totalWords' => $this->total_words,
            'masteredWords' => $this->mastered_words,
            'currentStreak' => $this->current_streak,
            'longestStreak' => $this->longest_streak,
            'accuracyPercentage' => $this->accuracy_percentage,
            'totalQuizzes' => $this->total_quizzes,
            'totalReviews' => $this->total_reviews,
            'levelProgress' => $this->level_progress,
            'lastActivity' => $this->last_activity?->toIso8601String(),
            'createdAt' => $this->created_at?->toIso8601String(),
            'updatedAt' => $this->updated_at?->toIso8601String(),
        ];
    }
}
