<?php

namespace App\Filament\Resources\BusinessProjects\Pages;

use App\Filament\Resources\BusinessProjects\BusinessProjectResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBusinessProject extends ViewRecord
{
    protected static string $resource = BusinessProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
