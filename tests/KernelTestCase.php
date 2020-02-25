<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class KernelTestCase extends \Symfony\Bundle\FrameworkBundle\Test\KernelTestCase
{

    protected KernelBrowser $client;
    protected EntityManagerInterface $em;

    public function setUp(): void
    {
        self::bootKernel();
        $this->em = self::$container->get(EntityManagerInterface::class);
        parent::setUp();
    }

    public function remove(object $entity): void
    {
        $this->em->remove($this->em->getRepository(get_class($entity))->find($entity->getId()));
    }

}
