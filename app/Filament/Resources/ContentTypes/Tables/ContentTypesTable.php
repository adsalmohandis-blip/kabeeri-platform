<?php

namespace App\Filament\Resources\ContentTypes\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContentTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Name')->searchable(),
                TextColumn::make('slug')->label('Slug')->searchable(),
                TextColumn::make('organization.name')->label('Organization')->searchable(),
                TextColumn::make('site.name')->label('App')->placeholder('-'),
                TextColumn::make('status')->label('Status')->badge(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
