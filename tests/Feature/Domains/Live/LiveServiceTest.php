<?php

use App\Domains\Live\LiveService;
use App\Infrastructure\Settings\SettingsService;
use Carbon\CarbonImmutable;
use Illuminate\Cache\Repository;

beforeEach(function () {
    $this->settings = Mockery::mock(SettingsService::class);
    $this->cache = Mockery::mock(Repository::class);
    $this->service = new LiveService($this->settings, $this->cache);
});

describe('isLiveRunning', function () {
    it('returns true when live started within last 5 hours', function () {
        $liveDate = CarbonImmutable::now()->subHour();

        $this->cache->shouldReceive('remember')
            ->once()
            ->andReturn($liveDate->toIso8601String());

        expect($this->service->isLiveRunning())->toBeTrue();
    });

    it('returns false when live started more than 5 hours ago', function () {
        $liveDate = CarbonImmutable::now()->subHours(6);

        $this->cache->shouldReceive('remember')
            ->once()
            ->andReturn($liveDate->toIso8601String());

        expect($this->service->isLiveRunning())->toBeFalse();
    });

    it('returns false when live is scheduled in the future', function () {
        $liveDate = CarbonImmutable::now()->addHour();

        $this->cache->shouldReceive('remember')
            ->once()
            ->andReturn($liveDate->toIso8601String());

        expect($this->service->isLiveRunning())->toBeFalse();
    });
});

describe('startLive', function () {
    it('sets the live date to 10 minutes ago', function () {
        CarbonImmutable::setTestNow('2024-01-15 14:00:00');

        $this->settings->shouldReceive('set')
            ->once()
            ->with(LiveService::SETTING_KEY, Mockery::on(function ($date) {
                return str_contains($date, '2024-01-15T13:50:00');
            }));

        $this->service->startLive();

        CarbonImmutable::setTestNow();
    });
});

describe('stopLive', function () {
    it('sets the live date to yesterday', function () {
        CarbonImmutable::setTestNow('2024-01-15 14:00:00');

        $this->settings->shouldReceive('set')
            ->once()
            ->with(LiveService::SETTING_KEY, Mockery::on(function ($date) {
                return str_contains($date, '2024-01-14T14:00:00');
            }));

        $this->service->stopLive();

        CarbonImmutable::setTestNow();
    });
});

describe('getNextLiveDate', function () {
    it('returns cached date', function () {
        $expectedDate = CarbonImmutable::now()->addDay();

        $this->cache->shouldReceive('remember')
            ->once()
            ->andReturn($expectedDate->toIso8601String());

        $result = $this->service->getNextLiveDate();

        expect($result->toIso8601String())->toBe($expectedDate->toIso8601String());
    });

    it('returns yesterday when no date is set', function () {
        CarbonImmutable::setTestNow('2024-01-15 14:00:00');

        $this->cache->shouldReceive('remember')
            ->once()
            ->andReturn(null);

        $result = $this->service->getNextLiveDate();

        expect($result->toDateString())->toBe('2024-01-14');

        CarbonImmutable::setTestNow();
    });

    it('caches the result for subsequent calls', function () {
        $expectedDate = CarbonImmutable::now()->addDay();

        $this->cache->shouldReceive('remember')
            ->once()
            ->andReturn($expectedDate->toIso8601String());

        $this->service->getNextLiveDate();
        $result = $this->service->getNextLiveDate();

        expect($result->toIso8601String())->toBe($expectedDate->toIso8601String());
    });
});
