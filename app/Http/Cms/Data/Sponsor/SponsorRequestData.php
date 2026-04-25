<?php

namespace App\Http\Cms\Data\Sponsor;

use App\Domains\Cms\DataToModel;
use App\Domains\Sponsorship\Sponsor;
use App\Domains\Sponsorship\SponsorType;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class SponsorRequestData extends Data implements DataToModel
{
    public function __construct(
        public string $name,
        public string $url,
        public string $content,
        public string $type = 'sponsor',
        public ?UploadedFile $logoFile = null,
        public ?CarbonImmutable $createdAt = null,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2'],
            'url' => ['required', 'url'],
            'content' => ['required', 'string'],
            'type' => ['required', 'string', Rule::enum(SponsorType::class)],
            'logoFile' => ['nullable', 'file', 'image:allow_svg'],
            'createdAt' => ['nullable', 'date'],
        ];
    }

    public function toModel(Model $model): Model
    {
        assert($model instanceof Sponsor);

        $model->fill([
            'name' => $this->name,
            'url' => $this->url,
            'content' => $this->content,
            'type' => $this->type,
            'created_at' => $this->createdAt,
        ]);
        $model->attachMedia($this->logoFile, 'logo');

        return $model;
    }
}
