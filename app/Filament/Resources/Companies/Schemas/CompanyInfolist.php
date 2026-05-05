<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CompanyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('trade_name')->label('Trade Name'),
                TextEntry::make('slug')->label('Slug'),
                TextEntry::make('organization.name')->label('Organization'),
                TextEntry::make('status')->label('Status')->badge(),
                TextEntry::make('verification_status')->label('Verification Status')->badge(),
                TextEntry::make('country.name')->label('Country')->placeholder('-'),
                TextEntry::make('city')->label('City')->placeholder('-'),
                TextEntry::make('updated_at')->label('Updated At')->dateTime()->placeholder('-'),
            ]);
    }
}
