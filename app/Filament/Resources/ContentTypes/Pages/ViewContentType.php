<?php

namespace App\Filament\Resources\ContentTypes\Pages;

use App\Filament\Resources\ContentTypes\ContentTypeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewContentType extends ViewRecord
{
    protected static string $resource = ContentTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
