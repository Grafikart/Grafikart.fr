<?php

namespace App\Domain\Application;

use App\Domain\Application\Entity\Option as OptionEntity;
use App\Helper\OptionManagerInterface;
use Doctrine\ORM\EntityManagerInterface;

class OptionManager implements OptionManagerInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function get(string $key, ?string $default = null): ?string
    {
        $option = $this->em->getRepository(OptionEntity::class)->find($key);

        return null === $option ? $default : $option->getValue();
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

    public function all(?array $keys = null): array
    {
        if (null === $keys) {
            $options = $this->em->getRepository(OptionEntity::class)->findAll();
        } else {
            $options = $this->em->getRepository(OptionEntity::class)->findBy([
                    'key' => $keys,
                ]);
        }

        $optionsByKey = array_reduce($options, function (array $acc, OptionEntity $option) {
            $acc[$option->getKey()] = $option->getValue();

            return $acc;
        }, []);

        if (null === $keys) {
            return $optionsByKey;
        }

        return array_reduce($keys, function (array $acc, string $key) use ($optionsByKey) {
            $acc[$key] = $optionsByKey[$key] ?? null;

            return $acc;
        }, []);
    }
}
