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
    public function home(string $username): View
    {
        $site = $this->siteForUsername($username);
        $contentEntry = ContentEntry::query()
            ->where('site_id', $site->id)
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->orderByRaw("case when slug = 'home' then 0 else 1 end")
            ->latest('published_at')
            ->oldest('id')
            ->firstOrFail();

        return $this->renderContentEntry($site, $contentEntry);
    }

    public function __invoke(string $username, ContentEntry $contentEntry): View
    {
        $site = $this->siteForUsername($username);

        if ($contentEntry->site_id !== $site->id) {
            abort(404);
        }

        if ($contentEntry->status !== 'published' || $contentEntry->visibility !== 'public') {
            abort(404);
        }

        return $this->renderContentEntry($site, $contentEntry);
    }

    private function siteForUsername(string $username): Site
    {
        return Site::query()
            ->where('slug', $username)
            ->firstOrFail();
    }

    private function renderContentEntry(Site $site, ContentEntry $contentEntry): View
    {
        $themeSettings = ThemeSetting::query()
            ->where('site_id', $site->id)
            ->get()
            ->mapWithKeys(function (ThemeSetting $setting): array {
                return [$setting->key => $setting->value];
            })
            ->all();

        $siteSettings = is_array($site->settings) ? $site->settings : [];
        $primaryColor = $themeSettings['primary_color'] ?? $siteSettings['primary_color'] ?? '#000000';

        if (is_array($primaryColor)) {
            $primaryColor = $primaryColor['value'] ?? '#000000';
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
