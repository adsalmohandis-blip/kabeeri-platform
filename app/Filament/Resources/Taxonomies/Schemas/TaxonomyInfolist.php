<?php

namespace App\Filament\Resources\Taxonomies\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TaxonomyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')->label('Name'),
                TextEntry::make('slug')->label('Slug'),
                TextEntry::make('type')->label('Type')->badge(),
                TextEntry::make('organization.name')->label('Organization'),
                TextEntry::make('site.name')->label('App')->placeholder('-'),
            ]);
    }
}
