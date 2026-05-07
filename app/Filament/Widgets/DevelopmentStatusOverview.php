<?php

namespace App\Filament\Widgets;

use App\Support\RootDashboardData;
use Filament\Widgets\Widget;

class DevelopmentStatusOverview extends Widget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = -10;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.development-status-overview';

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'dashboard' => RootDashboardData::make(),
        ];
    }
}
