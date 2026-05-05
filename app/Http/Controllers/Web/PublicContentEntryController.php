<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ContentEntry;
use App\Models\Site;
use App\Models\ThemeSetting;
use Illuminate\Contracts\View\View;

class PublicContentEntryController extends Controller
{
    public function __invoke(Site $site, ContentEntry $contentEntry): View
    {
        if ($contentEntry->site_id !== $site->id) {
            abort(404);
        }

        if ($contentEntry->status !== 'published' || $contentEntry->visibility !== 'public') {
            abort(404);
        }

        $themeSettings = ThemeSetting::query()
            ->where('site_id', $site->id)
            ->get()
            ->mapWithKeys(function (ThemeSetting $setting): array {
                return [$setting->key => $setting->value];
            })
            ->all();

        $siteSettings = is_array($site->settings) ? $site->settings : [];
        $primaryColor = $themeSettings['primary_color'] ?? $siteSettings['primary_color'] ?? '#b45309';

        if (is_array($primaryColor)) {
            $primaryColor = $primaryColor['value'] ?? '#b45309';
        }

        $language = (string) ($site->language ?: app()->getLocale());
        $direction = in_array($language, ['ar', 'fa', 'he', 'ur'], true)
            || (bool) ($site->theme?->supports_rtl ?? false)
            ? 'rtl'
            : 'ltr';

        return view('themes.kabeeri-starter.content-entry', [
            'site' => $site,
            'contentEntry' => $contentEntry,
            'pageTitle' => $contentEntry->title.' | '.$site->name,
            'themeName' => $site->theme?->name ?? 'Kabeeri Starter Theme',
            'language' => $language,
            'direction' => $direction,
            'primaryColor' => (string) $primaryColor,
        ]);
    }
}
