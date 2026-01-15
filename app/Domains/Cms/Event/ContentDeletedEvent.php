<?php

namespace App\Domains\Cms\Event;

readonly class ContentDeletedEvent
{

    public function __construct(public object $item){

    }

}
