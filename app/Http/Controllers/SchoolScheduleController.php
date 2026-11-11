<?php

namespace App\Http\Controllers;

use App\Models\SchoolSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchoolScheduleController extends Controller
{
    /**
     * Khung giờ của từng tiết học.
     */
    private function periodTimes(): array
    {
        return [
            1 => [
                'start' => '07:30',
                'end' => '08:20',
            ],

            2 => [
                'start' => '08:20',
                'end' => '09:10',
            ],

            3 => [
                'start' => '09:25',
                'end' => '10:15',
            ],

            4 => [
                'start' => '10:15',
                'end' => '11:05',
            ],

            5 => [
                'start' => '11:05',
                'end' => '11:55',
            ],

            6 => [
                'start' => '13:00',
                'end' => '13:50',
            ],

            7 => [
                'start' => '13:50',
                'end' => '14:40',
            ],

            8 => [
                'start' => '14:55',
                'end' => '15:45',
            ],

            9 => [
                'start' => '15:45',
                'end' => '16:35',
            ],

            10 => [
                'start' => '16:35',
                'end' => '17:25',
            ],

            11 => [
                'start' => '17:30',
                'end' => '18:20',
            ],

            12 => [
                'start' => '18:20',
                'end' => '19:10',
            ],

            13 => [
                'start' => '19:25',
                'end' => '20:15',
            ],
        ];
    }

    /**
     * Chuyển tiết học thành giờ bắt đầu và kết thúc.
     */
    private function getTimeByPeriods(
        int $startPeriod,
        int $endPeriod
    ): array {
        $periodTimes = $this->periodTimes();

        return [
            'start_time' => $periodTimes[$startPeriod]['start'],
            'end_time' => $periodTimes[$endPeriod]['end'],
        ];
    }

    /**
     * Tìm tiết bắt đầu từ giờ bắt đầu.
     */
    private function getPeriodByStartTime(?string $startTime): int
    {
        if (!$startTime) {
            return 1;
        }

        $startTime = substr($startTime, 0, 5);

        foreach ($this->periodTimes() as $period => $time) {
            if ($time['start'] === $startTime) {
                return $period;
            }
        }

        return 1;
    }

    /**
     * Tìm tiết kết thúc từ giờ kết thúc.
     */
    private function getPeriodByEndTime(?string $endTime): int
    {
        if (!$endTime) {
            return 1;
        }

        $endTime = substr($endTime, 0, 5);

        foreach ($this->periodTimes() as $period => $time) {
            if ($time['end'] === $endTime) {
                return $period;
            }
        }

        return 1;
    }

    /**
     * Hiển thị lịch học theo tuần.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        if ($request->filled('date')) {
            $selectedDate = Carbon::parse($request->date);
        } else {
            $selectedDate = Carbon::today();
        }

        $startOfWeek = $selectedDate
            ->copy()
            ->startOfWeek(Carbon::MONDAY);

        $endOfWeek = $selectedDate
            ->copy()
            ->endOfWeek(Carbon::SUNDAY);

        /*
        |--------------------------------------------------------------------------
        | Lấy lịch lặp theo tuần
        |--------------------------------------------------------------------------
        */

        $recurringSchedules = SchoolSchedule::where('user_id', $userId)
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->whereNotNull('day_of_week')
            ->whereDate('start_date', '<=', $endOfWeek->toDateString())
            ->whereDate('end_date', '>=', $startOfWeek->toDateString())
            ->orderBy('start_time')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Lấy lịch cũ chỉ có study_date
        |--------------------------------------------------------------------------
        */

        $oldSchedules = SchoolSchedule::where('user_id', $userId)
            ->whereNull('start_date')
            ->whereBetween('study_date', [
                $startOfWeek->toDateString(),
                $endOfWeek->toDateString(),
            ])
            ->orderBy('study_date')
            ->orderBy('start_time')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Tạo danh sách lịch hiển thị trong tuần
        |--------------------------------------------------------------------------
        */

        $schedules = collect();

        /*
        |--------------------------------------------------------------------------
        | Tạo các ngày từ thứ 2 đến chủ nhật
        |--------------------------------------------------------------------------
        */

        $weekDays = collect();

        for ($i = 0; $i < 7; $i++) {
            $weekDays->push(
                $startOfWeek->copy()->addDays($i)
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Sinh lịch lặp vào từng ngày phù hợp
        |--------------------------------------------------------------------------
        */

        foreach ($weekDays as $day) {
            $dayOfWeek = $day->dayOfWeekIso;

            foreach ($recurringSchedules as $schedule) {
                $scheduleStartDate = Carbon::parse($schedule->start_date);
                $scheduleEndDate = Carbon::parse($schedule->end_date);

                $isInsideDateRange =
                    $day->greaterThanOrEqualTo($scheduleStartDate) &&
                    $day->lessThanOrEqualTo($scheduleEndDate);

                $isCorrectDay =
                    (int) $schedule->day_of_week === $dayOfWeek;

                if ($isInsideDateRange && $isCorrectDay) {
                    $schedules->push((object) [
                        'id' => $schedule->id,
                        'subject_name' => $schedule->subject_name,
                        'course_code' => $schedule->course_code,
                        'class_code' => $schedule->class_code,
                        'study_date' => $day->copy(),
                        'start_date' => $schedule->start_date,
                        'end_date' => $schedule->end_date,
                        'day_of_week' => $schedule->day_of_week,
                        'start_time' => $schedule->start_time,
                        'end_time' => $schedule->end_time,
                        'room' => $schedule->room,
                        'instructor' => $schedule->instructor,
                        'is_recurring' => true,
                    ]);
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Thêm các lịch cũ vào danh sách
        |--------------------------------------------------------------------------
        */

        foreach ($oldSchedules as $schedule) {
            $schedules->push((object) [
                'id' => $schedule->id,
                'subject_name' => $schedule->subject_name,
                'course_code' => $schedule->course_code,
                'class_code' => $schedule->class_code,
                'study_date' => Carbon::parse($schedule->study_date),
                'start_date' => null,
                'end_date' => null,
                'day_of_week' => null,
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
                'room' => $schedule->room,
                'instructor' => $schedule->instructor,
                'is_recurring' => false,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Sắp xếp lịch theo ngày và giờ
        |--------------------------------------------------------------------------
        */

        $schedules = $schedules
            ->sortBy(function ($schedule) {
                return $schedule->study_date->format('Y-m-d')
                    . ' '
                    . $schedule->start_time;
            })
            ->values();

        return view('school-schedules.index', compact(
            'schedules',
            'weekDays',
            'startOfWeek',
            'endOfWeek',
            'selectedDate'
        ));
    }

    /**
     * Form thêm lịch học.
     */
    public function create()
    {
        return view('school-schedules.create');
    }

    /**
     * Lưu lịch học lặp.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_name' => [
                'required',
                'string',
                'max:255',
            ],

            'course_code' => [
                'nullable',
                'string',
                'max:100',
            ],

            'class_code' => [
                'nullable',
                'string',
                'max:100',
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
            ],

            'day_of_week' => [
                'required',
                'integer',
                'min:1',
                'max:7',
            ],

            'start_period' => [
                'required',
                'integer',
                'min:1',
                'max:13',
            ],

            'end_period' => [
                'required',
                'integer',
                'min:1',
                'max:13',
                'gte:start_period',
            ],

            'room' => [
                'nullable',
                'string',
                'max:100',
            ],

            'instructor' => [
                'nullable',
                'string',
                'max:255',
            ],
        ], [
            'subject_name.required' =>
                'Vui lòng nhập tên môn học.',

            'start_date.required' =>
                'Vui lòng chọn ngày bắt đầu.',

            'end_date.required' =>
                'Vui lòng chọn ngày kết thúc.',

            'end_date.after_or_equal' =>
                'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',

            'day_of_week.required' =>
                'Vui lòng chọn thứ học.',

            'start_period.required' =>
                'Vui lòng chọn tiết bắt đầu.',

            'end_period.required' =>
                'Vui lòng chọn tiết kết thúc.',

            'end_period.gte' =>
                'Tiết kết thúc phải lớn hơn hoặc bằng tiết bắt đầu.',
        ]);

        $periodTimes = $this->getTimeByPeriods(
            (int) $validated['start_period'],
            (int) $validated['end_period']
        );

        $scheduleData = [
            'user_id' => Auth::id(),

            'subject_name' => $validated['subject_name'],

            'course_code' => $validated['course_code'] ?? null,

            'class_code' => $validated['class_code'] ?? null,

            /*
            |--------------------------------------------------------------------------
            | Giữ study_date để tương thích với cấu trúc cũ
            |--------------------------------------------------------------------------
            */

            'study_date' => $validated['start_date'],

            'start_date' => $validated['start_date'],

            'end_date' => $validated['end_date'],

            'day_of_week' => $validated['day_of_week'],

            'start_time' => $periodTimes['start_time'],

            'end_time' => $periodTimes['end_time'],

            'room' => $validated['room'] ?? null,

            'instructor' => $validated['instructor'] ?? null,
        ];

        SchoolSchedule::create($scheduleData);

        return redirect()
            ->route('school-schedules.index')
            ->with('success', 'Đã thêm lịch học lặp theo tuần!');
    }

    /**
     * Form sửa lịch học.
     */
    public function edit(SchoolSchedule $schedule)
    {
        if ($schedule->user_id !== Auth::id()) {
            abort(403);
        }

        $startPeriod = $this->getPeriodByStartTime(
            $schedule->start_time
        );

        $endPeriod = $this->getPeriodByEndTime(
            $schedule->end_time
        );

        return view('school-schedules.edit', compact(
            'schedule',
            'startPeriod',
            'endPeriod'
        ));
    }

    /**
     * Cập nhật lịch học.
     */
    public function update(
        Request $request,
        SchoolSchedule $schedule
    ) {
        if ($schedule->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'subject_name' => [
                'required',
                'string',
                'max:255',
            ],

            'course_code' => [
                'nullable',
                'string',
                'max:100',
            ],

            'class_code' => [
                'nullable',
                'string',
                'max:100',
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
            ],

            'day_of_week' => [
                'required',
                'integer',
                'min:1',
                'max:7',
            ],

            'start_period' => [
                'required',
                'integer',
                'min:1',
                'max:13',
            ],

            'end_period' => [
                'required',
                'integer',
                'min:1',
                'max:13',
                'gte:start_period',
            ],

            'room' => [
                'nullable',
                'string',
                'max:100',
            ],

            'instructor' => [
                'nullable',
                'string',
                'max:255',
            ],
        ], [
            'subject_name.required' =>
                'Vui lòng nhập tên môn học.',

            'start_date.required' =>
                'Vui lòng chọn ngày bắt đầu.',

            'end_date.required' =>
                'Vui lòng chọn ngày kết thúc.',

            'end_date.after_or_equal' =>
                'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',

            'day_of_week.required' =>
                'Vui lòng chọn thứ học.',

            'start_period.required' =>
                'Vui lòng chọn tiết bắt đầu.',

            'end_period.required' =>
                'Vui lòng chọn tiết kết thúc.',

            'end_period.gte' =>
                'Tiết kết thúc phải lớn hơn hoặc bằng tiết bắt đầu.',
        ]);

        $periodTimes = $this->getTimeByPeriods(
            (int) $validated['start_period'],
            (int) $validated['end_period']
        );

        $schedule->update([
            'subject_name' => $validated['subject_name'],

            'course_code' => $validated['course_code'] ?? null,

            'class_code' => $validated['class_code'] ?? null,

            'study_date' => $validated['start_date'],

            'start_date' => $validated['start_date'],

            'end_date' => $validated['end_date'],

            'day_of_week' => $validated['day_of_week'],

            'start_time' => $periodTimes['start_time'],

            'end_time' => $periodTimes['end_time'],

            'room' => $validated['room'] ?? null,

            'instructor' => $validated['instructor'] ?? null,
        ]);

        return redirect()
            ->route('school-schedules.index')
            ->with('success', 'Đã cập nhật lịch học!');
    }

    /**
     * Xóa lịch học.
     */
    public function destroy(SchoolSchedule $schedule)
    {
        if ($schedule->user_id !== Auth::id()) {
            abort(403);
        }

        $schedule->delete();

        return redirect()
            ->route('school-schedules.index')
            ->with('success', 'Đã xóa lịch học!');
    }
}