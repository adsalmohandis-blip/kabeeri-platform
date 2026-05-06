<?php

namespace App\Filament\Resources\DashboardWidgets\Pages;

use App\Filament\Resources\DashboardWidgets\DashboardWidgetResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDashboardWidget extends EditRecord
{
    protected static string $resource = DashboardWidgetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
