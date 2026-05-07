<?php

namespace App\Http\Controllers;

use App\Support\Localization\KabeeriLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocalizationController extends Controller
{
    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        abort_unless(KabeeriLocale::isSupported($locale), 404);

        $locale = KabeeriLocale::normalize($locale);
        $redirectTarget = $this->safeRedirectTarget($request);
        $context = KabeeriLocale::context($redirectTarget ?? $request);
        $request->session()->put(KabeeriLocale::contextSessionKey($context), $locale);
        $request->session()->put(KabeeriLocale::sessionKey(), $locale);
        $this->storeUserLocale($request, "{$context}_locale", $locale);

        return redirect()->to(KabeeriLocale::localizedUrl($locale, $redirectTarget ?? '/'));
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

    protected function storeUserLocale(Request $request, string $key, string $locale): void
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
            'metadata' => array_merge($profile->metadata ?? [], [$key => $locale]),
        ])->save();
    }
}
