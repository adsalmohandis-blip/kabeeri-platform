<?php

namespace App\Filament\Resources\ContentEntries\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ContentEntryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title')->label('Title'),
                TextEntry::make('slug')->label('Slug'),
                TextEntry::make('site.name')->label('App'),
                TextEntry::make('contentType.name')->label('Content Type'),
                TextEntry::make('status')->label('Status')->badge(),
                TextEntry::make('visibility')->label('Visibility')->badge(),
                TextEntry::make('published_at')->label('Published At')->dateTime()->placeholder('-'),
                TextEntry::make('excerpt')->label('Excerpt')->placeholder('-'),
                TextEntry::make('body')->label('Body')->placeholder('-'),
            ]);
    }
}
