<?php

namespace App\Filament\Resources\ImportJobs;

use App\Filament\Concerns\LocalizesAdminResourceLabels;
use App\Filament\Resources\Concerns\ScopesToAccessibleOrganizations;
use App\Filament\Resources\ImportJobs\Pages\ListImportJobs;
use App\Filament\Resources\ImportJobs\Pages\ViewImportJob;
use App\Models\ImportJob;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ImportJobResource extends Resource
{
    use LocalizesAdminResourceLabels;
    use ScopesToAccessibleOrganizations;

    protected static ?string $model = ImportJob::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Import Jobs';

    protected static string|\UnitEnum|null $navigationGroup = 'V2 Migration';

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('source_type')->badge(),
            TextEntry::make('source_name')->placeholder('-'),
            TextEntry::make('status')->badge(),
            TextEntry::make('organization.name')->label('Organization')->placeholder('-'),
            TextEntry::make('site.name')->label('Site')->placeholder('-'),
            TextEntry::make('started_at')->dateTime()->placeholder('-'),
            TextEntry::make('completed_at')->dateTime()->placeholder('-'),
            TextEntry::make('failed_at')->dateTime()->placeholder('-'),
            KeyValueEntry::make('summary')->placeholder('No summary yet'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('source_type')->badge()->searchable(),
                TextColumn::make('source_name')->searchable()->placeholder('-'),
                TextColumn::make('organization.name')->label('Organization')->searchable(),
                TextColumn::make('site.name')->label('Site')->searchable()->placeholder('-'),
                TextColumn::make('status')->badge()->searchable(),
                TextColumn::make('records_count')->label('Records')->counts('records')->sortable(),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('source_type')->options([
                    'wordpress' => 'WordPress',
                    'woocommerce' => 'WooCommerce',
                    'csv' => 'CSV',
                ]),
                SelectFilter::make('status')->options([
                    'draft' => 'Draft',
                    'previewed' => 'Previewed',
                    'running' => 'Running',
                    'completed' => 'Completed',
                    'failed' => 'Failed',
                    'rolled_back' => 'Rolled Back',
                ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListImportJobs::route('/'),
            'view' => ViewImportJob::route('/{record}'),
        ];
    }
}
