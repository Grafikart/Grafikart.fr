<?php

namespace App\Helpers;

use App\Domains\Blog\Post;
use App\Domains\Course\Course;
use App\Domains\Course\Formation;
use App\Domains\Course\Path;
use App\Domains\Forum\Topic;
use App\Http\Front\Data\PathViewData;

class UrlGenerator
{
    public function url(mixed $record, bool $absolute = false): string
    {
        return match (true) {
            $record instanceof Course => route('courses.show', ['slug' => $record->slug, 'course' => $record->id], $absolute),
            $record instanceof Formation => route('formations.show', ['formation' => $record->slug], $absolute),
            $record instanceof Post => route('blog.show', ['post' => $record->slug], $absolute),
            (($record instanceof Path) || ($record instanceof PathViewData)) => route('paths.show', ['slug' => $record->slug, 'path' => $record->id], $absolute),
            $record instanceof Topic => route('forum.topic', ['topic' => $record->id], $absolute),
            default => '/',
        };
    }
}
