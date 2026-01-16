<?php

use App\Domains\Media\MediaProperty;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function getModelWithAvatar(?string $avatar): Model
{
    $model = Mockery::mock(Model::class);
    $model->shouldReceive('getAttribute')
        ->with('avatar')
        ->andReturn($avatar);
    $model->shouldReceive('setAttribute');
    $model->exists = true;

    return $model;
}

beforeEach(function () {
    Storage::fake('uploads');
    $this->mediaProperty = new MediaProperty(
        property: 'avatar',
        namer: fn ($model) => 'avatars/user-1',
    );
});

it('deletes the file from storage', function () {
    Storage::disk('uploads')->put('avatars/user-1.jpg', 'content');

    $this->mediaProperty->delete(getModelWithAvatar('user-1.jpg'));

    Storage::disk('uploads')->assertMissing('avatars/user-1.jpg');
});

it('does nothing when no file is attached', function () {
    $this->mediaProperty->delete(getModelWithAvatar(null));

    expect(true)->toBeTrue();
});

it('attaches a file to the model', function () {
    $file = UploadedFile::fake()->image('photo.jpg');

    $result = $this->mediaProperty->attach(getModelWithAvatar(null), $file);

    expect($result)->toBe('user-1.jpg');
    Storage::disk('uploads')->assertExists('avatars/user-1.jpg');
});

it('deletes old file before attaching new one', function () {
    Storage::disk('uploads')->put('avatars/user-1.jpg', 'old content');

    $file = UploadedFile::fake()->image('new-photo.png');

    $this->mediaProperty->attach(getModelWithAvatar('user-1.jpg'), $file);

    Storage::disk('uploads')->assertMissing('avatars/user-1.jpg');
    Storage::disk('uploads')->assertExists('avatars/user-1.png');
});

it('uses custom disk when specified', function () {
    Storage::fake('s3');

    $file = UploadedFile::fake()->image('photo.jpg');

    $mediaProperty = new MediaProperty(
        property: 'avatar',
        namer: fn ($model) => 'avatars/user-1',
        disk: 's3',
    );

    $mediaProperty->attach(getModelWithAvatar(null), $file);

    Storage::disk('s3')->assertExists('avatars/user-1.jpg');
});
