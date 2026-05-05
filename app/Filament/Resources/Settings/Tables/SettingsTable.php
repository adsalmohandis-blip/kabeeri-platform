<?php

namespace App\Filament\Resources\Settings\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('organization.name')->label('Organization')->searchable(),
                TextColumn::make('scope_type')->label('Scope')->badge(),
                TextColumn::make('scope_id')->label('Scope ID')->placeholder('-'),
                TextColumn::make('key')->label('Key')->searchable(),
                TextColumn::make('value_type')->label('Value Type'),
                TextColumn::make('updated_at')->label('Updated')->dateTime()->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
