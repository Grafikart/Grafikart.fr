<?php

namespace App\Http\API;

use App\Domains\Course\Course;
use App\Domains\History\ProgressionService;
use App\Http\API\Data\ProgressData;
use App\Http\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function __construct(private readonly ProgressionService $progressionService) {}

    public function store(Course $course, ProgressData $data, Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user === null) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $this->progressionService->trackProgress(
            user: $user,
            course: $course,
            progress: $data->progress,
            score: $data->score,
        );

        return response()->json(['message' => 'Progress updated successfully']);
    }
}
