<?php

namespace App\Services;

use App\Models\LearningContent;
use App\Models\StudySchedule;
use App\Models\User;
use RuntimeException;

class AILearningContentService
{
    protected GroqService $groqService;

    protected PersonalizationService $personalizationService;

    public function __construct(
        GroqService $groqService,
        PersonalizationService $personalizationService
    ) {
        $this->groqService = $groqService;
        $this->personalizationService = $personalizationService;
    }

    /**
     * Tạo nội dung học tập bằng Groq
     * dựa trên lịch học đã được người dùng chấp nhận
     * và dữ liệu cá nhân hóa của người học.
     */
    public function generate(
        User $user,
        StudySchedule $schedule
    ): LearningContent {
        /*
        |--------------------------------------------------------------------------
        | 1. Lấy dữ liệu cá nhân hóa
        |--------------------------------------------------------------------------
        */

        $personalization = $this->personalizationService
            ->getRecommendation($user);

        /*
        |--------------------------------------------------------------------------
        | 2. Thông tin hồ sơ người học
        |--------------------------------------------------------------------------
        */

        $profile = $user->profile;

        $learningLevel = $profile?->learning_level
            ?? 'beginner';

        $dailyStudyTarget = $profile?->daily_study_target
            ?? 60;

        /*
        |--------------------------------------------------------------------------
        | 3. Xác định mục tiêu học tập
        |--------------------------------------------------------------------------
        */

        $goals = $personalization['goals'] ?? [];

        $goalText = 'Chưa có mục tiêu học tập cụ thể.';

        foreach ($goals as $goal) {
            $goalSubject = $goal->subject ?? '';

            if (
                $this->subjectsMatch(
                    $schedule->subject,
                    $goalSubject
                )
            ) {
                $goalText =
                    ($goal->title ?? 'Mục tiêu học tập')
                    . ' - môn '
                    . ($goal->subject ?? $schedule->subject)
                    . ' - trình độ hiện tại: '
                    . ($goal->current_level ?? 'chưa xác định')
                    . ' - mục tiêu: '
                    . ($goal->target_level ?? 'chưa xác định');

                break;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Lấy kết quả học tập liên quan đến môn
        |--------------------------------------------------------------------------
        */

        $results = $personalization['results_by_content'] ?? [];

        $relatedResults = [];

        foreach ($results as $result) {
            $resultTitle = $result['title'] ?? '';

            if (
                $this->subjectsMatch(
                    $schedule->subject,
                    $resultTitle
                )
            ) {
                $relatedResults[] = $result;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Nếu không tìm thấy kết quả theo tên môn,
        |    lấy một số kết quả gần nhất để AI vẫn có dữ liệu.
        |--------------------------------------------------------------------------
        */

        if (empty($relatedResults)) {
            $relatedResults = array_slice(
                $results,
                0,
                3
            );
        }

        $resultText =
            'Chưa có dữ liệu kết quả học tập trước đó.';

        if (!empty($relatedResults)) {
            $resultLines = [];

            foreach ($relatedResults as $result) {
                $resultLines[] =
                    '- '
                    . ($result['title'] ?? 'Nội dung')
                    . ': '
                    . ($result['accuracy'] ?? 0)
                    . '% - '
                    . ($result['level'] ?? 'Chưa xác định');
            }

            $resultText = implode(
                "\n",
                $resultLines
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 6. Nội dung được hệ thống ưu tiên
        |--------------------------------------------------------------------------
        */

        $recommendation =
            $personalization['recommendation'] ?? null;

        $recommendedContent =
            'Chưa có nội dung ưu tiên cụ thể cho môn này.';

        if (
            is_array($recommendation)
            && $this->subjectsMatch(
                $schedule->subject,
                $recommendation['subject'] ?? ''
            )
        ) {
            $recommendedContent =
                ($recommendation['title'] ?? 'Nội dung')
                . ' - '
                . ($recommendation['description'] ?? '');

            if (!empty($recommendation['result'])) {
                $accuracy =
                    $recommendation['result']['accuracy']
                    ?? null;

                if ($accuracy !== null) {
                    $recommendedContent .=
                        ' Kết quả gần nhất: '
                        . $accuracy
                        . '%.';
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 7. Thông tin điều chỉnh cá nhân hóa
        |--------------------------------------------------------------------------
        */

        $adjustment =
            $personalization['adjustment'] ?? null;

        $adjustmentText =
            'Chưa có điều chỉnh cá nhân hóa cụ thể.';

        if (is_array($adjustment)) {
            $adjustmentText =
                ($adjustment['title'] ?? '')
                . ': '
                . ($adjustment['description'] ?? '');

            if (!empty($adjustment['focus'])) {
                $adjustmentText .=
                    ' Nội dung cần tập trung: '
                    . $adjustment['focus']
                    . '.';
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 8. Prompt hệ thống cho Groq
        |--------------------------------------------------------------------------
        */

        $systemPrompt = <<<PROMPT
Bạn là AI hỗ trợ học tập của hệ thống AIStudy.

Nhiệm vụ của bạn là tạo một bài học lý thuyết cá nhân hóa
cho người học dựa trên lịch học đã được người dùng chấp nhận.

NGUYÊN TẮC QUAN TRỌNG:

- Môn học trong lịch học là nguồn chủ đề chính.
- Không được chuyển sang một môn học khác chỉ vì dữ liệu
  cá nhân hóa có nội dung khác môn.
- Phải bám sát mục tiêu học tập của lịch.
- Phải phù hợp với trình độ hiện tại của người học.
- Nếu có kết quả học tập cho thấy phần kiến thức yếu,
  hãy ưu tiên giải thích và củng cố phần đó.
- Nếu chưa có kết quả học tập, hãy xây dựng nội dung nền tảng
  phù hợp với trình độ người học.
- Nội dung phải phục vụ việc tự học.
- Không tạo bài tập, câu hỏi trắc nghiệm hoặc đề kiểm tra.
- Có ví dụ minh họa khi phù hợp.
- Nếu môn học là môn lập trình, có thể sử dụng ví dụ code.
- Không bịa thông tin cá nhân của người học.
- Không nói rằng AI đã kiểm tra một nguồn tài liệu bên ngoài
  nếu không có dữ liệu nguồn được cung cấp.

Cấu trúc bắt buộc:

1. Mục tiêu bài học
2. Kiến thức cần nắm
3. Giải thích chi tiết
4. Ví dụ minh họa
5. Những điểm cần nhớ
6. Tóm tắt cuối bài

Không viết lời chào.
Không viết lời kết chung chung.
Chỉ trả về nội dung bài học.
PROMPT;

        /*
        |--------------------------------------------------------------------------
        | 9. Prompt dữ liệu người dùng
        |--------------------------------------------------------------------------
        */

        $userPrompt = <<<PROMPT
Hãy tạo một bài học lý thuyết cá nhân hóa cho người dùng AIStudy.

========================
LỊCH HỌC ĐÃ ĐƯỢC CHẤP NHẬN
========================

- Tiêu đề: {$schedule->title}
- Môn học: {$schedule->subject}
- Mục tiêu của buổi học: {$schedule->learning_goal}
- Nội dung lịch học: {$schedule->study_content}
- Ngày học: {$schedule->study_date}
- Thời gian: {$schedule->start_time} - {$schedule->end_time}

========================
HỒ SƠ NGƯỜI HỌC
========================

- Trình độ hiện tại: {$learningLevel}
- Thời gian học mục tiêu mỗi ngày: {$dailyStudyTarget} phút

========================
MỤC TIÊU HỌC TẬP
========================

{$goalText}

========================
ĐIỀU CHỈNH CÁ NHÂN HÓA
========================

{$adjustmentText}

========================
NỘI DUNG ĐƯỢC HỆ THỐNG ƯU TIÊN
========================

{$recommendedContent}

========================
KẾT QUẢ HỌC TẬP
========================

{$resultText}

========================
YÊU CẦU TẠO BÀI HỌC
========================

Hãy tạo bài học cho đúng môn:

"{$schedule->subject}"

Mục tiêu chính của bài học:

"{$schedule->learning_goal}"

Ưu tiên giúp người học chuẩn bị và củng cố kiến thức
phục vụ môn học này.

Nếu dữ liệu kết quả học tập cho thấy người học yếu ở một
phần kiến thức liên quan, hãy giải thích phần đó kỹ hơn.

Nếu chưa có dữ liệu phù hợp, hãy bắt đầu từ kiến thức nền
quan trọng nhất đối với trình độ hiện tại.

Bài học cần:
- Dễ hiểu.
- Có cấu trúc rõ ràng.
- Có ví dụ thực tế.
- Nếu là môn lập trình, có thể có ví dụ code.
- Giải thích thuật ngữ quan trọng.
- Tập trung vào kiến thức cần sử dụng.
- Không tạo bài tập.
- Không tạo câu hỏi trắc nghiệm.
- Không thêm nội dung không liên quan đến môn học.

Chỉ trả về nội dung bài học.
PROMPT;

        /*
        |--------------------------------------------------------------------------
        | 10. Gọi Groq
        |--------------------------------------------------------------------------
        */

        $content = $this->groqService->chat([
            [
                'role' => 'system',
                'content' => $systemPrompt,
            ],
            [
                'role' => 'user',
                'content' => $userPrompt,
            ],
        ]);

        if (!$content) {
            throw new RuntimeException(
                'Không thể tạo nội dung học tập bằng Groq.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 11. Xác định độ khó
        |--------------------------------------------------------------------------
        */

        $difficulty = match ($learningLevel) {
            'advanced' => 'advanced',
            'intermediate' => 'intermediate',
            default => 'beginner',
        };

        /*
        |--------------------------------------------------------------------------
        | 12. Tạo tiêu đề
        |--------------------------------------------------------------------------
        */

        $title =
            'AI - '
            . $schedule->subject
            . ': '
            . $schedule->learning_goal;

        /*
        |--------------------------------------------------------------------------
        | 13. Lưu nội dung học tập
        |--------------------------------------------------------------------------
        */

        return LearningContent::create([
            'user_id' => $user->id,

            'title' => $title,

            'description' =>
                'Nội dung học tập được AI tạo dựa trên '
                . 'lịch học môn '
                . $schedule->subject
                . ', mục tiêu học tập và dữ liệu cá nhân hóa '
                . 'của người dùng.',

            'content' => $content,

            'subject' => $schedule->subject,

            'difficulty' => $difficulty,

            'status' => 'pending',
        ]);
    }

    /**
     * Kiểm tra hai môn học có liên quan hay không.
     */
    protected function subjectsMatch(
        ?string $subject1,
        ?string $subject2
    ): bool {
        if (!$subject1 || !$subject2) {
            return false;
        }

        $subject1 = mb_strtolower(
            trim($subject1)
        );

        $subject2 = mb_strtolower(
            trim($subject2)
        );

        if ($subject1 === $subject2) {
            return true;
        }

        return str_contains(
            $subject1,
            $subject2
        ) || str_contains(
            $subject2,
            $subject1
        );
    }
}