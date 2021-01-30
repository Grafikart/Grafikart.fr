<?php

namespace App\Domain\Live;

use App\Helper\OptionManagerInterface;
use Psr\Cache\CacheItemPoolInterface;

class LiveService
{
    const OPTION_KEY = 'live_at';
    private CacheItemPoolInterface $cache;
    private OptionManagerInterface $optionManager;
    private ?\DateTimeImmutable $nextLiveDate = null;

    public function __construct(
        CacheItemPoolInterface $cache,
        OptionManagerInterface $optionManager
    ) {
        $this->cache = $cache;
        $this->optionManager = $optionManager;
    }

    public function isLiveRunning(): bool
    {
        $liveDate = $this->getNextLiveDate();

        return
            $liveDate < new \DateTime() &&
            $liveDate->modify('+2 hour') > new \DateTime();
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
