<?php

use Illuminate\Database\Eloquent\Model;

function app_url(mixed $model, bool $absolute = false)
{
    return app(\App\Helpers\UrlGenerator::class)->url($model, $absolute);
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
    if (! $expression) {
        return '';
    }

    return $expression;
};

function is_live_running(): bool
{
    return app(\App\Domains\Live\LiveService::class)->isLiveRunning();
}

function file_size(int|float $bytes, int $precision = 0, ?int $maxPrecision = null): string
{
    $units = ['o', 'ko', 'Mo', 'Go'];

    $unitCount = count($units);

    for ($i = 0; ($bytes / 1024) > 0.9 && ($i < $unitCount - 1); $i++) {
        $bytes /= 1024;
    }

    return sprintf('%s %s', Number::format($bytes, $precision, $maxPrecision), $units[$i]);
}

function duration(?int $duration): string
{
    if (! $duration) {
        return '';
    }
    $hours = floor($duration / 3600);
    $minutes = floor(($duration % 3600) / 60);
    if ($hours > 0) {
        return sprintf('%sh%s', $hours, str_pad((string) $minutes, 2, '0', STR_PAD_LEFT));
    }

    return $minutes.'min';
}
