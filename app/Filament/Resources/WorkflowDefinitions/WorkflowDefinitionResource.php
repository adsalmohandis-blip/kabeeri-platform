<?php

namespace App\Filament\Resources\WorkflowDefinitions;

use App\Filament\Concerns\LocalizesAdminResourceLabels;
use App\Filament\Resources\Concerns\ScopesToAccessibleOrganizations;
use App\Filament\Resources\WorkflowDefinitions\Pages\CreateWorkflowDefinition;
use App\Filament\Resources\WorkflowDefinitions\Pages\EditWorkflowDefinition;
use App\Filament\Resources\WorkflowDefinitions\Pages\ListWorkflowDefinitions;
use App\Filament\Resources\WorkflowDefinitions\Pages\ViewWorkflowDefinition;
use App\Filament\Resources\WorkflowDefinitions\Schemas\WorkflowDefinitionForm;
use App\Filament\Resources\WorkflowDefinitions\Schemas\WorkflowDefinitionInfolist;
use App\Filament\Resources\WorkflowDefinitions\Tables\WorkflowDefinitionsTable;
use App\Models\WorkflowDefinition;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WorkflowDefinitionResource extends Resource
{
    use LocalizesAdminResourceLabels;
    use ScopesToAccessibleOrganizations;

    protected static ?string $model = WorkflowDefinition::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBolt;

    protected static string|\UnitEnum|null $navigationGroup = 'Workflows';

    public static function form(Schema $schema): Schema
    {
        return WorkflowDefinitionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WorkflowDefinitionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkflowDefinitionsTable::configure($table);
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
            'index' => ListWorkflowDefinitions::route('/'),
            'create' => CreateWorkflowDefinition::route('/create'),
            'view' => ViewWorkflowDefinition::route('/{record}'),
            'edit' => EditWorkflowDefinition::route('/{record}/edit'),
        ];
    }
}
