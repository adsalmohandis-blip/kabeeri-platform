<?php

namespace App\Filament\Resources\MarketplaceCatalogItems\Pages;

use App\Filament\Resources\MarketplaceCatalogItems\MarketplaceCatalogItemResource;
use Filament\Resources\Pages\ListRecords;

class ListMarketplaceCatalogItems extends ListRecords
{
    protected static string $resource = MarketplaceCatalogItemResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
