<?php

namespace App\Infrastructure\Queue;

use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class ScheduledJobsService
{
    public function __construct(
        private readonly string $dsn,
        private readonly SerializerInterface $serializer
    ) {
    }

    public function getConnection(): \Redis
    {
        /** @var array $url */
        $url = parse_url($this->dsn);
        $redis = (new \Redis());
        if (!$redis->connect($url['host'], $url['port'])) {
            throw new \RuntimeException('Impossible de se connecter à redis');
        }

        return $redis;
    }

    /**
     * @return ScheduledJob[]
     */
    public function getJobs(): array
    {
        if (!str_starts_with($this->dsn, 'redis://')) {
            return [];
        }
        $messages = $this->getConnection()->zRange('messages__queue', 0, 10);
        if (empty($messages)) {
            return [];
        }
        $index = 0;
        $jobs = array_map(function (string $message) use (&$index) {
            return new ScheduledJob($this->serializer->decode(json_decode($message, true, 512, JSON_THROW_ON_ERROR)), $index++);
        }, $messages);

        return $jobs;
    }

    public function deleteJob(int $jobId): void
    {
        $this->getConnection()->zRemRangeByRank('messages__queue', $jobId, $jobId);
    }
}
