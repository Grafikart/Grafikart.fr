<?php

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([
    ])->withSets([
        Rector\Set\ValueObject\LevelSetList::UP_TO_PHP_82,
        Rector\Symfony\Set\SymfonySetList::SYMFONY_64,
    ]);
