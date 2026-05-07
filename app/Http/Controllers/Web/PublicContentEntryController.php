<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ContentEntry;
use App\Models\Site;
use App\Models\ThemeSetting;
use App\Support\Localization\KabeeriLocale;
use Illuminate\Contracts\View\View;

class PublicContentEntryController extends Controller
{
    public function __invoke(string $username, ContentEntry $contentEntry): View
    {
        $site = Site::query()
            ->where('slug', $username)
            ->firstOrFail();

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

        $language = app()->getLocale() ?: (string) ($site->language ?: 'ar');
        $direction = KabeeriLocale::direction($language);

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
