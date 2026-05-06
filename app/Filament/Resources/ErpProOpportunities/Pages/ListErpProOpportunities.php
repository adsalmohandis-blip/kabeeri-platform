<?php

namespace App\Filament\Resources\ErpProOpportunities\Pages;

use App\Filament\Resources\ErpProOpportunities\ErpProOpportunityResource;
use Filament\Resources\Pages\ListRecords;

class ListErpProOpportunities extends ListRecords
{
    protected static string $resource = ErpProOpportunityResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
