<?php

namespace App\Filament\Resources\FeatureFlags\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FeatureFlagsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('key')
            ->emptyStateHeading('No feature flags found')
            ->emptyStateDescription('Feature flags are seeded for V1 foundation.')
            ->columns([
                TextColumn::make('key')->label('Key')->searchable(),
                TextColumn::make('description')->label('Description')->placeholder('-'),
                IconColumn::make('default_value')->label('Default')->boolean(),
                TextColumn::make('status')->label('Status')->badge(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }
}
