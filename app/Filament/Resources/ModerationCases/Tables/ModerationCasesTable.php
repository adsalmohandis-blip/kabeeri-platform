<?php

namespace App\Filament\Resources\ModerationCases\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ModerationCasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('case_number')->searchable(),
                TextColumn::make('organization.name')->label('Organization')->searchable(),
                TextColumn::make('case_type')->badge(),
                TextColumn::make('priority')->badge(),
                TextColumn::make('status')->badge(),
                TextColumn::make('assigned_to_user_id')->label('Assignee'),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'open' => 'Open',
                    'in_review' => 'In Review',
                    'resolved' => 'Resolved',
                    'closed' => 'Closed',
                ]),
                SelectFilter::make('priority')->options([
                    'urgent' => 'Urgent',
                    'high' => 'High',
                    'normal' => 'Normal',
                    'low' => 'Low',
                ]),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
