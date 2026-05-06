<?php

namespace App\Filament\Resources\WorkflowDefinitions\Pages;

use App\Filament\Resources\WorkflowDefinitions\WorkflowDefinitionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewWorkflowDefinition extends ViewRecord
{
    protected static string $resource = WorkflowDefinitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
