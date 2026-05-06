<?php

namespace App\Filament\Resources\BusinessProjects\Pages;

use App\Filament\Resources\BusinessProjects\BusinessProjectResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBusinessProjects extends ListRecords
{
    protected static string $resource = BusinessProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
