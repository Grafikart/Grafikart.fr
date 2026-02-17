<?php

namespace App\Domains\Evaluation;

enum QuestionType: string
{
    case Choice = 'choice';
    case Text = 'text';

    public function label(): string
    {
        return match ($this) {
            self::Choice => 'Choix multiple',
            self::Text => 'Texte libre',
        };
    }
}
