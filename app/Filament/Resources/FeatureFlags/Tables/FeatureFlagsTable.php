<?php

namespace App\Filament\Resources\FeatureFlags\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FeatureFlagsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')->label('Key')->searchable(),
                TextColumn::make('description')->label('Description')->placeholder('-'),
                IconColumn::make('default_value')->label('Default')->boolean(),
                TextColumn::make('status')->label('Status')->badge(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }
}
