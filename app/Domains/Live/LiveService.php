<?php

namespace App\Domains\Live;

use App\Infrastructure\Settings\SettingsService;
use Carbon\CarbonImmutable;
use Illuminate\Cache\Repository;

/**
 * Retrieve the twitch live date
 */
final class LiveService
{
    public const SETTING_KEY = 'live_at';

    private ?CarbonImmutable $nextLiveDate = null;

    public function __construct(
        public SettingsService $settings,
        public Repository $cache,
    ) {}

    public function isLiveRunning(): bool
    {
        $liveDate = $this->getNextLiveDate();

        return $liveDate->isPast() && $liveDate->addHours(5)->isNowOrFuture();
    }

    public function startLive(): void
    {
        $this->settings->set(self::SETTING_KEY, now()->subMinutes(10)->toIso8601String());
    }

    public function stopLive(): void
    {
        $this->settings->set(self::SETTING_KEY, now()->subDay()->toIso8601String());
    }

    public function getNextLiveDate(): CarbonImmutable
    {
        if ($this->nextLiveDate) {
            return $this->nextLiveDate;
        }
        $date = $this->cache->remember(
            self::SETTING_KEY,
            600,
            fn () => $this->settings->get(self::SETTING_KEY, now()->subDays(2)->toIso8601String())
        );
        if (! $date) {
            return CarbonImmutable::now()->subDay();
        }
        $this->nextLiveDate = CarbonImmutable::parse($date);

        return $this->nextLiveDate;
    }
}
