<?php

namespace App\Filament\Concerns;

use App\Support\Ui\AdminLocaleCopy;
use Filament\Navigation\NavigationItem;
use Illuminate\Contracts\Support\Htmlable;

use function Filament\Support\original_request;

trait LocalizesAdminPageLabels
{
    public static function getNavigationItems(): array
    {
        $activeRoutePattern = static::getNavigationItemActiveRoutePattern();
        $navigationLabel = parent::getNavigationLabel();

        return [
            NavigationItem::make(fn (): string => AdminLocaleCopy::label($navigationLabel))
                ->group(parent::getNavigationGroup())
                ->parentItem(parent::getNavigationParentItem())
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
        return AdminLocaleCopy::label(static::$navigationLabel ?? static::$title ?? class_basename(static::class));
    }

    public function getTitle(): string|Htmlable
    {
        return AdminLocaleCopy::label(static::$title ?? static::getNavigationLabel());
    }
}
