<?php

namespace App\Support\Localization;

use Illuminate\Http\Request;

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

    public static function context(Request|string|null $request = null): string
    {
        if (is_string($request)) {
            $path = trim($request, '/');
        } else {
            $path = trim(($request ?? request())->path(), '/');
        }

        $segments = $path === '' ? [] : explode('/', $path);

        if (isset($segments[0]) && self::isSupported($segments[0])) {
            array_shift($segments);
        }

        $first = $segments[0] ?? '';

        return match ($first) {
            'admin', 'internal', 'ui' => 'admin',
            'customer' => 'customer',
            'app' => 'app_public',
            default => 'platform_public',
        };
    }

    public static function localeFromPath(Request $request): ?string
    {
        $first = explode('/', trim($request->path(), '/'))[0] ?? '';

        return self::isSupported($first) ? self::normalize($first) : null;
    }

    public static function pathWithoutLocale(string $path): string
    {
        $path = '/'.ltrim($path, '/');
        $segments = explode('/', trim($path, '/'));

        if (($segments[0] ?? '') !== '' && self::isSupported($segments[0])) {
            array_shift($segments);
        }

        return '/'.implode('/', $segments);
    }

    public static function localizedUrl(string $locale, ?string $path = null): string
    {
        $locale = self::normalize($locale);
        $path = self::pathWithoutLocale($path ?? request()->getRequestUri());
        $path = $path === '/' ? '' : $path;

        return url('/'.$locale.$path);
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

    public static function contextSessionKey(string $context): string
    {
        return config("kabeeri_localization.context_session_keys.{$context}")
            ?? self::sessionKey().'_'.$context;
    }

    public static function queryKey(): string
    {
        return (string) config('kabeeri_localization.query_key', 'lang');
    }
}
