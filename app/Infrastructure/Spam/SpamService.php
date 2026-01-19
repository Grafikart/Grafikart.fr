<?php

namespace App\Infrastructure\Spam;

use App\Infrastructure\Settings\SettingsService;

final readonly class SpamService
{
    public const string SETTING_KEY = 'spam_words';

    public function __construct(
        private SettingsService $settings
    ) {}

    /**
     * Find the list of spam words
     *
     * @return string[]
     */
    public function words(): array
    {
        $spamWords = $this->settings->get(self::SETTING_KEY);
        $wordList = preg_split('/\r\n|\r|\n/', $spamWords);
        if (! is_array($wordList)) {
            return [];
        }

        return collect($wordList)
            ->map(fn (string $word) => trim($word))
            ->filter(fn (string $word) => ! empty($word))
            ->toArray();
    }
}
