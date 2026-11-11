<?php

namespace App\Http\Controllers;

use App\Services\PersonalizationService;
use Illuminate\Support\Facades\Auth;

class LearningPathController extends Controller
{
    public function index(
        PersonalizationService $personalizationService
    ) {
        $user = Auth::user();

        $personalization =
            $personalizationService->getRecommendation($user);

        return view(
            'learning-path.index',
            compact('personalization')
        );
    }
}