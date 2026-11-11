<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $profile = UserProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'learning_level' => 'beginner',
                'daily_study_target' => 60,
            ]
        );

        return view('profile', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date'],
            'education_level' => ['nullable', 'string', 'max:255'],
            'major' => ['nullable', 'string', 'max:255'],
            'learning_level' => [
                'required',
                'in:beginner,intermediate,advanced'
            ],
            'daily_study_target' => [
                'required',
                'integer',
                'min:15',
                'max:1440'
            ],
            'preferred_study_start' => [
                'nullable',
                'date_format:H:i'
            ],
            'preferred_study_end' => [
                'nullable',
                'date_format:H:i'
            ],
        ]);

        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return redirect()
            ->route('profile')
            ->with('success', 'Cập nhật hồ sơ thành công!');
    }
}