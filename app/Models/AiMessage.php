<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiMessage extends Model
{
    protected $fillable = [
        'user_id',
        'conversation_id',
        'role',
        'message',
    ];

    /**
     * Người dùng sở hữu tin nhắn
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Cuộc trò chuyện chứa tin nhắn
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(
            AiConversation::class,
            'conversation_id'
        );
    }
}