<?php

namespace App\Support\Ui;

use Illuminate\Http\Request;

class KabeeriUiPreference
{
    public static function context(Request|string|null $request = null): string
    {
        if (is_string($request)) {
            $path = trim($request, '/');
        } else {
            $path = trim(($request ?? request())->path(), '/');
        }

        $segments = $path === '' ? [] : explode('/', $path);

        if (isset($segments[0]) && preg_match('/^[a-z]{2}$/i', $segments[0]) === 1) {
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

    public static function theme(Request $request): string
    {
        $context = self::context($request);
        $theme = (string) $request->session()->get(
            self::themeKey($context),
            self::userMetadataPreference($request, "{$context}_theme")
                ?? config('kabeeri_ui_preferences.default_theme', 'light'),
        );

        return self::supportedTheme($theme);
    }

    public static function font(Request $request): string
    {
        $context = self::context($request);
        $font = (string) $request->session()->get(
            self::fontKey($context),
            self::userMetadataPreference($request, "{$context}_font")
                ?? config('kabeeri_ui_preferences.default_font', 'ibm-plex-sans-arabic'),
        );

        return self::supportedFont($font);
    }

    public static function themeKey(string $context): string
    {
        return config('kabeeri_ui_preferences.theme_session_prefix', 'kabeeri_theme_').$context;
    }

    public static function fontKey(string $context): string
    {
        return config('kabeeri_ui_preferences.font_session_prefix', 'kabeeri_font_').$context;
    }

    public static function supportedTheme(string $theme): string
    {
        return array_key_exists($theme, config('kabeeri_ui_preferences.themes', [])) ? $theme : 'light';
    }

    public static function supportedFont(string $font): string
    {
        return array_key_exists($font, config('kabeeri_ui_preferences.fonts', [])) ? $font : 'ibm-plex-sans-arabic';
    }

    public static function fontFamily(string $font): string
    {
        return config("kabeeri_ui_preferences.fonts.{$font}.family")
            ?? config('kabeeri_ui_preferences.fonts.ibm-plex-sans-arabic.family');
    }

    private static function userMetadataPreference(Request $request, string $key): ?string
    {
        $user = $request->user();

        if (! $user) {
            return null;
        }

        if (! method_exists($user, 'profile')) {
            return null;
        }

        $profile = $user->profile()->first(['metadata']);
        $value = $profile?->metadata[$key] ?? null;

        return is_string($value) ? $value : null;
    }

    /**
     * @return array<string, mixed>
     */
    public static function viewData(Request $request): array
    {
        $font = self::font($request);

        return [
            'context' => self::context($request),
            'theme' => self::theme($request),
            'font' => $font,
            'font_family' => self::fontFamily($font),
            'themes' => config('kabeeri_ui_preferences.themes', []),
            'fonts' => config('kabeeri_ui_preferences.fonts', []),
        ];
    }
}
