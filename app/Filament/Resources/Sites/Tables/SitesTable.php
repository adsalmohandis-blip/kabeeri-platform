<?php

namespace App\Filament\Resources\Sites\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SitesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->emptyStateHeading('No apps yet')
            ->emptyStateDescription('Create an app under an organization to start publishing content.')
            ->columns([
                TextColumn::make('name')->label('App')->searchable(),
                TextColumn::make('organization.name')->label('Organization')->searchable(),
                TextColumn::make('company.trade_name')->label('Company')->placeholder('-'),
                TextColumn::make('slug')->label('Username')->searchable(),
                TextColumn::make('site_type')->label('Type')->badge(),
                TextColumn::make('status')->label('Status')->badge(),
                TextColumn::make('language')->label('Language'),
                TextColumn::make('timezone')->label('Timezone'),
            ])
            ->filters([
                SelectFilter::make('organization_id')
                    ->label('Organization')
                    ->relationship('organization', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('company_id')
                    ->label('Company')
                    ->relationship('company', 'trade_name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('site_type')
                    ->label('App Type')
                    ->options([
                        'website' => 'Website',
                        'blog' => 'Blog',
                        'store' => 'Store',
                        'portal' => 'Portal',
                        'landing' => 'Landing',
                        'internal_portal' => 'Internal Portal',
                        'mall_seller_site' => 'Mall Seller Site',
                    ]),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'draft' => 'Draft',
                    ]),
                SelectFilter::make('language')
                    ->label('Language')
                    ->options([
                        'ar' => 'Arabic',
                        'en' => 'English',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
