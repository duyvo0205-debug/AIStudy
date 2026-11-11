<?php

namespace App\Http\Controllers;

use App\Models\StudySchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudyScheduleController extends Controller
{
    public function index()
    {
        $schedules = StudySchedule::where('user_id', Auth::id())
            ->orderBy('study_date')
            ->orderBy('start_time')
            ->get();

        return view('study-schedules.index', compact('schedules'));
    }

    public function create()
    {
        return view('study-schedules.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',

            'subject' => 'nullable|string|max:255',

            'learning_goal' => 'nullable|string',

            'study_content' => 'nullable|string',

            'exercises' => 'nullable|string',

            'materials' => 'nullable|string',

            'description' => 'nullable|string',

            'result_note' => 'nullable|string',

            'study_date' => 'required|date',

            'start_time' => 'required|date_format:H:i',

            'end_time' => 'required|date_format:H:i|after:start_time',

            'priority' => 'required|in:low,medium,high',
        ], [
            'title.required' => 'Vui lòng nhập tên buổi học.',

            'study_date.required' => 'Vui lòng chọn ngày học.',

            'start_time.required' => 'Vui lòng chọn giờ bắt đầu.',

            'end_time.required' => 'Vui lòng chọn giờ kết thúc.',

            'end_time.after' => 'Giờ kết thúc phải sau giờ bắt đầu.',

            'priority.required' => 'Vui lòng chọn mức độ ưu tiên.',
        ]);

        $start = strtotime($validated['start_time']);
        $end = strtotime($validated['end_time']);

        $durationMinutes = ($end - $start) / 60;

        $validated['user_id'] = Auth::id();

        $validated['duration_minutes'] = $durationMinutes;

        $validated['status'] = 'planned';

        StudySchedule::create($validated);

        return redirect()
            ->route('study-schedules.index')
            ->with('success', 'Đã thêm lịch học thành công.');
    }

    public function edit(StudySchedule $schedule)
    {
        $this->checkOwner($schedule);

        return view('study-schedules.edit', compact('schedule'));
    }

    public function update(Request $request, StudySchedule $schedule)
    {
        $this->checkOwner($schedule);

        $validated = $request->validate([
            'title' => 'required|string|max:255',

            'subject' => 'nullable|string|max:255',

            'learning_goal' => 'nullable|string',

            'study_content' => 'nullable|string',

            'exercises' => 'nullable|string',

            'materials' => 'nullable|string',

            'description' => 'nullable|string',

            'result_note' => 'nullable|string',

            'study_date' => 'required|date',

            'start_time' => 'required|date_format:H:i',

            'end_time' => 'required|date_format:H:i|after:start_time',

            'priority' => 'required|in:low,medium,high',

            'status' => 'required|in:planned,completed,cancelled',
        ], [
            'title.required' => 'Vui lòng nhập tên buổi học.',

            'end_time.after' => 'Giờ kết thúc phải sau giờ bắt đầu.',
        ]);

        $start = strtotime($validated['start_time']);
        $end = strtotime($validated['end_time']);

        $validated['duration_minutes'] = ($end - $start) / 60;

        $schedule->update($validated);

        return redirect()
            ->route('study-schedules.index')
            ->with('success', 'Đã cập nhật lịch học.');
    }

    public function updateStatus(Request $request, StudySchedule $schedule)
    {
        $this->checkOwner($schedule);

        $validated = $request->validate([
            'status' => 'required|in:planned,completed,cancelled',
        ]);

        $schedule->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('study-schedules.index')
            ->with('success', 'Đã cập nhật trạng thái lịch học.');
    }

    public function destroy(StudySchedule $schedule)
    {
        $this->checkOwner($schedule);

        $schedule->delete();

        return redirect()
            ->route('study-schedules.index')
            ->with('success', 'Đã xóa lịch học.');
    }

    private function checkOwner(StudySchedule $schedule): void
    {
        if ((int) $schedule->user_id !== (int) Auth::id()) {
            abort(403);
        }
    }
}   