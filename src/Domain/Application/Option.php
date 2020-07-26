<?php

namespace App\Domain\Application;

use App\Core\OptionInterface;
use App\Domain\Application\Entity\Option as OptionEntity;
use Doctrine\ORM\EntityManagerInterface;

class Option implements OptionInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function get(string $key): ?string
    {
        $option = $this->em->getRepository(OptionEntity::class)->find($key);

        return null === $option ? null : $option->getValue();
    }

    public function set(string $key, string $value): void
    {
        $option = $this->em->getRepository(OptionEntity::class)->find($key);
        if (null === $option) {
            $option = (new OptionEntity())
                ->setKey($key)
                ->setValue($value);
            $this->em->persist($option);
        } else {
            $option->setValue($value);
        }
        $this->em->flush();
    }

    public function delete(string $key): void
    {
        $option = $this->em->getRepository(OptionEntity::class)->find($key);
        if (null !== $option) {
            $this->em->remove($option);
        }
        $this->em->flush();
    }
}
