<?php

use App\Http\Controllers\Desktop\DesktopSyncController;
use App\Http\Controllers\LocalizationController;
use App\Http\Controllers\Mobile\MobileAuthController;
use App\Http\Controllers\Mobile\MobileDeviceController;
use App\Http\Controllers\Mobile\MobilePublicController;
use App\Http\Controllers\Public\PublicWebRuntimeController;
use App\Http\Controllers\UiPreferenceController;
use App\Http\Controllers\Web\AdminLoginEntryController;
use App\Http\Controllers\Web\CustomerAuthController;
use App\Http\Controllers\Web\CustomerStartController;
use App\Http\Controllers\Web\CustomerWorkspaceController;
use App\Http\Controllers\Web\ExternalPortalController;
use App\Http\Controllers\Web\MallBusinessDirectoryController;
use App\Http\Controllers\Web\MallCourseController;
use App\Http\Controllers\Web\MallHomeController;
use App\Http\Controllers\Web\MallProductController;
use App\Http\Controllers\Web\MallServiceController;
use App\Http\Controllers\Web\MallTalentController;
use App\Http\Controllers\Web\MallTravelTourismController;
use App\Http\Controllers\Web\MarketplaceDeveloperController;
use App\Http\Controllers\Web\PublicContentEntryController;
use App\Http\Controllers\Web\PublicMarketingController;
use App\Http\Controllers\Web\RobotsController;
use App\Http\Controllers\Web\SitemapController;
use App\Http\Controllers\Web\UiReleaseCandidateController;
use App\Support\Localization\KabeeriLocale;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('public.business-client-home');
})->name('home');

Route::get('/internal/command-center', fn () => redirect()->route('filament.admin.pages.development-status'))
    ->middleware('auth')
    ->name('system.command-center');
Route::get('/platform-admin/login', AdminLoginEntryController::class)->name('admin.login.entry');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', RobotsController::class)->name('robots');
Route::get('/ui/release-candidate', UiReleaseCandidateController::class)->name('ui.release-candidate');
Route::get('/language/{locale}', LocalizationController::class)
    ->where('locale', '[A-Za-z]{2}')
    ->name('language.switch');
Route::get('/ui/theme/{theme}', [UiPreferenceController::class, 'theme'])->name('ui.theme');
Route::get('/ui/font/{font}', [UiPreferenceController::class, 'font'])->name('ui.font');

Route::get('/start', CustomerStartController::class)->name('customer.start');
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [CustomerAuthController::class, 'login'])->name('login.store');
    Route::get('/register', [CustomerAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [CustomerAuthController::class, 'register'])->name('register.store');
});
Route::post('/logout', [CustomerAuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/public', [PublicMarketingController::class, 'landing'])->name('public.landing');
Route::get('/for', [PublicMarketingController::class, 'audiences'])->name('public.audiences');
Route::get('/for/business-owners', [PublicMarketingController::class, 'business'])->name('public.business');
Route::get('/for/enterprise', [PublicMarketingController::class, 'enterprise'])->name('public.enterprise');
Route::get('/for/developers-creators', [PublicMarketingController::class, 'developers'])->name('public.developers');
Route::get('/for/marketers-partners', [PublicMarketingController::class, 'partners'])->name('public.partners');
Route::get('/wordpress-alternative', [PublicMarketingController::class, 'wordpress'])->name('public.wordpress');
Route::get('/use-cases/service-business', [PublicMarketingController::class, 'serviceBusiness'])->name('public.service-business');
Route::get('/templates', [PublicMarketingController::class, 'templates'])->name('public.templates');
Route::get('/onboarding', [PublicMarketingController::class, 'onboarding'])->name('public.onboarding');
Route::get('/onboarding/workspace-setup', [PublicMarketingController::class, 'workspaceSetup'])->name('public.workspace-setup');
Route::get('/pricing', [PublicMarketingController::class, 'pricing'])->name('public.pricing');
Route::get('/trust', [PublicMarketingController::class, 'trust'])->name('public.trust');
Route::get('/contact-sales', [PublicMarketingController::class, 'contact'])->name('public.contact');
Route::post('/contact-sales', [PublicMarketingController::class, 'storeInquiry'])->name('public.contact.store');

Route::get('/customer', function (Request $request, ExternalPortalController $portal) {
    return $request->user()
        ? redirect()->route('customer.workspace')
        : $portal->customerDashboard();
})->name('customer.dashboard');
Route::get('/customer/theme-plugins', [ExternalPortalController::class, 'customerThemePlugins'])->name('customer.theme-plugins');
Route::get('/customer/quick-setup', [ExternalPortalController::class, 'customerQuickSetup'])->name('customer.quick-setup');
Route::middleware('auth')->prefix('customer')->name('customer.')->group(function (): void {
    Route::get('/onboarding', [CustomerWorkspaceController::class, 'onboarding'])->name('onboarding');
    Route::post('/onboarding', [CustomerWorkspaceController::class, 'storeOnboarding'])->name('onboarding.store');
    Route::get('/dashboard', [CustomerWorkspaceController::class, 'dashboard'])->name('workspace');
    Route::get('/apps', [CustomerWorkspaceController::class, 'apps'])->name('apps.index');
    Route::get('/apps/create', [CustomerWorkspaceController::class, 'createApp'])->name('apps.create');
    Route::post('/apps', [CustomerWorkspaceController::class, 'storeApp'])->name('apps.store');
    Route::get('/apps/trash', [CustomerWorkspaceController::class, 'trash'])->name('apps.trash');
    Route::post('/apps/trash/{username}/restore', [CustomerWorkspaceController::class, 'restoreApp'])
        ->where('username', '[A-Za-z0-9][A-Za-z0-9_-]*')
        ->name('apps.restore');
    Route::patch('/apps/trash/{username}/schedule-delete', [CustomerWorkspaceController::class, 'scheduleTrashPurge'])
        ->where('username', '[A-Za-z0-9][A-Za-z0-9_-]*')
        ->name('apps.schedule-delete');
    Route::get('/apps/{username}/edit', [CustomerWorkspaceController::class, 'editApp'])
        ->where('username', '[A-Za-z0-9][A-Za-z0-9_-]*')
        ->name('apps.edit');
    Route::put('/apps/{username}', [CustomerWorkspaceController::class, 'updateApp'])
        ->where('username', '[A-Za-z0-9][A-Za-z0-9_-]*')
        ->name('apps.update');
    Route::delete('/apps/{username}', [CustomerWorkspaceController::class, 'trashApp'])
        ->where('username', '[A-Za-z0-9][A-Za-z0-9_-]*')
        ->name('apps.destroy');
    Route::get('/apps/{username}/themes', [CustomerWorkspaceController::class, 'themes'])
        ->where('username', '[A-Za-z0-9][A-Za-z0-9_-]*')
        ->name('apps.themes');
    Route::patch('/apps/{username}/themes', [CustomerWorkspaceController::class, 'switchTheme'])
        ->where('username', '[A-Za-z0-9][A-Za-z0-9_-]*')
        ->name('apps.themes.update');
    Route::get('/apps/{username}/plugins', [CustomerWorkspaceController::class, 'plugins'])
        ->where('username', '[A-Za-z0-9][A-Za-z0-9_-]*')
        ->name('apps.plugins');
    Route::post('/apps/{username}/plugins/{package}', [CustomerWorkspaceController::class, 'installPlugin'])
        ->where('username', '[A-Za-z0-9][A-Za-z0-9_-]*')
        ->where('package', '[A-Za-z0-9][A-Za-z0-9_-]*')
        ->name('apps.plugins.install');
    Route::patch('/apps/{username}/plugins/{package}/deactivate', [CustomerWorkspaceController::class, 'deactivatePlugin'])
        ->where('username', '[A-Za-z0-9][A-Za-z0-9_-]*')
        ->where('package', '[A-Za-z0-9][A-Za-z0-9_-]*')
        ->name('apps.plugins.deactivate');
    Route::patch('/apps/{username}/plugins/{package}/activate', [CustomerWorkspaceController::class, 'activatePlugin'])
        ->where('username', '[A-Za-z0-9][A-Za-z0-9_-]*')
        ->where('package', '[A-Za-z0-9][A-Za-z0-9_-]*')
        ->name('apps.plugins.activate');
    Route::get('/apps/{username}', [CustomerWorkspaceController::class, 'showSite'])
        ->where('username', '[A-Za-z0-9][A-Za-z0-9_-]*')
        ->name('apps.show');
    Route::post('/profile/capabilities', [CustomerWorkspaceController::class, 'updateCapabilities'])->name('capabilities.update');
});

Route::get('/partners', [ExternalPortalController::class, 'partnerLanding'])->name('partners.landing');
Route::get('/partners/agency-profile', [ExternalPortalController::class, 'agencyProfile'])->name('partners.agency-profile');
Route::get('/partners/storefront', [ExternalPortalController::class, 'partnerStorefront'])->name('partners.storefront');
Route::get('/partners/referrals', [ExternalPortalController::class, 'referralDashboard'])->name('partners.referrals');
Route::get('/partners/campaigns', [ExternalPortalController::class, 'campaignResources'])->name('partners.campaigns');
Route::get('/partners/legal-verification', [ExternalPortalController::class, 'legalVerification'])->name('partners.legal-verification');

Route::get('/network', [ExternalPortalController::class, 'networkAcademy'])->name('network.academy');
Route::get('/network/talent-path', [ExternalPortalController::class, 'talentPath'])->name('network.talent-path');

Route::prefix('marketplace')->name('marketplace.')->group(function (): void {
    Route::get('/', [MarketplaceDeveloperController::class, 'marketplaceHome'])->name('home');
    Route::get('/themes', [MarketplaceDeveloperController::class, 'themeCatalog'])->name('themes.index');
    Route::get('/themes/{theme}', [MarketplaceDeveloperController::class, 'themeDetail'])->name('themes.show');
    Route::get('/theme-recipes', [MarketplaceDeveloperController::class, 'themeRecipes'])->name('theme-recipes');
    Route::get('/plugins', [MarketplaceDeveloperController::class, 'pluginCatalog'])->name('plugins.index');
    Route::get('/plugins/{package}', [MarketplaceDeveloperController::class, 'pluginDetail'])->name('plugins.show');
    Route::get('/licensing', [MarketplaceDeveloperController::class, 'licensing'])->name('licensing');
    Route::get('/governance', [MarketplaceDeveloperController::class, 'governance'])->name('governance');
    Route::get('/review-status', [MarketplaceDeveloperController::class, 'reviewStatus'])->name('review-status');
});

Route::prefix('developers')->name('developers.')->group(function (): void {
    Route::get('/', [MarketplaceDeveloperController::class, 'developerLanding'])->name('portal');
    Route::get('/onboarding', [MarketplaceDeveloperController::class, 'developerOnboarding'])->name('onboarding');
    Route::get('/docs/themes', [MarketplaceDeveloperController::class, 'themeBuilderDocs'])->name('docs.themes');
    Route::get('/docs/plugin-manifest', [MarketplaceDeveloperController::class, 'pluginManifestDocs'])->name('docs.plugin-manifest');
    Route::get('/docs/connectors', [MarketplaceDeveloperController::class, 'connectorSdkDocs'])->name('docs.connectors');
    Route::get('/submission-checklist', [MarketplaceDeveloperController::class, 'submissionChecklist'])->name('submission-checklist');
    Route::get('/listings', [MarketplaceDeveloperController::class, 'developerListings'])->name('listings');
    Route::get('/sales', [MarketplaceDeveloperController::class, 'developerSales'])->name('sales');
    Route::get('/profile', [MarketplaceDeveloperController::class, 'developerProfile'])->name('profile');
    Route::get('/qa', [MarketplaceDeveloperController::class, 'qaCenter'])->name('qa');
});

Route::prefix('api/mobile')->name('mobile.')->group(function (): void {
    Route::get('/config', [MobilePublicController::class, 'config'])->name('config');
    Route::get('/manifest', [MobilePublicController::class, 'manifest'])->name('manifest');
    Route::get('/theme', [MobilePublicController::class, 'theme'])->name('theme');
    Route::post('/auth/register', [MobileAuthController::class, 'register'])->name('auth.register');
    Route::post('/auth/login', [MobileAuthController::class, 'login'])->name('auth.login');
    Route::post('/devices', [MobileDeviceController::class, 'store'])->name('devices.store');
    Route::post('/push-tokens', [MobileDeviceController::class, 'pushToken'])->name('push-tokens.store');
});

Route::prefix('api/desktop')->name('desktop.')->group(function (): void {
    Route::post('/register', [DesktopSyncController::class, 'register'])->name('register');
    Route::post('/sync/pull', [DesktopSyncController::class, 'pull'])->name('sync.pull');
    Route::post('/sync/push-dry-run', [DesktopSyncController::class, 'pushDryRun'])->name('sync.push-dry-run');
    Route::post('/files', [DesktopSyncController::class, 'queueFile'])->name('files.queue');
});

Route::prefix('api/public-web')->name('public-web.')->group(function (): void {
    Route::get('/manifest', [PublicWebRuntimeController::class, 'manifest'])->name('manifest');
});

Route::get('/mall', MallHomeController::class)
    ->name('mall.index');
Route::get('/mall/search', [ExternalPortalController::class, 'mallSearch'])
    ->name('mall.search');
Route::get('/mall/trust', [ExternalPortalController::class, 'mallTrust'])
    ->name('mall.trust');
Route::get('/mall/claim-report', [ExternalPortalController::class, 'mallClaimReport'])
    ->name('mall.claim-report');
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
    Route::get('/app/{username}', [PublicContentEntryController::class, 'home'])
        ->where('username', '[A-Za-z0-9][A-Za-z0-9_-]*')
        ->name('public.site.show');
    Route::get('/app/{username}/{contentEntry:slug}', PublicContentEntryController::class)
        ->where('username', '[A-Za-z0-9][A-Za-z0-9_-]*')
        ->name('public.content-entry.show');
});

Route::get('/{locale}/{localizedPath?}', function (
    Request $request,
    string $locale,
    ?string $localizedPath = null,
) {
    abort_unless(KabeeriLocale::isSupported($locale), 404);

    $locale = KabeeriLocale::normalize($locale);
    $context = KabeeriLocale::context($request);
    $request->session()->put(KabeeriLocale::contextSessionKey($context), $locale);
    $request->session()->put(KabeeriLocale::sessionKey(), $locale);

    $target = '/'.trim((string) $localizedPath, '/');
    $target = $target === '/' ? '/' : $target;
    $subRequest = Request::create(
        $target,
        'GET',
        $request->query->all(),
        $request->cookies->all(),
        [],
        $request->server->all(),
    );
    $subRequest->setLaravelSession($request->session());
    $subRequest->headers->replace($request->headers->all());

    return app(Kernel::class)->handle($subRequest);
})
    ->where('locale', '[A-Za-z]{2}')
    ->where('localizedPath', '.*')
    ->name('localized.proxy');
