<?php

use App\Concerns\Media\HasMedia;
use App\Concerns\Media\RegisterMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');

    Schema::create('media_test', function ($table) {
        $table->id();
        $table->string('name')->nullable();
        $table->timestamps();
    });
});

afterEach(function () {
    Schema::dropIfExists('media_test');
});

class IdNamedModel extends Model implements RegisterMedia
{
    use HasMedia;

    protected $table = 'media_test';

    public static function registerMedia(): void
    {
        static::registerMediaForProperty(property: 'name', directory: 'documents', filename: 'id', needId: true);
    }
}

class YearNamedModel extends Model implements RegisterMedia
{
    use HasMedia;

    protected $table = 'media_test';

    public static function registerMedia(): void
    {
        static::registerMediaForProperty(property: 'name', directory: 'documents', filename: fn (YearNamedModel $model) => $model->created_at->year, needId: true);
    }
}

it('should attach media correctly', function () {
    $model = new IdNamedModel;
    $model->attachMedia(\Illuminate\Http\UploadedFile::fake()->create('cv.pdf', 100), 'name');
    $model->updateTimestamps();
    $model->save();

    expect($model->name)->toBe(sprintf('%s.pdf', $model->id));
    Storage::disk('public')->assertExists(sprintf('documents/%d.pdf', $model->id));
});

it('should remove previous file', function () {
    $model = new IdNamedModel;
    $model->attachMedia(\Illuminate\Http\UploadedFile::fake()->create('cv.pdf', 100), 'name');
    $model->updateTimestamps();
    $model->save();

    $model->attachMedia(\Illuminate\Http\UploadedFile::fake()->create('video.mp4', 100), 'name');
    $model->updateTimestamps();
    $model->save();
    Storage::disk('public')->assertExists(sprintf('documents/%d.mp4', $model->id));
    Storage::disk('public')->assertMissing(sprintf('documents/%d.pdf', $model->id));
});

it('should remove file when the model is deleted', function () {
    $model = new IdNamedModel;
    $model->attachMedia(\Illuminate\Http\UploadedFile::fake()->create('cv.pdf', 100), 'name');
    $model->updateTimestamps();
    $model->save();

    $model->delete();
    Storage::disk('public')->assertMissing(sprintf('documents/%d.pdf', $model->id));
});

it('should handle namer function', function () {
    $model = new YearNamedModel;
    $model->created_at = new DateTimeImmutable('2022-04-01');
    $model->attachMedia(\Illuminate\Http\UploadedFile::fake()->create('cv.pdf', 100), 'name');
    $model->save();

    Storage::disk('public')->assertExists(sprintf('documents/2022.pdf', $model->id));
});

describe('write errors', function () {
    it('should not set name if error', function () {
        $file = \Illuminate\Http\UploadedFile::fake()->create('cv.pdf', 100);
        $mock = Mockery::mock($file)->makePartial();
        $mock->shouldReceive('storeAs')->andThrow(new \League\Flysystem\UnableToWriteFile('Could not write file'));

        $model = new IdNamedModel;
        $model->save();

        $model->attachMedia($mock, 'name');
        expect($model->name)->toBeNull();
        Storage::disk('public')->assertMissing(sprintf('documents/%d.pdf', $model->id));
    });

    it('should not delete previous file if error', function () {
        $model = new IdNamedModel;
        $model->attachMedia(\Illuminate\Http\UploadedFile::fake()->create('cv.pdf', 100), 'name');
        $model->save();

        expect($model->name)->toBe(sprintf('%s.pdf', $model->id));
        Storage::disk('public')->assertExists(sprintf('documents/%d.pdf', $model->id));

        $file = \Illuminate\Http\UploadedFile::fake()->create('cv.pdf', 100);
        $mock = Mockery::mock($file)->makePartial();
        $mock->shouldReceive('storeAs')->andThrow(new \League\Flysystem\UnableToWriteFile('Could not write file'));
        $model->attachMedia($mock, 'name');
        Storage::disk('public')->assertExists(sprintf('documents/%d.pdf', $model->id));
    });
});
