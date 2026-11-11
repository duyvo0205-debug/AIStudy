<?php

namespace App\Services;

use App\Models\AIStudyRecommendation;
use App\Models\SchoolSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AIStudyRecommendationService
{
    protected PersonalizationService $personalizationService;

    public function __construct(
        PersonalizationService $personalizationService
    ) {
        $this->personalizationService = $personalizationService;
    }

    /**
     * Tạo các đề xuất học tập dựa trên:
     * - Thời khóa biểu
     * - Hồ sơ người học
     * - Mục tiêu học tập
     * - Kết quả học tập
     * - Nội dung cần ưu tiên
     */
    public function generateForUser(User $user): Collection
    {
        $personalization = $this->personalizationService
            ->getRecommendation($user);

        $schedules = SchoolSchedule::where('user_id', $user->id)
            ->orderBy('study_date')
            ->orderBy('start_time')
            ->get();

        $recommendations = collect();

        foreach ($schedules as $schedule) {
            $studyDate = $this->getNextStudyDate($schedule);

            if (!$studyDate) {
                continue;
            }

            $recommendation = $this->createRecommendation(
                $user,
                $schedule,
                $studyDate,
                $personalization
            );

            if ($recommendation) {
                $recommendations->push($recommendation);
            }
        }

        return $recommendations;
    }

    /**
     * Xác định ngày học tiếp theo.
     */
    protected function getNextStudyDate(
        SchoolSchedule $schedule
    ): ?Carbon {
        /*
         * Lịch học cố định.
         */
        if (!$schedule->start_date && !$schedule->end_date) {
            if (!$schedule->study_date) {
                return null;
            }

            $date = Carbon::parse($schedule->study_date);

            return $date->isToday() || $date->isFuture()
                ? $date
                : null;
        }

        /*
         * Lịch học lặp.
         */
        if (!$schedule->start_date) {
            return null;
        }

        $startDate = Carbon::parse($schedule->start_date);
        $today = today();

        $searchFrom = $startDate->greaterThan($today)
            ? $startDate->copy()
            : $today->copy();

        $endDate = $schedule->end_date
            ? Carbon::parse($schedule->end_date)
            : $searchFrom->copy()->addMonths(3);

        if ($searchFrom->greaterThan($endDate)) {
            return null;
        }

        /*
         * Carbon:
         * 0 = Chủ nhật
         * 1 = Thứ 2
         * 2 = Thứ 3
         * 3 = Thứ 4
         * 4 = Thứ 5
         * 5 = Thứ 6
         * 6 = Thứ 7
         */
        if ($schedule->day_of_week !== null) {
            $targetDay = (int) $schedule->day_of_week;

            $currentDate = $searchFrom->copy();

            while ($currentDate->lessThanOrEqualTo($endDate)) {
                if ((int) $currentDate->dayOfWeek === $targetDay) {
                    return $currentDate;
                }

                $currentDate->addDay();
            }

            return null;
        }

        return $searchFrom->lessThanOrEqualTo($endDate)
            ? $searchFrom
            : null;
    }

    /**
     * Tạo một đề xuất học tập.
     */
    protected function createRecommendation(
        User $user,
        SchoolSchedule $schedule,
        Carbon $studyDate,
        array $personalization
    ): ?AIStudyRecommendation {
        /*
         * Không tạo trùng đề xuất.
         */
        $exists = AIStudyRecommendation::where('user_id', $user->id)
            ->where('school_schedule_id', $schedule->id)
            ->whereDate(
                'recommended_date',
                $studyDate->toDateString()
            )
            ->whereIn('status', [
                'pending',
                'accepted',
            ])
            ->exists();

        if ($exists) {
            return null;
        }

        $startTime = $this->getRecommendedStartTime($user);

        $duration = $this->getRecommendedDuration($user);

        $endTime = $startTime->copy()->addMinutes($duration);

        $learningGoal = $this->buildLearningGoal(
            $schedule,
            $personalization
        );

        $reason = $this->buildPersonalizedReason(
            $schedule,
            $personalization
        );

        return AIStudyRecommendation::create([
            'user_id' => $user->id,

            'school_schedule_id' => $schedule->id,

            'title' => 'Ôn tập ' . $schedule->subject_name,

            'subject' => $schedule->subject_name,

            'description' => $this->buildDescription(
                $schedule,
                $personalization
            ),

            'recommended_date' => $studyDate->toDateString(),

            'recommended_start_time' => $startTime->format('H:i:s'),

            'recommended_end_time' => $endTime->format('H:i:s'),

            'duration_minutes' => $duration,

            'learning_goal' => $learningGoal,

            'reason' => $reason,

            'status' => 'pending',
        ]);
    }

    /**
     * Xác định giờ bắt đầu học.
     */
    protected function getRecommendedStartTime(User $user): Carbon
    {
        $preferredStart = optional($user->profile)
            ->preferred_study_start;

        if ($preferredStart) {
            $preferredStart = substr(
                (string) $preferredStart,
                0,
                5
            );

            return Carbon::createFromFormat(
                'H:i',
                $preferredStart
            );
        }

        return Carbon::createFromFormat(
            'H:i',
            '19:00'
        );
    }

    /**
     * Xác định thời lượng học.
     */
    protected function getRecommendedDuration(User $user): int
    {
        $dailyTarget = optional($user->profile)
            ->daily_study_target;

        if (!$dailyTarget || $dailyTarget < 15) {
            return 60;
        }

        return min((int) $dailyTarget, 120);
    }

    /**
     * Tạo mục tiêu học phù hợp với dữ liệu cá nhân hóa.
     */
    protected function buildLearningGoal(
        SchoolSchedule $schedule,
        array $personalization
    ): string {
        $recommendation = $personalization['recommendation'] ?? null;

        /*
         * Nếu môn học trong thời khóa biểu có nội dung
         * tương ứng với nội dung AI đang ưu tiên.
         */
        if (
            is_array($recommendation)
            && !empty($recommendation['subject'])
            && $this->subjectsMatch(
                $schedule->subject_name,
                $recommendation['subject']
            )
        ) {
            return 'Ôn tập môn '
                . $schedule->subject_name
                . ' và củng cố nội dung '
                . ($recommendation['title'] ?? 'đang học');
        }

        /*
         * Nếu chưa có nội dung học tương ứng,
         * vẫn ưu tiên chuẩn bị cho môn sắp học.
         */
        return 'Chuẩn bị kiến thức cho môn '
            . $schedule->subject_name;
    }

    /**
     * Tạo lý do đề xuất dựa trên PersonalizationService.
     */
    protected function buildPersonalizedReason(
        SchoolSchedule $schedule,
        array $personalization
    ): string {
        $parts = [];

        /*
         * Lý do 1: môn sắp học.
         */
        $parts[] =
            'Môn ' . $schedule->subject_name
            . ' sắp có trong thời khóa biểu.';

        /*
         * Lý do 2: điều chỉnh học tập.
         */
        $adjustment = $personalization['adjustment'] ?? null;

        if (
            is_array($adjustment)
            && !empty($adjustment['description'])
        ) {
            $parts[] = $adjustment['description'];
        }

        /*
         * Lý do 3: nội dung đang được ưu tiên.
         */
        $recommendation = $personalization['recommendation'] ?? null;

        if (
            is_array($recommendation)
            && !empty($recommendation['title'])
            && !empty($recommendation['reasons'])
        ) {
            $reasons = array_slice(
                $recommendation['reasons'],
                0,
                2
            );

            foreach ($reasons as $reason) {
                $parts[] = $reason;
            }
        }

        return implode(' ', $parts);
    }

    /**
     * Mô tả đề xuất.
     */
    protected function buildDescription(
        SchoolSchedule $schedule,
        array $personalization
    ): string {
        $description =
            'Ôn tập và chuẩn bị kiến thức cho môn '
            . $schedule->subject_name
            . ' trước buổi học.';

        if ($schedule->course_code) {
            $description .=
                ' Mã môn: ' . $schedule->course_code . '.';
        }

        if ($schedule->class_code) {
            $description .=
                ' Lớp: ' . $schedule->class_code . '.';
        }

        if ($schedule->instructor) {
            $description .=
                ' Giảng viên: ' . $schedule->instructor . '.';
        }

        /*
         * Nếu có nội dung cá nhân hóa phù hợp,
         * thêm vào mô tả.
         */
        $recommendation = $personalization['recommendation'] ?? null;

        if (
            is_array($recommendation)
            && !empty($recommendation['title'])
            && $this->subjectsMatch(
                $schedule->subject_name,
                $recommendation['subject'] ?? ''
            )
        ) {
            $description .=
                ' Nội dung ưu tiên: '
                . $recommendation['title']
                . '.';
        }

        return $description;
    }

    /**
     * Kiểm tra hai môn có tương đồng hay không.
     */
    protected function subjectsMatch(
        ?string $subject1,
        ?string $subject2
    ): bool {
        if (!$subject1 || !$subject2) {
            return false;
        }

        $subject1 = mb_strtolower(trim($subject1));
        $subject2 = mb_strtolower(trim($subject2));

        if ($subject1 === $subject2) {
            return true;
        }

        return str_contains($subject1, $subject2)
            || str_contains($subject2, $subject1);
    }
}