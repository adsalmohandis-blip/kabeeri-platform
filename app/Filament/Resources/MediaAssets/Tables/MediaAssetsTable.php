<?php

namespace App\Filament\Resources\MediaAssets\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MediaAssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->emptyStateHeading('No media assets yet')
            ->emptyStateDescription('Upload media files to attach them to apps and content.')
            ->columns([
                TextColumn::make('filename')->label('Filename')->searchable(),
                TextColumn::make('organization.name')->label('Organization')->searchable(),
                TextColumn::make('site.name')->label('App')->placeholder('-'),
                TextColumn::make('mime_type')->label('MIME')->searchable(),
                TextColumn::make('size_bytes')->label('Size')->numeric()->sortable(),
                TextColumn::make('visibility')->label('Visibility')->badge(),
                TextColumn::make('updated_at')->label('Updated')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('organization_id')
                    ->label('Organization')
                    ->relationship('organization', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('site_id')
                    ->label('App')
                    ->relationship('site', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('visibility')
                    ->label('Visibility')
                    ->options([
                        'public' => 'Public',
                        'private' => 'Private',
                        'organization_only' => 'Organization Only',
                        'company_only' => 'Company Only',
                        'sensitive' => 'Sensitive',
                    ]),
                SelectFilter::make('mime_type')
                    ->label('MIME')
                    ->options([
                        'image/jpeg' => 'image/jpeg',
                        'image/png' => 'image/png',
                        'application/pdf' => 'application/pdf',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
