<?php

use App\Http\Controllers\Web\MallBusinessDirectoryController;
use App\Http\Controllers\Web\MallProductController;
use App\Http\Controllers\Web\PublicContentEntryController;
use App\Http\Controllers\Web\RobotsController;
use App\Http\Controllers\Web\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', RobotsController::class)->name('robots');

Route::get('/mall/businesses', [MallBusinessDirectoryController::class, 'index'])
    ->name('mall.businesses.index');
Route::get('/mall/businesses/{business:slug}', [MallBusinessDirectoryController::class, 'show'])
    ->name('mall.businesses.show');
Route::get('/mall/products', [MallProductController::class, 'index'])
    ->name('mall.products.index');
Route::get('/mall/products/{product:slug}', [MallProductController::class, 'show'])
    ->name('mall.products.show');

Route::scopeBindings()->group(function (): void {
    Route::get('/app/{site:slug}/{contentEntry:slug}', PublicContentEntryController::class)
        ->name('public.content-entry.show');
});
