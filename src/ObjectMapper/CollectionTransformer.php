<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\ObjectMapper;

use App\Component\ObjectMapper\Exception\MappingException;
use App\Component\ObjectMapper\ObjectMapperInterface;
use App\Component\ObjectMapper\TransformCallableInterface;
use App\Http\Admin\Data\Course\TechnologyListItemData;

/**
 * @template T of object
 *
 * @implements TransformCallableInterface<object, T>
 */
readonly class CollectionTransformer implements TransformCallableInterface
{
    public function __construct(
        private ObjectMapperInterface $objectMapper,
    ) {
    }

    public function __invoke(mixed $value, object $source, ?object $target): mixed
    {
        if (!is_iterable($value)) {
            throw new MappingException(\sprintf('The MapCollection transform expects an iterable, "%s" given.', get_debug_type($value)));
        }
        if (!$target) {
            throw new MappingException("A target must be specified for CollectionTransformer");
        }

        $values = [];
        foreach ($value as $k => $v) {
            $values[$k] = $this->objectMapper->map($v, TechnologyListItemData::class);
        }

        return $values;
    }
}
