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

        $request->session()->put(KabeeriLocale::sessionKey(), KabeeriLocale::normalize($locale));

        return redirect()->to($this->safeRedirectTarget($request) ?? url()->previous(route('home')));
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
}
