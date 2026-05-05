<?php

namespace App\Filament\Resources\ContentTypes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ContentTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')->label('Name'),
                TextEntry::make('slug')->label('Slug'),
                TextEntry::make('organization.name')->label('Organization'),
                TextEntry::make('site.name')->label('App')->placeholder('-'),
                TextEntry::make('status')->label('Status')->badge(),
                TextEntry::make('description')->label('Description')->placeholder('-'),
            ]);
    }
}
