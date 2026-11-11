<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',

        'subject_name',
        'course_code',
        'class_code',

        'study_date',

        'start_date',
        'end_date',
        'day_of_week',

        'start_time',
        'end_time',

        'room',
        'instructor',
    ];

    protected $casts = [
        'study_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Người sở hữu lịch học.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}