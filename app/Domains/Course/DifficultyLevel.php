<?php

namespace App\Domains\Course;

use App\Http\Cms\Data\OptionItemData;

enum DifficultyLevel: int
{
    case Junior = 0;
    case Intermediaire = 1;
    case Senior = 2;

    public function label(): string
    {
        return match ($this) {
            self::Junior => 'Junior',
            self::Intermediaire => 'Intermédiaire',
            self::Senior => 'Senior',
        };
    }

    /**
     * @return OptionItemData[]
     */
    public static function toOptions(): array
    {
        return array_map(fn (self $level) => new OptionItemData(id: $level->value, name: $level->label()), self::cases());
    }
}
