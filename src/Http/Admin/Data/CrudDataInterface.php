<?php

namespace App\Http\Admin\Data;

interface CrudDataInterface
{
    public function getEntity(): object;

    /**
     * @return class-string<\Symfony\Component\Form\FormTypeInterface<CrudDataInterface>>
     */
    public function getFormClass(): string;

    public function hydrate(): void;
}
