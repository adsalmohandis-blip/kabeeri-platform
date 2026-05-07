<?php

namespace App\Http\Middleware;

use App\Support\Localization\KabeeriLocale;
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
        $queryLocale = $request->query(KabeeriLocale::queryKey());

        if (is_string($queryLocale) && KabeeriLocale::isSupported($queryLocale)) {
            $request->session()->put(KabeeriLocale::sessionKey(), KabeeriLocale::normalize($queryLocale));
        }

        $locale = KabeeriLocale::normalize(
            $request->session()->get(KabeeriLocale::sessionKey(), KabeeriLocale::default()),
        );

        App::setLocale($locale);
        View::share('kabeeriLocale', [
            'current' => $locale,
            'direction' => KabeeriLocale::direction($locale),
            'supported' => KabeeriLocale::supported(),
        ]);

        return $next($request);
    }
}
