<?php

namespace App\Helpers;

use Symfony\Component\Intl\Countries;

class IntlHelper
{
    public static function countries(): array
    {
        return Countries::getNames('fr');
    }
}
