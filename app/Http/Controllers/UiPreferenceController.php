<?php

namespace App\Http\Controllers;

use App\Support\Ui\KabeeriUiPreference;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UiPreferenceController extends Controller
{
    public function theme(Request $request, string $theme): RedirectResponse
    {
        $theme = KabeeriUiPreference::supportedTheme($theme);
        $redirectTarget = $this->safeRedirectTarget($request);
        $context = KabeeriUiPreference::context($redirectTarget ?? $request);

        $request->session()->put(KabeeriUiPreference::themeKey($context), $theme);
        $this->storeUserPreference($request, "{$context}_theme", $theme);

        return redirect()->to($redirectTarget ?? url()->previous(route('home')));
    }

    public function font(Request $request, string $font): RedirectResponse
    {
        $font = KabeeriUiPreference::supportedFont($font);
        $redirectTarget = $this->safeRedirectTarget($request);
        $context = KabeeriUiPreference::context($redirectTarget ?? $request);

        abort_if($context === 'platform_public', 403);

        $request->session()->put(KabeeriUiPreference::fontKey($context), $font);
        $this->storeUserPreference($request, "{$context}_font", $font);

        return redirect()->to($redirectTarget ?? url()->previous(route('home')));
    }

    protected function safeRedirectTarget(Request $request): ?string
    {
        $target = (string) $request->query('redirect', '');

        if ($target === '') {
            return null;
        }

        if (str_starts_with($target, '/') && ! str_starts_with($target, '//')) {
            return $target;
        }

        return null;
    }

    protected function storeUserPreference(Request $request, string $key, string $value): void
    {
        $user = $request->user();

        if (! $user) {
            return;
        }

        $profile = $user->profile()->firstOrCreate(
            ['user_id' => $user->id],
            ['visibility' => 'private'],
        );

        $profile->forceFill([
            'metadata' => array_merge($profile->metadata ?? [], [$key => $value]),
        ])->save();
    }
}
