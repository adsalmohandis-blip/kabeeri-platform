<?php

namespace App\Filament\Resources\ContentEntries\Pages;

use App\Filament\Resources\ContentEntries\ContentEntryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewContentEntry extends ViewRecord
{
    protected static string $resource = ContentEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
