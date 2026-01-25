<?php

namespace App\Http\Cms\Data\Attachment;

use App\Domains\Attachment\Attachment;
use App\Domains\Cms\DataToModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Data;

/**
 * @implements DataToModel<Attachment>
 */
class AttachmentUploadData extends Data implements DataToModel
{
    public function __construct(
        public UploadedFile $file,
        public ?string $attachableType = null,
        public ?int $attachableId = null,
    ) {}

    public static function rules(): array
    {
        return [
            'file' => ['required', 'image'],
            'attachableType' => ['nullable', 'string'],
            'attachableId' => ['nullable', 'integer'],
        ];
    }

    public function toModel(Model $model): Model
    {
        assert($model instanceof Attachment);

        $model->created_at = now();
        $model->size = $this->file->getSize();
        $model->attachable_type = $this->attachableType;
        $model->attachable_id = $this->attachableId;
        $model->attachMedia($this->file, 'name');

        return $model;
    }
}
