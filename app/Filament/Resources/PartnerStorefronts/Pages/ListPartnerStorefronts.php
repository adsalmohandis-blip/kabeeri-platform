<?php

namespace App\Filament\Resources\PartnerStorefronts\Pages;

use App\Filament\Resources\PartnerStorefronts\PartnerStorefrontResource;
use Filament\Resources\Pages\ListRecords;

class ListPartnerStorefronts extends ListRecords
{
    protected static string $resource = PartnerStorefrontResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
