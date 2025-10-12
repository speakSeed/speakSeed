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
        'repetitions',
        'interval',
        'ease_factor',
    ];

    protected $casts = [
        'next_review_date' => 'datetime',
        'review_count' => 'integer',
        'repetitions' => 'integer',
        'interval' => 'integer',
        'ease_factor' => 'float',
    ];

    public function word(): BelongsTo
    {
        return $this->belongsTo(Word::class);
    }
}
