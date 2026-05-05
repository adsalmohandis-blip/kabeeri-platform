<?php

namespace App\Filament\Resources\Sites\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SitesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('App')->searchable(),
                TextColumn::make('organization.name')->label('Organization')->searchable(),
                TextColumn::make('company.trade_name')->label('Company')->placeholder('-'),
                TextColumn::make('slug')->label('Slug')->searchable(),
                TextColumn::make('site_type')->label('Type')->badge(),
                TextColumn::make('status')->label('Status')->badge(),
                TextColumn::make('language')->label('Language'),
                TextColumn::make('timezone')->label('Timezone'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
