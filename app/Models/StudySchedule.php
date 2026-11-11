<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudySchedule extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'subject',
        'learning_goal',
        'study_content',
        'exercises',
        'materials',
        'description',
        'result_note',
        'study_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'priority',
        'status',
        'source',
    ];

    protected $casts = [
        'study_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}