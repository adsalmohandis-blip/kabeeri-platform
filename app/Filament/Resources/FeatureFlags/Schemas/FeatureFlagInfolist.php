<?php

namespace App\Filament\Resources\FeatureFlags\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class FeatureFlagInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('key')->label('Key'),
                TextEntry::make('description')->label('Description')->placeholder('-'),
                IconEntry::make('default_value')->label('Default')->boolean(),
                TextEntry::make('status')->label('Status')->badge(),
            ]);
    }
}
