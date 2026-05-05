<?php

namespace App\Filament\Resources\MediaAssets\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class MediaAssetInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('filename')->label('Filename'),
                TextEntry::make('mime_type')->label('MIME'),
                TextEntry::make('size_bytes')->label('Size (bytes)'),
                TextEntry::make('organization.name')->label('Organization'),
                TextEntry::make('site.name')->label('App')->placeholder('-'),
                TextEntry::make('company.trade_name')->label('Company')->placeholder('-'),
                TextEntry::make('visibility')->label('Visibility')->badge(),
                TextEntry::make('path')->label('Path'),
                TextEntry::make('alt_text')->label('Alt Text')->placeholder('-'),
                TextEntry::make('caption')->label('Caption')->placeholder('-'),
            ]);
    }
}
