<?php

namespace App\Http\Middleware;

use App\Support\Localization\KabeeriLocale;
use App\Support\Ui\KabeeriUiPreference;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetKabeeriLocale
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $context = KabeeriLocale::context($request);
        $contextSessionKey = KabeeriLocale::contextSessionKey($context);
        $pathLocale = KabeeriLocale::localeFromPath($request);
        $queryLocale = $request->query(KabeeriLocale::queryKey());

        if ($pathLocale !== null) {
            $request->session()->put($contextSessionKey, $pathLocale);
            $request->session()->put(KabeeriLocale::sessionKey(), $pathLocale);
        } elseif (is_string($queryLocale) && KabeeriLocale::isSupported($queryLocale)) {
            $locale = KabeeriLocale::normalize($queryLocale);
            $request->session()->put($contextSessionKey, $locale);
            $request->session()->put(KabeeriLocale::sessionKey(), $locale);
        } elseif (! $request->session()->has($contextSessionKey)) {
            $user = $request->user();
            $profile = ($user && method_exists($user, 'profile')) ? $user->profile()->first(['metadata']) : null;
            $profileLocale = $profile?->metadata["{$context}_locale"] ?? null;

            if (is_string($profileLocale) && KabeeriLocale::isSupported($profileLocale)) {
                $locale = KabeeriLocale::normalize($profileLocale);
                $request->session()->put($contextSessionKey, $locale);
                $request->session()->put(KabeeriLocale::sessionKey(), $locale);
            }
        }

        $locale = KabeeriLocale::normalize(
            $request->session()->get($contextSessionKey, KabeeriLocale::default()),
        );

        App::setLocale($locale);
        View::share('kabeeriLocale', [
            'current' => $locale,
            'direction' => KabeeriLocale::direction($locale),
            'supported' => KabeeriLocale::supported(),
            'context' => $context,
        ]);
        View::share('kabeeriUi', KabeeriUiPreference::viewData($request));

        return $next($request);
    }
}
