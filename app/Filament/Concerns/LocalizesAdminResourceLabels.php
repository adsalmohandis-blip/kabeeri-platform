<?php

namespace App\Filament\Concerns;

use App\Support\Ui\AdminLocaleCopy;
use Filament\Navigation\NavigationItem;

use function Filament\Support\original_request;

trait LocalizesAdminResourceLabels
{
    public static function getNavigationItems(): array
    {
        if (! static::hasPage('index')) {
            return [];
        }

        $activeRoutePattern = static::getNavigationItemActiveRoutePattern();
        $navigationLabel = parent::getNavigationLabel();

        return [
            NavigationItem::make(fn (): string => AdminLocaleCopy::label($navigationLabel))
                ->group(parent::getNavigationGroup())
                ->parentItem(parent::getNavigationParentItem())
                ->icon(static::getNavigationIcon())
                ->activeIcon(static::getActiveNavigationIcon())
                ->isActiveWhen(fn (): bool => original_request()->routeIs($activeRoutePattern))
                ->badge(static::getNavigationBadge(), color: static::getNavigationBadgeColor())
                ->badgeTooltip(static::getNavigationBadgeTooltip())
                ->sort(static::getNavigationSort())
                ->url(static::getNavigationUrl()),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return AdminLocaleCopy::label(parent::getNavigationLabel());
    }

    public static function getModelLabel(): string
    {
        return AdminLocaleCopy::label(parent::getModelLabel());
    }

    public static function getPluralModelLabel(): string
    {
        return AdminLocaleCopy::label(parent::getPluralModelLabel());
    }

    public static function getTitleCaseModelLabel(): string
    {
        return static::getModelLabel();
    }

    public static function getTitleCasePluralModelLabel(): string
    {
        return static::getPluralModelLabel();
    }
}
