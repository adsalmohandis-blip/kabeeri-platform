<?php

namespace App\Filament\Resources\ReportDefinitions;

use App\Filament\Resources\Concerns\ScopesToAccessibleOrganizations;
use App\Filament\Resources\ReportDefinitions\Pages\CreateReportDefinition;
use App\Filament\Resources\ReportDefinitions\Pages\EditReportDefinition;
use App\Filament\Resources\ReportDefinitions\Pages\ListReportDefinitions;
use App\Filament\Resources\ReportDefinitions\Pages\ViewReportDefinition;
use App\Filament\Resources\ReportDefinitions\Schemas\ReportDefinitionForm;
use App\Filament\Resources\ReportDefinitions\Schemas\ReportDefinitionInfolist;
use App\Filament\Resources\ReportDefinitions\Tables\ReportDefinitionsTable;
use App\Models\ReportDefinition;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ReportDefinitionResource extends Resource
{
    use ScopesToAccessibleOrganizations;

    protected static ?string $model = ReportDefinition::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|\UnitEnum|null $navigationGroup = 'Reports';

    public static function form(Schema $schema): Schema
    {
        return ReportDefinitionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ReportDefinitionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReportDefinitionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReportDefinitions::route('/'),
            'create' => CreateReportDefinition::route('/create'),
            'view' => ViewReportDefinition::route('/{record}'),
            'edit' => EditReportDefinition::route('/{record}/edit'),
        ];
    }
}
