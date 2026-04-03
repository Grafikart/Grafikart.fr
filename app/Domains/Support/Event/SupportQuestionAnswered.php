<?php

namespace App\Domains\Support\Event;

use App\Domains\Support\SupportQuestion;

final readonly class SupportQuestionAnswered
{
    public function __construct(public SupportQuestion $question) {}
}
