<?php

namespace App\Filament\Resources\Settings\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->emptyStateHeading('No settings yet')
            ->emptyStateDescription('Create organization or app settings as needed.')
            ->columns([
                TextColumn::make('organization.name')->label('Organization')->searchable(),
                TextColumn::make('scope_type')->label('Scope')->badge(),
                TextColumn::make('scope_id')->label('Scope ID')->placeholder('-'),
                TextColumn::make('key')->label('Key')->searchable(),
                TextColumn::make('value_type')->label('Value Type'),
                TextColumn::make('updated_at')->label('Updated')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('organization_id')
                    ->label('Organization')
                    ->relationship('organization', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('scope_type')
                    ->label('Scope')
                    ->options([
                        'organization' => 'Organization',
                        'company' => 'Company',
                        'site' => 'Site',
                        'module' => 'Module',
                        'user' => 'User',
                    ]),
                SelectFilter::make('value_type')
                    ->label('Value Type')
                    ->options([
                        'string' => 'String',
                        'json' => 'JSON',
                        'boolean' => 'Boolean',
                        'integer' => 'Integer',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
