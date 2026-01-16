<?php

use App\Concerns\Media\HasMedia;
use App\Concerns\Media\WithMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('uploads');
});

function createFakeModel(): HasMedia|Model
{
    $model = new class extends Model implements HasMedia
    {
        use WithMedia;

        protected $guarded = [];

        protected $table = 'technologies';

        public function registerMedia(): void
        {
            $this->registerMediaForProperty(
                'image',
                directory: 'avatars',
                filename: fn (Model $model) => $model->slug,
            );
        }
    };
    $model->name = 'PHP';
    $model->slug = 'php';

    return $model;
}

it('attach media correctly', function () {
    $file = UploadedFile::fake()->image('photo.jpg');
    $model = createFakeModel();
    $model->attachMedia($file, 'image');
    $model->save();

    expect($model->image)->toBe('php.jpg');
    Storage::disk('uploads')->assertExists('avatars/php.jpg');
});

it('should detach previous file', function () {
    $file = UploadedFile::fake()->image('photo.jpg');
    $model = createFakeModel();
    $model->attachMedia($file, 'image');
    $model->save();
    Storage::disk('uploads')->assertExists('avatars/php.jpg');
    $model->slug = 'demo';
    $model->attachMedia($file, 'image');
    $model->save();
    Storage::disk('uploads')->assertMissing('avatars/php.jpg');
    Storage::disk('uploads')->assertExists('avatars/demo.jpg');
});

it('should detach file on delete', function () {
    $file = UploadedFile::fake()->image('photo.jpg');
    $model = createFakeModel();
    $model->attachMedia($file, 'image');
    $model->save();
    Storage::disk('uploads')->assertExists('avatars/php.jpg');
    $model->delete();
    Storage::disk('uploads')->assertMissing('avatars/php.jpg');
});
