<?php

namespace App\Tests;

/**
 * @template E
 */
class RepositoryTestCase extends KernelTestCase
{
    /**
     * @var E
     */
    protected $repository;

    /**
     * @var class-string<E>
     */
    protected $repositoryClass;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = self::getContainer()->get($this->repositoryClass);
    }
}
