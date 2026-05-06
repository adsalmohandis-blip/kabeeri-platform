<?php

namespace App\Filament\Resources\Reviews\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('organization.name')->label('Organization')->searchable(),
                TextColumn::make('rating')->sortable(),
                TextColumn::make('title')->placeholder('-')->searchable(),
                TextColumn::make('source')->badge(),
                TextColumn::make('status')->badge(),
                TextColumn::make('submitted_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'pending' => 'Pending',
                    'published' => 'Published',
                    'rejected' => 'Rejected',
                ]),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
