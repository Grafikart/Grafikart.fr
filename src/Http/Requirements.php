<?php

namespace App\Http;

class Requirements
{
    public const ID = "\d+";
    public const SLUG = '[a-z0-9A-Z\-]+';
    public const YEAR = '\d{4}';
    public const ANY = '.+';
}
