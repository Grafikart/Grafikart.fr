<?php

namespace App\Http\API;

use App\Http\API\Data\CountryData;
use Symfony\Component\Intl\Countries;

class CountryController
{
    public function index(): mixed
    {
        return CountryData::collect(
            collect(Countries::getNames('fr'))
                ->map(fn (string $name, string $code) => new CountryData(name: $name, code: $code))
                ->sortBy('name')
                ->values()
                ->all()
        );
    }
}
