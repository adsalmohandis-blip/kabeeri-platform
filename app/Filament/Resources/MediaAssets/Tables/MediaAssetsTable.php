<?php

namespace App\Filament\Resources\MediaAssets\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MediaAssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('filename')->label('Filename')->searchable(),
                TextColumn::make('organization.name')->label('Organization')->searchable(),
                TextColumn::make('site.name')->label('App')->placeholder('-'),
                TextColumn::make('mime_type')->label('MIME')->searchable(),
                TextColumn::make('size_bytes')->label('Size')->numeric()->sortable(),
                TextColumn::make('visibility')->label('Visibility')->badge(),
                TextColumn::make('updated_at')->label('Updated')->dateTime()->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
