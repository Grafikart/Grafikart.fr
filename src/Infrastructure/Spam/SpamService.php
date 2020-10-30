<?php

namespace App\Infrastructure\Spam;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class SpamService
{
    private iterable $entities;

    private EntityManagerInterface $em;

    public function __construct(iterable $entities, EntityManagerInterface $em)
    {
        $this->entities = $entities;
        $this->em = $em;
    }

    public function count(): int
    {
        $count = 0;
        /** @var class-string $entity */
        foreach ($this->entities as $entity) {
            /** @var EntityRepository $repository */
            $repository = $this->em->getRepository($entity);
            $count += $repository->count(['spam' => true]);
        }

        return $count;
    }
}
