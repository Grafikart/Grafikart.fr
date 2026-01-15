<?php

namespace App\Domains\Cms\Event;

readonly class ContentUpdatedEvent
{

    public function __construct(public object $item){

    }

}
