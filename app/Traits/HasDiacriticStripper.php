<?php
declare(strict_types=1);

namespace App\Traits;

/**
 * Trait HasDiacriticStripper
 * Provides a helper to strip Arabic diacritics (Tashkeel) for normalization/search.
 */
trait HasDiacriticStripper
{
    /**
     * Remove Arabic diacritics (Tashkeel, Tatweel) from a string.
     *
     * @param string $text
     * @return string
     */
    public function stripDiacritics(string $text): string
    {
        // Arabic diacritics: Fatha, Damma, Kasra, Shadda, Sukun, Tatweel/Kashida, etc.
        $pattern = '/[\x{064B}-\x{0652}\x{0640}]/u';
        return preg_replace($pattern, '', $text);
    }
}
