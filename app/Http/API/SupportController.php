<?php

namespace App\Http\API;

use App\Domains\Course\Course;
use App\Domains\Support\SupportService;
use App\Http\API\Data\SupportQuestionData;
use App\Http\API\Data\SupportQuestionRequestData;
use App\Http\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SupportController extends Controller
{
    public function __construct(private readonly SupportService $supportService) {}

    public function index(Course $course, Request $request): Collection
    {
        return SupportQuestionData::collect(
            $this->supportService->questionsFor($course, $request->user()),
        );
    }

    public function store(Course $course, SupportQuestionRequestData $data, Request $request): JsonResponse
    {
        $user = $request->user();
        assert($user instanceof User);

        $question = $this->supportService->createQuestion(
            $course,
            $user,
            $data,
        );

        return response()->json(SupportQuestionData::from($question), 201);
    }
}
