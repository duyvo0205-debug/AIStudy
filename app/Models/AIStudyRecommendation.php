<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIStudyRecommendation extends Model
{
    use HasFactory;

    protected $table = 'ai_study_recommendations';

    protected $fillable = [
        'user_id',
        'school_schedule_id',
        'title',
        'subject',
        'description',
        'recommended_date',
        'recommended_start_time',
        'recommended_end_time',
        'duration_minutes',
        'learning_goal',
        'reason',
        'status',
    ];

    protected $casts = [
        'recommended_date' => 'date',
    ];

    /**
     * Người dùng nhận đề xuất.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Thời khóa biểu liên quan đến đề xuất.
     */
    public function schoolSchedule(): BelongsTo
    {
        return $this->belongsTo(
            SchoolSchedule::class,
            'school_schedule_id'
        );
    }
}