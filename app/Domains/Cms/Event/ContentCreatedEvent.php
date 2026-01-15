<?php

namespace App\Domains\Cms\Event;

readonly class ContentCreatedEvent
{

    public function __construct(public object $item){

    }

}
