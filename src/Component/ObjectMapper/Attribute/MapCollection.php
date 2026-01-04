<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Component\ObjectMapper\Attribute;

use App\Component\ObjectMapper\Transform\EntityReferenceTransformer;
use App\Component\ObjectMapper\Transform\MapCollectionTransformer;

/**
 * Configures a class or a property to map to.
 *
 * @author Antoine Bluchet <soyuka@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class MapCollection extends Map
{
    public function __construct(
        string  $item,
        ?string $target = null,
        ?string $source = null,
        mixed   $if = null,
    ) {
        parent::__construct(
            source: $source,
            if: $if,
            transform: MapCollectionTransformer::class,
            context: ['targetClass' => $item],
            target: $target,
        );
    }
}
