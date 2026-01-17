<?php

use App\Domains\Attachment\Attachment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('uploads');
    $this->user = new User;
});

describe('folders', function () {
    it('returns empty array when no attachments exist', function () {
        $this->actingAs($this->user)
            ->get(route('cms.attachments.folders'))
            ->assertOk()
            ->assertJson([]);
    });

    it('groups attachments by year and month', function () {
        Attachment::factory()->create(['created_at' => '2024-01-15']);
        Attachment::factory()->create(['created_at' => '2024-01-20']);
        Attachment::factory()->create(['created_at' => '2024-02-10']);

        $response = $this->actingAs($this->user)
            ->get(route('cms.attachments.folders'))
            ->assertOk();

        expect($response->json())->toHaveCount(2);
        expect($response->json())->toContainEqual(['path' => '2024/01', 'count' => 2]);
        expect($response->json())->toContainEqual(['path' => '2024/02', 'count' => 1]);
    });

    it('orders folders by date descending', function () {
        Attachment::factory()->create(['created_at' => '2023-06-01']);
        Attachment::factory()->create(['created_at' => '2024-01-01']);
        Attachment::factory()->create(['created_at' => '2023-12-01']);

        $response = $this->actingAs($this->user)
            ->get(route('cms.attachments.folders'))
            ->assertOk();

        $paths = collect($response->json())->pluck('path')->toArray();

        expect($paths)->toBe(['2024/01', '2023/12', '2023/06']);
    });
});

describe('index', function () {
    it('returns latest attachments when no filters provided', function () {
        Attachment::factory()->count(3)->create();

        $response = $this->actingAs($this->user)
            ->get(route('cms.attachments.index'))
            ->assertOk();

        expect($response->json())->toHaveCount(3);
    });

    it('limits latest attachments to 25', function () {
        Attachment::factory()->count(30)->create();

        $response = $this->actingAs($this->user)
            ->get(route('cms.attachments.index'))
            ->assertOk();

        expect($response->json())->toHaveCount(25);
    });

    it('filters attachments by path', function () {
        Attachment::factory()->create(['created_at' => '2024-01-15']);
        Attachment::factory()->create(['created_at' => '2024-01-20']);
        Attachment::factory()->create(['created_at' => '2024-02-10']);

        $response = $this->actingAs($this->user)
            ->get(route('cms.attachments.index', ['path' => '2024/01']))
            ->assertOk();

        expect($response->json())->toHaveCount(2);
    });

    it('searches attachments by name', function () {
        Attachment::factory()->create(['name' => 'photo-vacances.jpg']);
        Attachment::factory()->create(['name' => 'document.pdf']);
        Attachment::factory()->create(['name' => 'photo-famille.png']);

        $response = $this->actingAs($this->user)
            ->get(route('cms.attachments.index', ['q' => 'photo']))
            ->assertOk();

        expect($response->json())->toHaveCount(2);
    });

    it('returns validation error for invalid path format', function () {
        $this->actingAs($this->user)
            ->get(route('cms.attachments.index', ['path' => 'invalid']))
            ->assertInvalid(['path']);
    });

    it('returns validation error for invalid month in path', function () {
        $this->actingAs($this->user)
            ->get(route('cms.attachments.index', ['path' => '2024/13']))
            ->assertInvalid(['path']);
    });

    it('accepts valid path formats', function (string $path) {
        $this->actingAs($this->user)
            ->get(route('cms.attachments.index', ['path' => $path]))
            ->assertOk();
    })->with([
        '2024/01',
        '2024/12',
        '2023/06',
    ]);
});

describe('store', function () {
    it('creates an attachment with a valid image', function () {
        $file = UploadedFile::fake()->image('photo.jpg', 800, 600);

        $this->actingAs($this->user)
            ->post(route('cms.attachments.store'), ['file' => $file])
            ->assertStatus(201);

        $this->assertDatabaseCount('attachments', 1);

        $attachment = Attachment::first();
        expect($attachment->name)->toEndWith('.jpg');
        expect($attachment->size)->toBe($file->getSize());

        Storage::disk('uploads')->assertExists("attachments/{$attachment->created_at->year}/{$attachment->name}");
    });

    it('returns the created attachment data', function () {
        $file = UploadedFile::fake()->image('photo.png');

        $response = $this->actingAs($this->user)
            ->post(route('cms.attachments.store'), ['file' => $file])
            ->assertStatus(201);

        expect($response->json())->toHaveKeys(['id', 'name', 'size', 'url', 'createdAt']);
    });

    it('returns validation error when no file provided', function () {
        $this->actingAs($this->user)
            ->post(route('cms.attachments.store'), [])
            ->assertInvalid(['file']);
    });

    it('returns validation error when file is not an image', function () {
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $this->actingAs($this->user)
            ->post(route('cms.attachments.store'), ['file' => $file])
            ->assertInvalid(['file']);
    });
});

describe('destroy', function () {
    it('deletes an attachment', function () {
        $attachment = Attachment::factory()->create();

        $this->actingAs($this->user)
            ->delete(route('cms.attachments.destroy', $attachment))
            ->assertOk();

        $this->assertDatabaseMissing('attachments', ['id' => $attachment->id]);
    });

    it('removes the file from storage', function () {
        $file = UploadedFile::fake()->image('photo.jpg');

        $this->actingAs($this->user)
            ->post(route('cms.attachments.store'), ['file' => $file])
            ->assertStatus(201);

        $attachment = Attachment::first();
        $filePath = "attachments/{$attachment->created_at->year}/{$attachment->name}";

        Storage::disk('uploads')->assertExists($filePath);

        $this->actingAs($this->user)
            ->delete(route('cms.attachments.destroy', $attachment))
            ->assertOk();

        Storage::disk('uploads')->assertMissing($filePath);
    });
});
