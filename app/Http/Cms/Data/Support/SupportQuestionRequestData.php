<?php

namespace App\Http\Cms\Data\Support;

use App\Domains\Cms\DataToModel;
use App\Domains\Course\Course;
use App\Domains\Support\SupportQuestion;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class SupportQuestionRequestData extends Data implements DataToModel
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $content,
        public readonly ?string $answer,
        public readonly bool $online,
        public readonly int $timestamp,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'content' => ['nullable', 'string'],
            'answer' => ['nullable', 'string'],
            'online' => ['boolean'],
            'timestamp' => ['required', 'integer', 'min:0'],
        ];
    }

    public function toModel(Model $model): Model
    {
        assert($model instanceof SupportQuestion);

        $answer = trim((string) $this->answer);

        return $model->fill([
            'title' => trim($this->title),
            'content' => trim((string) $this->content),
            'answer' => $answer !== '' ? $answer : null,
            'online' => $this->online,
            'timestamp' => $this->timestamp,
        ]);
    }
}
