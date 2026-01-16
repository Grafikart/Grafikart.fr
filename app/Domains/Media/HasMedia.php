<?php

namespace App\Domains\Media;

use Illuminate\Http\UploadedFile;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
interface HasMedia
{

    public function registerMedia(): void;

    public function attachMedia(UploadedFile $file, string $name): self;

}
