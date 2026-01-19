<?php

use App\Infrastructure\Settings\SettingsService;

beforeEach(function () {
    $this->service = new SettingsService;
});

describe('get', function () {
    it('returns the value for an existing key', function () {
        $this->service->set('site_name', 'Grafikart');
        expect($this->service->get('site_name'))->toBe('Grafikart');
    });

    it('returns null for a non-existing key', function () {
        expect($this->service->get('non_existing'))->toBeNull();
    });

    it('returns the default value for a non-existing key', function () {
        expect($this->service->get('non_existing', 'default'))->toBe('default');
    });
});

describe('all', function () {
    it('returns an empty array when no settings exist', function () {
        expect($this->service->all())->toBe([]);
    });

    it('returns all setting values', function () {
        $this->service->set('site_name', 'Grafikart');
        $this->service->set('author', 'Jonathan');

        expect($this->service->all())->toEqual([
            'site_name' => 'Grafikart',
            'author' => 'Jonathan',
        ]);
    });
});

describe('set', function () {
    it('updates an existing setting', function () {
        $this->service->set('site_name', 'Grafikart');
        expect($this->service->get('site_name'))->toBe('Grafikart');
        $this->service->set('site_name', 'New Name');
        expect($this->service->get('site_name'))->toBe('New Name');
    });
});

describe('updateAll', function () {
    it('creates multiple settings', function () {
        $this->service->updateAll([
            'site_name' => 'Grafikart',
            'site_description' => 'Tutorials',
        ]);

        expect($this->service->get('site_name'))->toBe('Grafikart')
            ->and($this->service->get('site_description'))->toBe('Tutorials');
    });

    it('updates existing settings', function () {
        $this->service->updateAll([
            'site_name' => 'Old Name',
            'site_description' => 'Old Description',
        ]);

        $this->service->updateAll([
            'site_name' => 'New Name',
            'site_description' => 'New Description',
        ]);

        expect($this->service->get('site_name'))->toBe('New Name')
            ->and($this->service->get('site_description'))->toBe('New Description');
    });

    it('handles mixed create and update', function () {
        $this->service->set('existing_key', 'Old Value');

        $this->service->updateAll([
            'existing_key' => 'Updated Value',
            'new_key' => 'New Value',
        ]);

        expect($this->service->get('existing_key'))->toBe('Updated Value')
            ->and($this->service->get('new_key'))->toBe('New Value');
    });
});
