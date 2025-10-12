<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProgress extends Model
{
    protected $table = 'user_progress';

    protected $fillable = [
        'session_id',
        'level',
        'total_words',
        'mastered_words',
        'current_streak',
        'longest_streak',
        'last_activity',
    ];

    protected $casts = [
        'total_words' => 'integer',
        'mastered_words' => 'integer',
        'current_streak' => 'integer',
        'longest_streak' => 'integer',
        'last_activity' => 'datetime',
    ];
}
