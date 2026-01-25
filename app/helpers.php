<?php

use Illuminate\Database\Eloquent\Model;
use TalesFromADev\TailwindMerge\TailwindMerge;

/**
 * @param  string[]  $args
 */
function cn(array $args)
{
    $tw = new TailwindMerge;

    return $tw->merge(Arr::toCssClasses($args));
}

function cache_key(mixed $expression): string
{
    if (is_array($expression)) {
        return implode('-', array_map(cache_key(...), $expression));
    }
    if ($expression instanceof Model) {
        /** @var object{id: int, updated_at: DateTimeInterface} $expression */
        return sprintf('%s-%s', $expression->id, $expression->updated_at->getTimestamp());
    }

    return $expression;
};
