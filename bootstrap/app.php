<?php

use App\Http\Middleware\SetKabeeriLocale;
use App\Support\Localization\KabeeriLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SetKabeeriLocale::class,
        ]);
        $middleware->redirectUsersTo(function (Request $request): string {
            $path = KabeeriLocale::pathWithoutLocale($request->path());

            return str_starts_with($path, '/admin')
                ? route('filament.admin.pages.dashboard')
                : route('customer.workspace');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
