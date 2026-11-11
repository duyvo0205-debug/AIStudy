<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExerciseAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exercise_id',
        'exercise_question_id',
        'selected_option',
        'is_correct',
        'score',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'score' => 'decimal:2',
    ];

    /**
     * Người làm bài.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Bộ bài tập.
     */
    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    /**
     * Câu hỏi.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(
            ExerciseQuestion::class,
            'exercise_question_id'
        );
    }
}