<?php

namespace App\Infrastructure\Queue;

use Predis\Client;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class ScheduledJobsService
{
    public function __construct(
        private readonly string $dsn,
        private readonly SerializerInterface $serializer,
    ) {
    }

    public function getConnection(): Client
    {
        return new Client($this->dsn);
    }

    /**
     * @return ScheduledJob[]
     */
    public function getJobs(): array
    {
        if (!str_starts_with($this->dsn, 'redis://')) {
            return [];
        }
        $messages = $this->getConnection()->xrange('messages', '-', '+');
        if (empty($messages)) {
            return [];
        }
        $index = 0;
        $jobs = [];
        foreach ($messages as ['message' => $message]) {
            $jobs[] = new ScheduledJob(
                $this->serializer->decode(
                    json_decode(
                        unserialize($message),
                        true,
                        512,
                        JSON_THROW_ON_ERROR
                    )
                ),
                $index++
            );
        }

        return $jobs;
    }

    public function deleteJob(int $jobId): void
    {
        $this->getConnection()->zremrangebyrank('messages', $jobId, $jobId);
    }
}
