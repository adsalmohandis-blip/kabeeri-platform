<?php

namespace App\Filament\Resources\Organizations\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrganizationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No organizations yet')
            ->emptyStateDescription('Create your first organization to start building apps and content.')
            ->columns([
                TextColumn::make('name')
                    ->label('Organization')
                    ->searchable(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable(),
                TextColumn::make('owner.name')
                    ->label('Owner')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('account_type')
                    ->label('Account Type')
                    ->badge()
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->searchable(),
                TextColumn::make('plan_code')
                    ->label('Plan')
                    ->badge()
                    ->placeholder('free')
                    ->searchable(),
                TextColumn::make('country.name')
                    ->label('Country')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('locale')
                    ->label('Locale')
                    ->searchable(),
                TextColumn::make('timezone')
                    ->label('Timezone')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('account_type')
                    ->label('Account Type')
                    ->options([
                        'business' => 'Business',
                        'personal' => 'Personal',
                        'agency' => 'Agency',
                    ]),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'draft' => 'Draft',
                    ]),
                SelectFilter::make('plan_code')
                    ->label('Plan')
                    ->options([
                        'free' => 'Free / Community',
                        'starter' => 'Starter / Pro',
                        'business' => 'Business',
                        'agency' => 'Agency',
                        'enterprise' => 'Enterprise',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
