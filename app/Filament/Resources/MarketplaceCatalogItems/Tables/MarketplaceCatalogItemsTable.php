<?php

namespace App\Filament\Resources\MarketplaceCatalogItems\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MarketplaceCatalogItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('item_kind')->badge(),
                TextColumn::make('catalogable_type')->label('Source')->formatStateUsing(fn (?string $state): string => $state === null ? '-' : class_basename($state)),
                TextColumn::make('listing_status')->badge(),
                TextColumn::make('governance_status')->badge(),
                TextColumn::make('visibility')->badge(),
                IconColumn::make('is_featured')->boolean(),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('item_kind')->options([
                    'package' => 'Package',
                    'theme' => 'Theme',
                ]),
                SelectFilter::make('governance_status')->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                ]),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
