<?php

use App\Http\Controllers\Web\MallBusinessDirectoryController;
use App\Http\Controllers\Web\MallCourseController;
use App\Http\Controllers\Web\MallProductController;
use App\Http\Controllers\Web\MallServiceController;
use App\Http\Controllers\Web\MallTalentController;
use App\Http\Controllers\Web\MallTravelTourismController;
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
Route::get('/mall/services', [MallServiceController::class, 'index'])
    ->name('mall.services.index');
Route::get('/mall/services/{service:slug}', [MallServiceController::class, 'show'])
    ->name('mall.services.show');
Route::get('/mall/courses', [MallCourseController::class, 'index'])
    ->name('mall.courses.index');
Route::get('/mall/courses/{course:slug}', [MallCourseController::class, 'show'])
    ->name('mall.courses.show');
Route::get('/mall/talent', [MallTalentController::class, 'index'])
    ->name('mall.talent.index');
Route::get('/mall/talent/{talent:slug}', [MallTalentController::class, 'show'])
    ->name('mall.talent.show');
Route::get('/mall/travel', [MallTravelTourismController::class, 'index'])
    ->name('mall.travel.index');
Route::get('/mall/travel/{listing:slug}', [MallTravelTourismController::class, 'show'])
    ->name('mall.travel.show');

Route::scopeBindings()->group(function (): void {
    Route::get('/app/{site:slug}/{contentEntry:slug}', PublicContentEntryController::class)
        ->name('public.content-entry.show');
});
