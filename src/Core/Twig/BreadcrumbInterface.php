<?php

namespace App\Core\Twig;

interface BreadcrumbInterface
{
    /**
     * @param object $entity
     */
    public function generate($entity): array;

    public function support(object $object): bool;
}
