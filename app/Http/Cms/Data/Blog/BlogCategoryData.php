<?php

namespace App\Http\Cms\Data\Blog;

use App\Domains\Blog\BlogCategory;
use App\Domains\Cms\DataToModel;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class BlogCategoryData extends Data implements DataToModel
{

    public function __construct(
        readonly public ?int $id = null,
        #[Required]
        #[Min(2)]
        readonly public string $name = '',
        #[Min(2)]
        #[Unique(table: 'blog_categories')]
        readonly public string $slug = '',
    ){

    }

    public function toModel(Model $model): Model
    {
        assert($model instanceof BlogCategory);
        return $model->fill([
            'name' => $this->name,
            'slug' => $this->slug
        ]);
    }
}
