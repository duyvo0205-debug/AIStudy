<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exercise extends Model
{
    protected $fillable = [
        'user_id',
        'learning_content_id',
        'title',
        'description',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_option',
        'explanation',
        'difficulty',
        'status',
    ];

    /**
     * Bài tập thuộc về người dùng.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Bài tập thuộc về một nội dung học tập.
     */
    public function learningContent(): BelongsTo
    {
        return $this->belongsTo(
            LearningContent::class,
            'learning_content_id'
        );
    }

    /**
     * Các lần làm bài.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(ExerciseAttempt::class);
    }
    public function questions(): HasMany
{
    return $this->hasMany(ExerciseQuestion::class)
        ->orderBy('sort_order');
}
}