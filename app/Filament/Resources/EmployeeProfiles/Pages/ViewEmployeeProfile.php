<?php

namespace App\Filament\Resources\EmployeeProfiles\Pages;

use App\Filament\Resources\EmployeeProfiles\EmployeeProfileResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEmployeeProfile extends ViewRecord
{
    protected static string $resource = EmployeeProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
