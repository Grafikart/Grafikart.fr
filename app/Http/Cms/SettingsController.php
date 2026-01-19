<?php

namespace App\Http\Cms;

use App\Domains\Live\LiveService;
use App\Http\Cms\Data\SettingsFormData;
use App\Infrastructure\Settings\SettingsService;
use App\Infrastructure\Spam\SpamService;
use Inertia\Inertia;
use Inertia\Response;

final readonly class SettingsController
{
    public function __construct(private SettingsService $settings) {}

    public function index(): Response
    {
        $data = SettingsFormData::from($this->settings->all());

        return Inertia::render('settings/index', [
            'settings' => $data,
        ]);

    }

    public function store(SettingsFormData $data)
    {
        $this->settings->updateAll([
            LiveService::SETTING_KEY => $data->liveAt->toAtomString(),
            SpamService::SETTING_KEY => $data->spamWords,
        ]);

        return to_route('cms.settings.index')->with('success', 'Les paramètres ont bien été mis à jour');
    }
}
