<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExerciseQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'exercise_id',
        'question',
        'question_type',
        'options',
        'correct_answer',
        'explanation',
        'points',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'correct_answer' => 'array',
        'points' => 'decimal:2',
    ];

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }
    public function attempts(): HasMany
{
    return $this->hasMany(ExerciseAttempt::class);
}
}