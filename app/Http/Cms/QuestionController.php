<?php

namespace App\Http\Cms;

use App\Domains\Course\Course;
use App\Domains\Evaluation\Question;
use App\Http\Cms\Data\Question\QuestionData;
use App\Http\Cms\Data\Question\QuestionImportData;
use App\Http\Cms\Data\Question\QuestionRequestData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class QuestionController
{
    /**
     * @return Collection<int, QuestionData>
     */
    public function index(Course $course): Collection
    {
        return QuestionData::collect($course->questions);
    }

    public function store(Course $course, QuestionRequestData $data): JsonResponse
    {
        $question = $course->questions()->create($data->toArray());

        return response()->json(QuestionData::from($question), 201);
    }

    public function update(Question $question, QuestionRequestData $data): QuestionData
    {
        $question->update($data->toArray());

        return QuestionData::from($question);
    }

    public function destroy(Question $question): Response
    {
        $question->delete();

        return response()->noContent();
    }

    public function import(Course $course, QuestionImportData $data): JsonResponse
    {
        $created = [];
        foreach ($data->questions as $item) {
            $created[] = $course->questions()->create($item->toArray());
        }

        return response()->json(QuestionData::collect($created), 201);
    }
}
