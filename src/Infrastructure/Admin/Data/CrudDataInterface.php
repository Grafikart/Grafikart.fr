<?php


namespace App\Infrastructure\Admin\Data;


use Doctrine\ORM\EntityManagerInterface;

/**
 * @method hydrate(object $post, EntityManagerInterface $em)
 */
interface CrudDataInterface
{

    public function getEntity(): object;

    public function getFormClass(): string;

}
