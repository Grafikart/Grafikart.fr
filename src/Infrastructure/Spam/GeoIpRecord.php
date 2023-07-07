<?php

namespace App\Infrastructure\Spam;

readonly final class GeoIpRecord
{

    public function __construct(
        public string $country,
    )
    {
    }

    /**
     * Converts country code (ISO 3166-1) to its emoji flag representation (PL -> 🇵🇱).
     *
     * 0x1F1E5 is a code of character right before "REGIONAL INDICATOR SYMBOL LETTER A" (🇦).
     *
     * Since there are 32 letters in the ASCII and A is at code 65 (dec), modulo operation returns:
     *  - 1 for "A" and "a"
     *  - 2 for "B" and "b"
     */
    public function getEmoji(): string
    {
        return (string) preg_replace_callback(
            '/./',
            static fn (array $letter) => mb_chr(ord($letter[0]) % 32 + 0x1F1E5),
            $this->country
        );
    }

}
