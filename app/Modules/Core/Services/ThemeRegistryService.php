<?php

namespace App\Modules\Core\Services;

use App\Models\Site;
use App\Models\Theme;
use App\Models\ThemeSetting;

class ThemeRegistryService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function register(array $attributes): Theme
    {
        return Theme::query()->updateOrCreate(
            ['slug' => $attributes['slug']],
            $attributes,
        );
    }

    public function getBySlug(string $slug): ?Theme
    {
        return Theme::query()->where('slug', $slug)->first();
    }

    public function assignToSite(Site $site, Theme $theme): Site
    {
        $site->forceFill(['theme_id' => $theme->id])->save();

        return $site->refresh();
    }

    public function setSiteSetting(Site $site, Theme $theme, string $key, mixed $value): ThemeSetting
    {
        return ThemeSetting::query()->updateOrCreate(
            [
                'site_id' => $site->id,
                'theme_id' => $theme->id,
                'key' => $key,
            ],
            [
                'value' => ['value' => $value],
            ],
        );
    }

    public function getSiteSetting(Site $site, Theme $theme, string $key, mixed $default = null): mixed
    {
        $setting = ThemeSetting::query()
            ->where('site_id', $site->id)
            ->where('theme_id', $theme->id)
            ->where('key', $key)
            ->first();

        return $setting?->value['value'] ?? $default;
    }
}
