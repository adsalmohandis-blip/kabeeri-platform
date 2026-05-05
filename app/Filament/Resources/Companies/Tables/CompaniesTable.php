<?php

namespace App\Filament\Resources\Companies\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CompaniesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('trade_name')->label('Company')->searchable(),
                TextColumn::make('organization.name')->label('Organization')->searchable(),
                TextColumn::make('slug')->label('Slug')->searchable(),
                TextColumn::make('status')->label('Status')->badge(),
                TextColumn::make('verification_status')->label('Verification')->badge(),
                TextColumn::make('updated_at')->label('Updated')->dateTime()->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
