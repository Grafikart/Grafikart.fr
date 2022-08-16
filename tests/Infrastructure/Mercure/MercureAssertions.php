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
        $this->assertNotNull(HubStub::$lastUpdate);
        /** @var Update $update */
        $update = HubStub::$lastUpdate;
        $this->assertContains($topic, $update->getTopics());
    }
}
