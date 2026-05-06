<?php

namespace App\Filament\Resources\ModerationCases\Pages;

use App\Filament\Resources\ModerationCases\ModerationCaseResource;
use Filament\Resources\Pages\ListRecords;

class ListModerationCases extends ListRecords
{
    protected static string $resource = ModerationCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
