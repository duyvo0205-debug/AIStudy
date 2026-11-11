<?php

namespace App\Services;

use App\Models\User;

class PersonalizationService
{
    /**
     * Phân tích dữ liệu học tập và xác định
     * nội dung học tập nên được ưu tiên.
     */
    public function getRecommendation(User $user): array
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Hồ sơ người học
        |--------------------------------------------------------------------------
        */

        $profile = $user->profile;

        /*
        |--------------------------------------------------------------------------
        | 2. Mục tiêu đang hoạt động
        |--------------------------------------------------------------------------
        */

        $goals = $user->learningGoals()
            ->where('status', 'active')
            ->orderBy('target_date')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 3. Nội dung học tập
        |--------------------------------------------------------------------------
        */

        $contents = $user->learningContents()
            ->orderByRaw("
                CASE status
                    WHEN 'in_progress' THEN 1
                    WHEN 'pending' THEN 2
                    WHEN 'completed' THEN 3
                    ELSE 4
                END
            ")
            ->orderByDesc('created_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 4. Kết quả làm bài
        |--------------------------------------------------------------------------
        */

        $attempts = $user->exerciseAttempts()
            ->with('exercise.learningContent')
            ->latest()
            ->limit(100)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 5. Phân tích kết quả theo từng nội dung
        |--------------------------------------------------------------------------
        */

        $resultsByContent = [];

        foreach ($attempts as $attempt) {

            $content = $attempt->exercise?->learningContent;

            if (!$content) {
                continue;
            }

            $contentId = $content->id;

            if (!isset($resultsByContent[$contentId])) {

                $resultsByContent[$contentId] = [
                    'content_id' => $contentId,
                    'title' => $content->title,
                    'total' => 0,
                    'correct' => 0,
                    'wrong' => 0,
                    'accuracy' => 0,
                    'level' => 'Chưa xác định',
                ];
            }

            $resultsByContent[$contentId]['total']++;

            if ($attempt->is_correct) {

                $resultsByContent[$contentId]['correct']++;

            } else {

                $resultsByContent[$contentId]['wrong']++;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 5.1. Tính độ chính xác
        |--------------------------------------------------------------------------
        */

        foreach ($resultsByContent as &$result) {

            if ($result['total'] > 0) {

                $result['accuracy'] = round(
                    (
                        $result['correct']
                        / $result['total']
                    ) * 100,
                    2
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Phân loại kết quả
            |--------------------------------------------------------------------------
            */

            if ($result['accuracy'] >= 80) {

                $result['level'] = 'Tốt';

            } elseif ($result['accuracy'] >= 60) {

                $result['level'] = 'Cần củng cố';

            } else {

                $result['level'] = 'Cần cải thiện';
            }
        }

        unset($result);

        /*
        |--------------------------------------------------------------------------
        | 6. Xếp hạng nội dung học tập
        |--------------------------------------------------------------------------
        */

        $recommendations = [];

        foreach ($contents as $content) {

            $score = 0;

            $reasons = [];

            $result = $resultsByContent[$content->id]
                ?? null;

            /*
            |--------------------------------------------------------------------------
            | A. Trạng thái học tập
            |--------------------------------------------------------------------------
            */

            if ($content->status === 'in_progress') {

                $score += 40;

                $reasons[] =
                    'Nội dung đang được học nên được ưu tiên tiếp tục.';
            }

            if ($content->status === 'pending') {

                $score += 20;

                $reasons[] =
                    'Nội dung chưa được bắt đầu.';
            }

            if ($content->status === 'completed') {

                $score -= 30;
            }

            /*
            |--------------------------------------------------------------------------
            | B. Kết quả bài tập
            |--------------------------------------------------------------------------
            */

            if ($result) {

                if ($result['accuracy'] < 60) {

                    $score += 70;

                    $reasons[] =
                        'Kết quả bài tập cho thấy nội dung cần được cải thiện.';

                } elseif ($result['accuracy'] < 80) {

                    $score += 45;

                    $reasons[] =
                        'Kết quả bài tập cho thấy nội dung cần được củng cố.';

                } else {

                    $reasons[] =
                        'Kết quả bài tập hiện tại ở mức tốt.';
                }

            } else {

                $reasons[] =
                    'Chưa có kết quả bài tập để đánh giá nội dung này.';
            }

            /*
            |--------------------------------------------------------------------------
            | C. Đối chiếu mục tiêu học tập
            |--------------------------------------------------------------------------
            */

            $matchesGoal = false;

            foreach ($goals as $goal) {

                $goalText = mb_strtolower(
                    trim(
                        ($goal->title ?? '')
                        . ' '
                        . ($goal->subject ?? '')
                        . ' '
                        . ($goal->description ?? '')
                    )
                );

                $contentText = mb_strtolower(
                    trim(
                        ($content->title ?? '')
                        . ' '
                        . ($content->subject ?? '')
                        . ' '
                        . ($content->description ?? '')
                    )
                );

                /*
                |--------------------------------------------------------------------------
                | Tách từ khóa mục tiêu
                |--------------------------------------------------------------------------
                */

                $keywords = preg_split(
                    '/\s+/',
                    $goalText
                );

                foreach ($keywords as $keyword) {

                    if (mb_strlen($keyword) < 3) {
                        continue;
                    }

                    if (
                        mb_stripos(
                            $contentText,
                            $keyword
                        ) !== false
                    ) {

                        $matchesGoal = true;

                        break 2;
                    }
                }
            }

            if ($matchesGoal) {

                $score += 30;

                $reasons[] =
                    'Nội dung liên quan trực tiếp đến mục tiêu học tập.';
            }

            /*
            |--------------------------------------------------------------------------
            | D. Phù hợp với trình độ mục tiêu
            |--------------------------------------------------------------------------
            */

            $targetLevels = $goals
                ->pluck('target_level')
                ->filter()
                ->unique()
                ->values();

            if (
                $targetLevels->contains(
                    $content->difficulty
                )
            ) {

                $score += 10;

                $reasons[] =
                    'Độ khó của nội dung phù hợp với một trình độ mục tiêu.';
            }

            /*
            |--------------------------------------------------------------------------
            | E. Xác định mức ưu tiên
            |--------------------------------------------------------------------------
            */

            if ($result) {

                /*
                |--------------------------------------------------------------------------
                | Kết quả thấp
                |--------------------------------------------------------------------------
                */

                if ($result['accuracy'] < 60) {

                    $priority = 'Cao';

                /*
                |--------------------------------------------------------------------------
                | Kết quả trung bình
                |--------------------------------------------------------------------------
                */

                } elseif ($result['accuracy'] < 80) {

                    $priority = 'Cao';

                /*
                |--------------------------------------------------------------------------
                | Kết quả tốt nhưng đang học
                |--------------------------------------------------------------------------
                */

                } elseif ($content->status === 'in_progress') {

                    $priority = 'Trung bình';

                /*
                |--------------------------------------------------------------------------
                | Kết quả tốt và chưa bắt đầu
                |--------------------------------------------------------------------------
                */

                } elseif (
                    $result['accuracy'] >= 80
                    && $content->status === 'pending'
                ) {

                    if ($matchesGoal) {

                        $priority = 'Trung bình';

                    } else {

                        $priority = 'Thấp';
                    }

                } else {

                    $priority = 'Thấp';
                }

            } else {

                /*
                |--------------------------------------------------------------------------
                | Chưa có kết quả
                |--------------------------------------------------------------------------
                */

                if ($content->status === 'in_progress') {

                    $priority = 'Trung bình';

                } elseif ($matchesGoal) {

                    $priority = 'Trung bình';

                } else {

                    $priority = 'Thấp';
                }
            }

            /*
            |--------------------------------------------------------------------------
            | F. Lưu kết quả
            |--------------------------------------------------------------------------
            */

            $recommendations[] = [

                'content_id' =>
                    $content->id,

                'title' =>
                    $content->title,

                'subject' =>
                    $content->subject
                    ?? 'Chưa xác định',

                'description' =>
                    $content->description
                    ?? null,

                'difficulty' =>
                    $content->difficulty,

                'status' =>
                    $content->status,

                'score' =>
                    $score,

                'priority' =>
                    $priority,

                'reasons' =>
                    array_values(
                        array_unique($reasons)
                    ),

                'result' =>
                    $result,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | 7. Sắp xếp từ ưu tiên cao xuống thấp
        |--------------------------------------------------------------------------
        */

        usort(
            $recommendations,
            function ($a, $b) {

                return $b['score']
                    <=> $a['score'];
            }
        );

        /*
        |--------------------------------------------------------------------------
        | 8. Nội dung được đề xuất
        |--------------------------------------------------------------------------
        */

        $recommendedContent = null;

/*
|--------------------------------------------------------------------------
| Ưu tiên nội dung đã có kết quả bài tập
|--------------------------------------------------------------------------
|
| Nếu người học đã làm bài tập, AI phải dựa vào kết quả
| đó để đánh giá khả năng và điều chỉnh lộ trình.
|
*/

foreach ($recommendations as $item) {

    if ($item['result'] !== null) {
        $recommendedContent = $item;
        break;
    }
}

/*
|--------------------------------------------------------------------------
| Nếu chưa có kết quả bài tập
|--------------------------------------------------------------------------
|
| Khi người học chưa làm bài nào, sử dụng nội dung
| có điểm ưu tiên cao nhất.
|
*/

if ($recommendedContent === null) {
    $recommendedContent =
        $recommendations[0]
        ?? null;
}

        $nextContent = null;

if (
    $recommendedContent !== null
    && isset($recommendedContent['result'])
    && $recommendedContent['result'] !== null
) {
    $accuracy =
        (float) $recommendedContent['result']['accuracy'];

    if ($accuracy >= 80) {
        foreach ($recommendations as $item) {
            if (
                $item['content_id'] ===
                $recommendedContent['content_id']
            ) {
                continue;
            }

            if (
                $item['status'] !== 'pending'
            ) {
                continue;
            }

            $nextContent = $item;
            break;
        }
    }
}

        /*
        |--------------------------------------------------------------------------
        | 9. Xây dựng lộ trình học tập
        |--------------------------------------------------------------------------
        */

        $learningPath = [

            'not_started' => [],

            'in_progress' => [],

            'needs_improvement' => [],

            'needs_review' => [],

            'completed' => [],
        ];

        foreach ($recommendations as $item) {

            $result = $item['result'];

            /*
            |--------------------------------------------------------------------------
            | A. Nội dung đã hoàn thành
            |--------------------------------------------------------------------------
            */

            if ($item['status'] === 'completed') {

                $learningPath['completed'][] =
                    $item;

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | B. Nội dung đang học
            |--------------------------------------------------------------------------
            */

            if ($item['status'] === 'in_progress') {

                if ($result) {

                    /*
                    |--------------------------------------------------------------------------
                    | Dưới 60%
                    |--------------------------------------------------------------------------
                    */

                    if ($result['accuracy'] < 60) {

                        $learningPath['needs_improvement'][] =
                            $item;

                    /*
                    |--------------------------------------------------------------------------
                    | 60% - dưới 80%
                    |--------------------------------------------------------------------------
                    */

                    } elseif ($result['accuracy'] < 80) {

                        $learningPath['needs_review'][] =
                            $item;

                    /*
                    |--------------------------------------------------------------------------
                    | Từ 80% trở lên
                    |--------------------------------------------------------------------------
                    */

                    } else {

                        $learningPath['in_progress'][] =
                            $item;
                    }

                } else {

                    $learningPath['in_progress'][] =
                        $item;
                }

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | C. Nội dung chưa bắt đầu
            |--------------------------------------------------------------------------
            */

            if ($item['status'] === 'pending') {

                $learningPath['not_started'][] =
                    $item;

                continue;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 10. Thống kê lộ trình
        |--------------------------------------------------------------------------
        */

        $learningPathSummary = [

            'not_started' =>
                count(
                    $learningPath['not_started']
                ),

            'in_progress' =>
                count(
                    $learningPath['in_progress']
                ),

            'needs_improvement' =>
                count(
                    $learningPath['needs_improvement']
                ),

            'needs_review' =>
                count(
                    $learningPath['needs_review']
                ),

            'completed' =>
                count(
                    $learningPath['completed']
                ),
        ];

        /*
        |--------------------------------------------------------------------------
        | 11. AI điều chỉnh lộ trình
        |--------------------------------------------------------------------------
        */

        $adjustment = [

            'action' => 'continue',

            'title' => 'Tiếp tục học',

            'description' =>
                'Tiếp tục học theo lộ trình hiện tại.',

            'focus' => null,
        ];

        if ($recommendedContent) {

            $result =
                $recommendedContent['result'];

            if ($result) {

                /*
                |--------------------------------------------------------------------------
                | Dưới 60%
                |--------------------------------------------------------------------------
                */

                if ($result['accuracy'] < 60) {

                    $adjustment = [

                        'action' => 'improve',

                        'title' =>
                            'Ưu tiên cải thiện nội dung',

                        'description' =>
                            'Kết quả bài tập cho thấy người học đang gặp khó khăn với nội dung này. Hệ thống ưu tiên học lại nội dung trước khi chuyển sang nội dung mới.',

                        'focus' =>
                            $recommendedContent['title'],
                    ];

                /*
                |--------------------------------------------------------------------------
                | 60% - dưới 80%
                |--------------------------------------------------------------------------
                */

                } elseif ($result['accuracy'] < 80) {

                    $adjustment = [

                        'action' => 'review',

                        'title' =>
                            'Ôn tập và củng cố',

                        'description' =>
                            'Người học đã nắm được một phần kiến thức nhưng vẫn cần ôn tập thêm trước khi chuyển sang nội dung mới.',

                        'focus' =>
                            $recommendedContent['title'],
                    ];

                /*
                |--------------------------------------------------------------------------
                | Từ 80% trở lên
                |--------------------------------------------------------------------------
                */

                } else {

                    $adjustment = [

                        'action' => 'advance',

                        'title' =>
                            'Có thể tiếp tục nội dung mới',

                        'description' =>
                            'Kết quả hiện tại tốt. Người học có thể tiếp tục sang nội dung tiếp theo trong lộ trình.',

                        'focus' =>
                            $recommendedContent['title'],
                    ];
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 12. Trả về dữ liệu cá nhân hóa
        |--------------------------------------------------------------------------
        */

        return [

            /*
            |--------------------------------------------------------------------------
            | Thông tin người học
            |--------------------------------------------------------------------------
            */

            'learner' => [

                'name' =>
                    $user->name,

                'learning_level' =>
                    $profile?->learning_level
                    ?? 'Chưa xác định',

                'daily_study_target' =>
                    $profile?->daily_study_target
                    ?? 60,

                'preferred_study_start' =>
                    $profile?->preferred_study_start,

                'preferred_study_end' =>
                    $profile?->preferred_study_end,
            ],

            /*
            |--------------------------------------------------------------------------
            | AI điều chỉnh
            |--------------------------------------------------------------------------
            */

            'adjustment' =>
                $adjustment,

            /*
            |--------------------------------------------------------------------------
            | Mục tiêu
            |--------------------------------------------------------------------------
            */

            'goals' =>
                $goals->values()->all(),

            /*
            |--------------------------------------------------------------------------
            | Nội dung được ưu tiên
            |--------------------------------------------------------------------------
            */

            'recommendation' =>
                $recommendedContent,

            /*
            |--------------------------------------------------------------------------
            | Danh sách đề xuất
            |--------------------------------------------------------------------------
            */

            'recommendations' =>
                $recommendations,

            /*
            |--------------------------------------------------------------------------
            | Kết quả theo nội dung
            |--------------------------------------------------------------------------
            */

            'results_by_content' =>
                array_values(
                    $resultsByContent
                ),

            /*
            |--------------------------------------------------------------------------
            | Lộ trình học tập
            |--------------------------------------------------------------------------
            */

            'learning_path' =>
                $learningPath,

            /*
            |--------------------------------------------------------------------------
            | Thống kê lộ trình
            |--------------------------------------------------------------------------
            */

            'learning_path_summary' =>
                $learningPathSummary,

            /*
            |--------------------------------------------------------------------------
            | Nội dung tiếp theo
            |--------------------------------------------------------------------------
            */

            'next_content' =>
                $nextContent,
        ];
    }
}