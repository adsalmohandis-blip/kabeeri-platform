<?php

use App\Http\Controllers\Web\PublicContentEntryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::scopeBindings()->group(function (): void {
    Route::get('/app/{site:slug}/{contentEntry:slug}', PublicContentEntryController::class)
        ->name('public.content-entry.show');
});
