<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Pages\UserSettings;
use App\Filament\Widgets\DevelopmentStatusOverview;
use App\Http\Middleware\ResetCustomerSessionForAdminLogin;
use App\Http\Middleware\SetKabeeriLocale;
use App\Support\Ui\AdminLocaleCopy;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile(UserSettings::class, isSimple: false)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->darkMode(false)
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => view('filament.partials.admin-style')->render(),
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->navigationGroups([
                'V10 Admin Command' => NavigationGroup::make(fn (): string => AdminLocaleCopy::label('V10 Admin Command'))->collapsed(false),
                'Core' => NavigationGroup::make(fn (): string => AdminLocaleCopy::label('Core')),
                'Organizations' => NavigationGroup::make(fn (): string => AdminLocaleCopy::label('Organizations')),
                'Apps' => NavigationGroup::make(fn (): string => AdminLocaleCopy::label('Apps')),
                'Content' => NavigationGroup::make(fn (): string => AdminLocaleCopy::label('Content')),
                'Media' => NavigationGroup::make(fn (): string => AdminLocaleCopy::label('Media')),
                'Rabet Foundation' => NavigationGroup::make(fn (): string => AdminLocaleCopy::label('Rabet Foundation')),
                'CRM' => NavigationGroup::make(fn (): string => AdminLocaleCopy::label('CRM')),
                'Sales & Invoicing' => NavigationGroup::make(fn (): string => AdminLocaleCopy::label('Sales & Invoicing')),
                'Inventory & Purchasing' => NavigationGroup::make(fn (): string => AdminLocaleCopy::label('Inventory & Purchasing')),
                'Workflows' => NavigationGroup::make(fn (): string => AdminLocaleCopy::label('Workflows')),
                'Reports' => NavigationGroup::make(fn (): string => AdminLocaleCopy::label('Reports')),
                'Freemium' => NavigationGroup::make(fn (): string => AdminLocaleCopy::label('Freemium')),
                'V2 CMS' => NavigationGroup::make(fn (): string => AdminLocaleCopy::label('V2 CMS')),
                'V2 Commerce' => NavigationGroup::make(fn (): string => AdminLocaleCopy::label('V2 Commerce')),
                'V2 Migration' => NavigationGroup::make(fn (): string => AdminLocaleCopy::label('V2 Migration')),
                'V4 Marketplace' => NavigationGroup::make(fn (): string => AdminLocaleCopy::label('V4 Marketplace')),
                'V4 Moderation' => NavigationGroup::make(fn (): string => AdminLocaleCopy::label('V4 Moderation')),
                'V4 Partners' => NavigationGroup::make(fn (): string => AdminLocaleCopy::label('V4 Partners')),
                'V4 Trust' => NavigationGroup::make(fn (): string => AdminLocaleCopy::label('V4 Trust')),
                'V5 ERP Pro' => NavigationGroup::make(fn (): string => AdminLocaleCopy::label('V5 ERP Pro')),
                'V5 Integration Hub' => NavigationGroup::make(fn (): string => AdminLocaleCopy::label('V5 Integration Hub')),
                'V6 Marketplace' => NavigationGroup::make(fn (): string => AdminLocaleCopy::label('V6 Marketplace')),
                'V6 Data Platform' => NavigationGroup::make(fn (): string => AdminLocaleCopy::label('V6 Data Platform')),
                'V6 GRC' => NavigationGroup::make(fn (): string => AdminLocaleCopy::label('V6 GRC')),
                'System' => NavigationGroup::make(fn (): string => AdminLocaleCopy::label('System')),
                'People' => NavigationGroup::make(fn (): string => AdminLocaleCopy::label('People')),
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                DevelopmentStatusOverview::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ResetCustomerSessionForAdminLogin::class,
                SetKabeeriLocale::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
