<?php

namespace App\Infrastructure\Spam;

use App\Helper\OptionManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class SpamService
{
    private iterable $entities;

    private EntityManagerInterface $em;
    private OptionManagerInterface $optionManager;

    public function __construct(iterable $entities, EntityManagerInterface $em, OptionManagerInterface $optionManager)
    {
        $this->entities = $entities;
        $this->em = $em;
        $this->optionManager = $optionManager;
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

    /**
     * Renvoie la liste des mots constituant du spam.
     *
     * @return string[]
     */
    public function words(): array
    {
        $spamWords = preg_split('/\r\n|\r|\n/', $this->optionManager->get('spam_words') ?: '');

        return is_array($spamWords) ? $spamWords : [];
    }
}
