<?php

namespace App\Core\Breadcrumb;

interface BreadcrumbGeneratorInterface
{
    /**
     * @param object $entity
     */
    public function generate($entity): array;

    public function support(object $object): bool;
}
