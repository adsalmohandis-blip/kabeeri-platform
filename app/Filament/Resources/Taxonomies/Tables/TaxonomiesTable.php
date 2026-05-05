<?php

namespace App\Filament\Resources\Taxonomies\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TaxonomiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Name')->searchable(),
                TextColumn::make('slug')->label('Slug')->searchable(),
                TextColumn::make('type')->label('Type')->badge(),
                TextColumn::make('organization.name')->label('Organization')->searchable(),
                TextColumn::make('site.name')->label('App')->placeholder('-'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
