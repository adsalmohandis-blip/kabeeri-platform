<?php

namespace App\Filament\Resources\BusinessProjects;

use App\Filament\Resources\BusinessProjects\Pages\CreateBusinessProject;
use App\Filament\Resources\BusinessProjects\Pages\EditBusinessProject;
use App\Filament\Resources\BusinessProjects\Pages\ListBusinessProjects;
use App\Filament\Resources\BusinessProjects\Pages\ViewBusinessProject;
use App\Filament\Resources\BusinessProjects\Schemas\BusinessProjectForm;
use App\Filament\Resources\BusinessProjects\Schemas\BusinessProjectInfolist;
use App\Filament\Resources\BusinessProjects\Tables\BusinessProjectsTable;
use App\Filament\Resources\Concerns\ScopesToAccessibleOrganizations;
use App\Models\BusinessProject;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BusinessProjectResource extends Resource
{
    use ScopesToAccessibleOrganizations;

    protected static ?string $model = BusinessProject::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static string|\UnitEnum|null $navigationGroup = 'People';

    public static function form(Schema $schema): Schema
    {
        return BusinessProjectForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BusinessProjectInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BusinessProjectsTable::configure($table);
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
            'index' => ListBusinessProjects::route('/'),
            'create' => CreateBusinessProject::route('/create'),
            'view' => ViewBusinessProject::route('/{record}'),
            'edit' => EditBusinessProject::route('/{record}/edit'),
        ];
    }
}
