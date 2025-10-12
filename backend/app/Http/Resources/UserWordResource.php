<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserWordResource extends JsonResource
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
            'wordId' => $this->word_id,
            'status' => $this->status,
            'addedAt' => $this->added_at?->toIso8601String(),
            'nextReviewDate' => $this->next_review_date?->toIso8601String(),
            'reviewCount' => $this->review_count,
            'easeFactor' => $this->easiness_factor,
            'incorrectCount' => $this->incorrect_count,
            'interval' => $this->interval,
            'correctCount' => $this->correct_count,
            'createdAt' => $this->created_at?->toIso8601String(),
            'updatedAt' => $this->updated_at?->toIso8601String(),
            'word' => new WordResource($this->whenLoaded('word')),
        ];
    }
}
