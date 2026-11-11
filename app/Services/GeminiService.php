<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiService
{
    public function chat(array $messages): string
    {
        $apiKey = config('services.gemini.api_key');

        if (!$apiKey) {
            throw new RuntimeException(
                'Gemini API key chưa được cấu hình.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Chuẩn bị lịch sử cuộc trò chuyện
        |--------------------------------------------------------------------------
        */

        $input = [];

        foreach ($messages as $message) {
            $input[] = [
                'type' => 'text',
                'text' => (
                    $message['role'] === 'user'
                        ? "Người dùng: "
                        : "Trợ lý AI: "
                ) . $message['message'],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Gọi Gemini Interactions API
        |--------------------------------------------------------------------------
        */

        $response = Http::timeout(60)
            ->withHeaders([
                'x-goog-api-key' => $apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post(
                'https://generativelanguage.googleapis.com/v1beta/interactions',
                [
                    'model' => 'gemini-3.6-flash',
                    'input' => $input,
                    'store' => false,
                ]
            );

        /*
        |--------------------------------------------------------------------------
        | Kiểm tra lỗi API
        |--------------------------------------------------------------------------
        */

        if ($response->failed()) {
            throw new RuntimeException(
                'Gemini API lỗi: HTTP '
                . $response->status()
                . ' - '
                . $response->body()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Lấy steps từ Gemini
        |--------------------------------------------------------------------------
        */

        $steps = $response->json('steps');

        if (!is_array($steps)) {
            throw new RuntimeException(
                'Gemini không trả về steps. '
                . $response->body()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Tìm model_output
        |--------------------------------------------------------------------------
        */

        foreach ($steps as $step) {
            if (
                isset($step['type'])
                && $step['type'] === 'model_output'
                && isset($step['content'])
                && is_array($step['content'])
            ) {
                foreach ($step['content'] as $content) {
                    if (
                        isset($content['type'])
                        && $content['type'] === 'text'
                        && isset($content['text'])
                    ) {
                        return $content['text'];
                    }
                }
            }
        }

        throw new RuntimeException(
            'Gemini không trả về nội dung văn bản. '
            . $response->body()
        );
    }
}