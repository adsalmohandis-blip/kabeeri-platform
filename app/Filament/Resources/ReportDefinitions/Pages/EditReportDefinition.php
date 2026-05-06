<?php

namespace App\Filament\Resources\ReportDefinitions\Pages;

use App\Filament\Resources\ReportDefinitions\ReportDefinitionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditReportDefinition extends EditRecord
{
    protected static string $resource = ReportDefinitionResource::class;

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
