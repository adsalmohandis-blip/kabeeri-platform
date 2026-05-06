<?php

namespace App\Filament\Resources\ReportDefinitions\Pages;

use App\Filament\Resources\ReportDefinitions\ReportDefinitionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewReportDefinition extends ViewRecord
{
    protected static string $resource = ReportDefinitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
