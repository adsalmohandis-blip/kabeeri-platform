<?php

namespace App\Filament\Resources\AgencyPartnerProfiles\Pages;

use App\Filament\Resources\AgencyPartnerProfiles\AgencyPartnerProfileResource;
use Filament\Resources\Pages\ListRecords;

class ListAgencyPartnerProfiles extends ListRecords
{
    protected static string $resource = AgencyPartnerProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
