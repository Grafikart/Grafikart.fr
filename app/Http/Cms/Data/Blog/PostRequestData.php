<?php

namespace App\Http\Cms\Data\Blog;

use App\Domains\Blog\Post;
use App\Domains\Cms\DataToModel;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class PostRequestData extends Data implements DataToModel
{
    public function __construct(
        #[Required]
        #[Min(2)]
        public readonly string $title,
        #[Required]
        #[Min(2)]
        public readonly string $slug,
        #[Nullable]
        #[Exists(table: 'blog_categories', column: 'id')]
        public readonly ?int $categoryId,
        public readonly bool $online,
        #[Nullable]
        #[Exists(table: 'attachments', column: 'id')]
        public readonly ?int $attachmentId,
        #[Required]
        public readonly string $content,
    ) {}

    public function toModel(Model $model): Model
    {
        assert($model instanceof Post);

        return $model->fill([
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'online' => $this->online,
            'category_id' => $this->categoryId,
            'attachment_id' => $this->attachmentId,
        ]);
    }
}
