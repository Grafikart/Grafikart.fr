<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;

class DatabaseTestCase extends \Symfony\Bundle\FrameworkBundle\Test\KernelTestCase
{
    protected EntityManagerInterface $em;

    use FixturesTrait;

    /**
     * @inheritdoc
     */
    public function setUp(): void
    {
        parent::setUp();
        static::bootKernel();
        $this->em = static::$container->get(EntityManagerInterface::class);
        $this->loader = static::$container->get('fidry_alice_data_fixtures.loader.doctrine');
        static::$container->get('doctrine')->getConnection()->beginTransaction();
    }

    public function tearDown(): void
    {
        static::$container->get('doctrine')->getConnection()->rollback();
        parent::tearDown();
    }

}
