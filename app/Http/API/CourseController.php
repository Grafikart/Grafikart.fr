<?php

namespace App\Http\API;

use App\Domains\Course\Course;

class CourseController
{
    public function vtt(Course $course)
    {
        $matches = [];
        preg_match_all("/((?:\d{2}:){1,2}\d{2}) ([^\n]*)/", $course->content, $matches, PREG_SET_ORDER);

        return response(view('courses.vtt', [
            'chapters' => collect($matches)->map(fn (array $match, int $k) => [
                'start' => $this->formatTime($match[1]),
                'end' => $this->formatTime($k + 1 >= count($matches) ? $course->duration : $matches[$k + 1][1]),
                'title' => trim($match[2]),
            ]),
        ]), 200, [
            'Content-Type' => 'text/vtt',
        ]);
    }

    /**
     * Format a time (number of seconds or time formatted) into a vtt compatible format
     */
    private function formatTime(string|int $time): string
    {

        if (is_int($time)) {
            return gmdate('H:i:s', $time).'.000';
        }
        if (strlen($time) === 5) {
            return '00:'.$time.'.000';
        }

        return $time.'.000';
    }
}
