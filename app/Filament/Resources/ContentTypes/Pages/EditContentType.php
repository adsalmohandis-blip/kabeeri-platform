<?php

namespace App\Filament\Resources\ContentTypes\Pages;

use App\Filament\Resources\ContentTypes\ContentTypeResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditContentType extends EditRecord
{
    protected static string $resource = ContentTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
        ];
    }
}
