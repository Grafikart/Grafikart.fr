<?php

namespace App\Domains\Course;

use App\Domains\Course\Subscriber\CourseSubscriber;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class CourseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Event::subscribe(CourseSubscriber::class);
    }
}
