<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Word extends Model
{
    protected $fillable = [
        'word',
        'level',
        'difficulty',
        'definition',
        'phonetic',
        'audio_url',
        'image_url',
        'example_sentence',
        'meanings',
        'synonyms',
    ];

    protected $casts = [
        'meanings' => 'array',
        'synonyms' => 'array',
    ];

    public function userWords(): HasMany
    {
        return $this->hasMany(UserWord::class);
    }

    public function scopeByLevel($query, string $level)
    {
        return $query->where('level', $level);
    }

    public function scopeByDifficulty($query, int $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }
}

