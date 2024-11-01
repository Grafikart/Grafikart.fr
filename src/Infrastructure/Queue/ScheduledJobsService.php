<?php

namespace App\Infrastructure\Queue;

use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class ScheduledJobsService
{
    public function __construct(
        private readonly \Redis $redis,
        private readonly SerializerInterface $serializer,
    ) {
    }

    /**
     * @return ScheduledJob[]
     */
    public function getJobs(): array
    {
        $messages = $this->redis->zrange('messages__queue', 0, 10);
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
        $this->redis->zremrangebyrank('messages__queue', $jobId, $jobId);
    }
}
