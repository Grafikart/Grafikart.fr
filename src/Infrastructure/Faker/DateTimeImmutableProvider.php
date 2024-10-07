<?php

namespace App\Infrastructure\Faker;
use Faker\Generator;
use Faker\Provider\Base as BaseProvider;

/**
 * Faker helper to create immutable dates
 */
final class DateTimeImmutableProvider extends BaseProvider
{
    public function __construct(Generator $generator)
    {
        parent::__construct($generator);
    }

    public function dateTimeImmutableBetween(string $start, string $end): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromMutable($this->generator->dateTimeBetween($start, $end));
    }

    public function dateTimeImmutableThisDecade(string $max = "now")
    {
        return \DateTimeImmutable::createFromMutable($this->generator->dateTimeThisDecade($max));
    }

    public function dateTimeImmutableThisMonth(string $max = "now")
    {
        return \DateTimeImmutable::createFromMutable($this->generator->dateTimeThisMonth($max));
    }

    public function dateTimeImmutableThisYear(?string $max = "now")
    {
        return \DateTimeImmutable::createFromMutable($this->generator->dateTimeThisYear($max));
    }

    public function dateTimeImmutable(string $max = "now")
    {
        return \DateTimeImmutable::createFromMutable($this->generator->dateTime($max));
    }


}
