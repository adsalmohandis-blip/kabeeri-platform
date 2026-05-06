<?php

namespace App\Filament\Resources\DashboardWidgets\Pages;

use App\Filament\Resources\DashboardWidgets\DashboardWidgetResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDashboardWidget extends ViewRecord
{
    protected static string $resource = DashboardWidgetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
