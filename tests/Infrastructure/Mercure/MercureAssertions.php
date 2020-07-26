<?php

namespace App\Tests\Infrastructure\Mercure;

use Symfony\Component\Mercure\Update;

trait MercureAssertions
{
    /**
     * Vérifie qu'une publication a eu lieu sur Mercure.
     */
    public function assertPublishedOnTopic(string $topic): void
    {
        $this->assertNotNull(PublisherStub::$lastUpdate);
        /** @var Update $update */
        $update = PublisherStub::$lastUpdate;
        $this->assertContains($topic, $update->getTopics());
    }
}
