<?php

namespace App\Filament\Resources\DeveloperMarketplaceListings\Pages;

use App\Filament\Resources\DeveloperMarketplaceListings\DeveloperMarketplaceListingResource;
use Filament\Resources\Pages\ListRecords;

class ListDeveloperMarketplaceListings extends ListRecords
{
    protected static string $resource = DeveloperMarketplaceListingResource::class;
}
