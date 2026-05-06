<?php

namespace App\Filament\Resources\AgencyPartnerProfiles\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AgencyPartnerProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('display_name')->searchable(),
                TextColumn::make('organization.name')->label('Organization')->searchable(),
                TextColumn::make('agency_type')->badge(),
                TextColumn::make('status')->badge(),
                TextColumn::make('accreditation_status')->badge(),
                TextColumn::make('accreditation_level')->placeholder('-')->badge(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'draft' => 'Draft',
                    'submitted' => 'Submitted',
                    'active' => 'Active',
                ]),
                SelectFilter::make('accreditation_status')->options([
                    'not_submitted' => 'Not Submitted',
                    'submitted' => 'Submitted',
                    'accredited' => 'Accredited',
                ]),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
