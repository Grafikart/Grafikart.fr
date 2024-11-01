<?php

namespace App\Tests;

use App\Tests\Constraint\ArraySubset;
use Doctrine\ORM\EntityManagerInterface;

class ApiTestCase extends WebTestCase
{
    protected EntityManagerInterface $em;

    public function assertJsonContains(array $subset, bool $checkForObjectIdentity = true, string $message = ''): void
    {
        if (!\is_array($subset)) {
            throw new \InvalidArgumentException('$subset must be array or string (JSON array or JSON object)');
        }

        $this->assertThat(
            json_decode($this->client->getResponse()->getContent(), true),
            new ArraySubset($subset, $checkForObjectIdentity),
            $message
        );
    }
}
