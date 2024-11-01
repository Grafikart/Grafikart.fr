<?php

namespace App\Domain\Live;

use App\Helper\OptionManagerInterface;
use Psr\Cache\CacheItemPoolInterface;

class LiveService
{
    final public const OPTION_KEY = 'live_at';
    private ?\DateTimeImmutable $nextLiveDate = null;

    public function __construct(private readonly CacheItemPoolInterface $cache, private readonly OptionManagerInterface $optionManager)
    {
    }

    public function isLiveRunning(): bool
    {
        $liveDate = $this->getNextLiveDate();

        return
            $liveDate < new \DateTimeImmutable() &&
            $liveDate->modify('+5 hour') > new \DateTimeImmutable();
    }

    public function getNextLiveDate(): \DateTimeImmutable
    {
        if ($this->nextLiveDate) {
            return $this->nextLiveDate;
        }
        $cacheItem = $this->cache->getItem(self::OPTION_KEY);
        if (!$cacheItem->isHit()) {
            $this->cache->save(
                $cacheItem
                    ->expiresAfter(600)
                    ->set($this->optionManager->get(self::OPTION_KEY))
            );
        }
        $this->nextLiveDate = new \DateTimeImmutable($cacheItem->get() ?: '-1 day');

        return $this->nextLiveDate;
    }
}
