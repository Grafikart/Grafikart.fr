<?php

namespace App\Http\Cms\Data\Comment;

use App\Domains\Cms\DataToModel;
use App\Domains\Comment\Comment;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class CommentRequestData extends Data implements DataToModel
{
    public function __construct(
        #[Required]
        public readonly string $content,
    ) {}

    public function toModel(Model $model): Model
    {
        assert($model instanceof Comment);

        return $model->fill([
            'content' => $this->content,
        ]);
    }
}
