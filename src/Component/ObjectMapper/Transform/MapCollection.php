<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Component\ObjectMapper\Transform;

use App\Component\ObjectMapper\Exception\MappingException;
use App\Component\ObjectMapper\ObjectMapper;
use App\Component\ObjectMapper\ObjectMapperInterface;
use App\Component\ObjectMapper\TransformCallableWithContextInterface;

/**
 * @template T of object
 *
 * @implements TransformCallableWithContextInterface<object, T>
 */
class MapCollection implements TransformCallableWithContextInterface
{
    public function __construct(
        private ObjectMapperInterface $objectMapper,
    ) {
    }

    public function __invoke(mixed $value, object $source, ?object $target, array $context): mixed
    {
        if (!is_iterable($value)) {
            throw new MappingException(\sprintf('The MapCollection transform expects an iterable, "%s" given.', get_debug_type($value)));
        }

        $values = [];
        foreach ($value as $k => $v) {
            $values[$k] = $this->objectMapper->map($v, $context['targetClass'] ?? null);
        }

        return $values;
    }
}
