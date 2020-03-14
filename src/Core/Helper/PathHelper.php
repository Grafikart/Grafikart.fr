<?php

namespace App\Core\Helper;

class PathHelper
{

    /**
     * @param array<int,string> $parts
     */
    public static function join(... $parts): string
    {
        return preg_replace('~[/\\\\]+~', DIRECTORY_SEPARATOR, implode(DIRECTORY_SEPARATOR, $parts)) ?: '';
    }
}
