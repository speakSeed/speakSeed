<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WordResource extends JsonResource
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
            'word' => $this->word,
            'level' => $this->level,
            'difficulty' => $this->difficulty,
            'definition' => $this->definition,
            'phonetic' => $this->phonetic,
            'audioUrl' => $this->audio_url,
            'imageUrl' => $this->image_url,
            'exampleSentence' => $this->example_sentence,
            'meanings' => $this->meanings,
            'synonyms' => $this->synonyms,
            'createdAt' => $this->created_at?->toIso8601String(),
            'updatedAt' => $this->updated_at?->toIso8601String(),
        ];
    }
}
