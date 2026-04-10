<?php

namespace App\Http\Cms\Data\Badge;

use App\Domains\Badge\Badge;
use App\Domains\Cms\DataToModel;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Data;

class BadgeRequestData extends Data implements DataToModel
{
    public function __construct(
        public string $name,
        public string $description,
        public int $position,
        public string $action,
        public int $actionCount = 0,
        public string $theme = 'grey',
        public ?string $image = null,
        public bool $unlockable = false,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2'],
            'description' => ['required', 'string', 'min:2'],
            'position' => ['required', 'integer', 'min:0'],
            'action' => ['required', 'string', 'min:2'],
            'actionCount' => ['required', 'integer', 'min:0'],
            'theme' => ['required', 'string', 'min:2'],
            'image' => ['nullable', 'string'],
            'unlockable' => ['boolean'],
        ];
    }

    public function toModel(Model $model): Model
    {
        assert($model instanceof Badge);

        $model->fill([
            'name' => $this->name,
            'description' => $this->description,
            'position' => $this->position,
            'action' => $this->action,
            'action_count' => $this->actionCount,
            'theme' => $this->theme,
            'image' => $this->image,
            'unlockable' => $this->unlockable,
        ]);

        return $model;
    }
}
