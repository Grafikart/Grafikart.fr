<?php

use TalesFromADev\TailwindMerge\TailwindMerge;

/**
 * @param  string[]  $args
 */
function cn(array $args)
{
    $tw = new TailwindMerge;

    return $tw->merge(Arr::toCssClasses($args));
}
