<?php

namespace App\Filament\Resources\PartnerStorefronts\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PartnerStorefrontsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('organization.name')->label('Organization')->searchable(),
                TextColumn::make('agencyPartnerProfile.display_name')->label('Agency')->placeholder('-')->searchable(),
                TextColumn::make('storefront_type')->badge(),
                TextColumn::make('status')->badge(),
                TextColumn::make('visibility')->badge(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'draft' => 'Draft',
                    'active' => 'Active',
                    'archived' => 'Archived',
                ]),
                SelectFilter::make('visibility')->options([
                    'private' => 'Private',
                    'public' => 'Public',
                ]),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
