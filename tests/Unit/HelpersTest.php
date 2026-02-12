<?php

describe('duration', function () {
    it('returns empty string for null', function () {
        expect(duration(null))->toBe('');
    });

    it('returns empty string for zero', function () {
        expect(duration(0))->toBe('');
    });

    it('returns minutes for durations under one hour', function () {
        expect(duration(60))->toBe('1min');
        expect(duration(120))->toBe('2min');
        expect(duration(1800))->toBe('30min');
        expect(duration(3540))->toBe('59min');
    });

    it('returns hours and minutes for durations of one hour or more', function () {
        expect(duration(3600))->toBe('1h00');
        expect(duration(3660))->toBe('1h01');
        expect(duration(5400))->toBe('1h30');
        expect(duration(7200))->toBe('2h00');
        expect(duration(7260))->toBe('2h01');
        expect(duration(9000))->toBe('2h30');
    });
});
