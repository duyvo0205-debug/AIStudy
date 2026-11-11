<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GroqService
{
    public function chat(array $messages): string
    {
        $apiKey = config('services.groq.api_key');

        if (!$apiKey) {
            throw new RuntimeException(
                'Groq API key chưa được cấu hình.'
            );
        }

        $response = Http::timeout(60)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post(
                'https://api.groq.com/openai/v1/chat/completions',
                [
                    'model' => 'openai/gpt-oss-120b',

                    'messages' => $messages,

                    'temperature' => 0.7,
                ]
            );

        if ($response->failed()) {
            throw new RuntimeException(
                'Groq API lỗi: HTTP '
                . $response->status()
                . ' - '
                . $response->body()
            );
        }

        $content = $response->json(
            'choices.0.message.content'
        );

        if (!$content) {
            throw new RuntimeException(
                'Groq không trả về nội dung văn bản. '
                . $response->body()
            );
        }

        return trim($content);
    }
}