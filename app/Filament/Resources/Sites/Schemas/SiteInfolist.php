<?php

namespace App\Filament\Resources\Sites\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SiteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')->label('App Name'),
                TextEntry::make('slug')->label('Username'),
                TextEntry::make('organization.name')->label('Organization'),
                TextEntry::make('company.trade_name')->label('Company')->placeholder('-'),
                TextEntry::make('site_type')->label('App Type')->badge(),
                TextEntry::make('status')->label('Status')->badge(),
                TextEntry::make('language')->label('Language'),
                TextEntry::make('timezone')->label('Timezone'),
            ]);
    }
}
