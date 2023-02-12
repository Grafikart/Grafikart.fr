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
        $messages = $this->getConnection()->zrange('messages__queue', 0, 10);
        if (empty($messages)) {
            return [];
        }
        $index = 0;
        return array_map(function (string $message) use (&$index) {
            return new ScheduledJob($this->serializer->decode(json_decode($message, true)), $index++);
        }, $messages);
    }

    public function deleteJob(int $jobId): void
    {
        $this->getConnection()->zremrangebyrank('messages__queue', $jobId, $jobId);
    }
}
