<?php

namespace App\Domains\Support\Event;

use App\Domains\Support\SupportQuestion;

final readonly class SupportQuestionCreated
{
    public function __construct(public SupportQuestion $question) {}
}
