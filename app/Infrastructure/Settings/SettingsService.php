<?php

namespace App\Infrastructure\Settings;

class SettingsService
{
    /**
     * Find the setting for a specific key
     */
    public function get(string $key, ?string $default = null): ?string
    {
        return Setting::find($key)?->value ?? $default;
    }

    /**
     * Retrieve all the settings indexed by key
     */
    public function all(): array
    {
        return Setting::all()
            ->keyBy('key')
            ->map(fn (Setting $s) => $s->value)
            ->toArray();
    }

    /**
     * Update or insert multiple key / value settings
     *
     * @param  array<string, ?string>  $array
     */
    public function updateAll(array $array): void
    {
        $records = collect($array)
            ->map(fn (?string $value, string $key) => ['key' => $key, 'value' => $value])
            ->values()
            ->all();

        Setting::upsert($records, ['key'], ['value']);
    }

    public function set(string $key, ?string $value): void
    {
        $this->updateAll([$key => $value]);
    }
}
