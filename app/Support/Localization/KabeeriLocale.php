<?php

namespace App\Support\Localization;

class KabeeriLocale
{
    /**
     * @return array<string, array{label: string, native_label: string, short_label: string, direction: string}>
     */
    public static function supported(): array
    {
        return config('kabeeri_localization.supported', []);
    }

    /**
     * @return array<int, string>
     */
    public static function codes(): array
    {
        return array_keys(self::supported());
    }

    public static function default(): string
    {
        return self::normalize(config('kabeeri_localization.default'));
    }

    public static function current(): string
    {
        return app()->getLocale();
    }

    public static function normalize(?string $locale): string
    {
        $locale = strtolower((string) $locale);

        return array_key_exists($locale, self::supported()) ? $locale : 'ar';
    }

    public static function isSupported(?string $locale): bool
    {
        return array_key_exists(strtolower((string) $locale), self::supported());
    }

    public static function direction(?string $locale = null): string
    {
        $locale = self::normalize($locale ?? self::current());

        return self::supported()[$locale]['direction'] ?? 'rtl';
    }

    public static function sessionKey(): string
    {
        return (string) config('kabeeri_localization.session_key', 'kabeeri_locale');
    }

    public static function queryKey(): string
    {
        return (string) config('kabeeri_localization.query_key', 'lang');
    }
}
