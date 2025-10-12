<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserWord extends Model
{
    protected $fillable = [
        'session_id',
        'word_id',
        'status',
        'next_review_date',
        'review_count',
        'correct_count',
        'incorrect_count',
        'easiness_factor',
        'interval',
    ];

    protected $casts = [
        'next_review_date' => 'datetime',
        'easiness_factor' => 'float',
    ];

    public function word(): BelongsTo
    {
        return $this->belongsTo(Word::class);
    }

    public function scopeBySession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public function scopeDueForReview($query)
    {
        return $query->whereNotNull('next_review_date')
            ->where('next_review_date', '<=', now());
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}

