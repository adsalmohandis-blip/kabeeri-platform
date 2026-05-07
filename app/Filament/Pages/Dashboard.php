<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DevelopmentStatusOverview;
use App\Support\Ui\AdminLocaleCopy;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\Widget;
use Illuminate\Contracts\Support\Htmlable;

use function Filament\Support\original_request;

class Dashboard extends BaseDashboard
{
    public static function getNavigationItems(): array
    {
        $activeRoutePattern = static::getNavigationItemActiveRoutePattern();

        return [
            NavigationItem::make(fn (): string => AdminLocaleCopy::label('Dashboard'))
                ->group(static::getNavigationGroup())
                ->parentItem(static::getNavigationParentItem())
                ->icon(static::getNavigationIcon())
                ->activeIcon(static::getActiveNavigationIcon())
                ->isActiveWhen(fn (): bool => original_request()->routeIs($activeRoutePattern))
                ->sort(static::getNavigationSort())
                ->badge(static::getNavigationBadge(), color: static::getNavigationBadgeColor())
                ->badgeTooltip(static::getNavigationBadgeTooltip())
                ->url(static::getNavigationUrl()),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return AdminLocaleCopy::label('Dashboard');
    }

    public function getTitle(): string|Htmlable
    {
        return AdminLocaleCopy::label('Platform Admin Dashboard');
    }

    /**
     * @return array<class-string<Widget>>
     */
    public function getWidgets(): array
    {
        return [
            DevelopmentStatusOverview::class,
        ];
    }

    /**
     * @return int|array<string, ?int>
     */
    public function getColumns(): int|array
    {
        return 1;
    }
}
