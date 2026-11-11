<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningContent extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'content',
        'subject',
        'difficulty',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function exercises(): HasMany
    {
        return $this->hasMany(
            Exercise::class,
            'learning_content_id'
        );
    }
}