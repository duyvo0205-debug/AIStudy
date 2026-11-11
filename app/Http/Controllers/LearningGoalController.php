<?php

namespace App\Http\Controllers;

use App\Models\LearningGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LearningGoalController extends Controller
{
    public function index()
    {
        $goals = LearningGoal::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('learning-goals.index', compact('goals'));
    }

    public function create()
    {
        return view('learning-goals.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'subject' => ['nullable', 'string', 'max:255'],

            'current_level' => [
                'required',
                'in:beginner,intermediate,advanced'
            ],

            'target_level' => [
                'required',
                'in:beginner,intermediate,advanced'
            ],

            'target_date' => [
                'nullable',
                'date'
            ],

            'daily_minutes' => [
                'required',
                'integer',
                'min:15',
                'max:1440'
            ],
        ]);

        $validated['user_id'] = Auth::id();

        LearningGoal::create($validated);

        return redirect()
            ->route('learning-goals.index')
            ->with('success', 'Đã tạo mục tiêu học tập!');
    }

    public function updateStatus(
        Request $request,
        LearningGoal $goal
    ) {
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => [
                'required',
                'in:active,completed,paused'
            ],
        ]);

        $goal->update($validated);

        return back()
            ->with('success', 'Đã cập nhật mục tiêu!');
    }

    public function destroy(LearningGoal $goal)
    {
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        $goal->delete();

        return back()
            ->with('success', 'Đã xóa mục tiêu!');
    }
}