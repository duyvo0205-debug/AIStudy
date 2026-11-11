<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Các trường được phép thêm hoặc cập nhật.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Các trường không hiển thị khi chuyển sang mảng hoặc JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Ép kiểu dữ liệu.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Hồ sơ cá nhân
    |--------------------------------------------------------------------------
    */

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Mục tiêu học tập
    |--------------------------------------------------------------------------
    */

    public function learningGoals(): HasMany
    {
        return $this->hasMany(LearningGoal::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Nội dung học tập
    |--------------------------------------------------------------------------
    */

    public function learningContents(): HasMany
    {
        return $this->hasMany(LearningContent::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Lịch tự học
    |--------------------------------------------------------------------------
    */

    public function studySchedules(): HasMany
    {
        return $this->hasMany(StudySchedule::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Lịch học ở trường
    |--------------------------------------------------------------------------
    */

    public function schoolSchedules(): HasMany
    {
        return $this->hasMany(SchoolSchedule::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Kết quả học tập
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | Bài tập
    |--------------------------------------------------------------------------
    */

    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Kết quả làm bài tập
    |--------------------------------------------------------------------------
    */

    public function exerciseAttempts(): HasMany
    {
        return $this->hasMany(ExerciseAttempt::class);
    }
    /**
 * Lịch sử trò chuyện với AI.
 */
public function aiMessages(): HasMany
{
    return $this->hasMany(AiMessage::class);
}
public function aiConversations(): HasMany
{
    return $this->hasMany(AiConversation::class);
}
public function aiStudyRecommendations(): HasMany
{
    return $this->hasMany(AIStudyRecommendation::class);
}
}