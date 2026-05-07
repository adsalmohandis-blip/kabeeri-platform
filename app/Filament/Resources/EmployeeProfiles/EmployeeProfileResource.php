<?php

namespace App\Filament\Resources\EmployeeProfiles;

use App\Filament\Concerns\LocalizesAdminResourceLabels;
use App\Filament\Resources\Concerns\ScopesToAccessibleOrganizations;
use App\Filament\Resources\EmployeeProfiles\Pages\CreateEmployeeProfile;
use App\Filament\Resources\EmployeeProfiles\Pages\EditEmployeeProfile;
use App\Filament\Resources\EmployeeProfiles\Pages\ListEmployeeProfiles;
use App\Filament\Resources\EmployeeProfiles\Pages\ViewEmployeeProfile;
use App\Filament\Resources\EmployeeProfiles\Schemas\EmployeeProfileForm;
use App\Filament\Resources\EmployeeProfiles\Schemas\EmployeeProfileInfolist;
use App\Filament\Resources\EmployeeProfiles\Tables\EmployeeProfilesTable;
use App\Models\EmployeeProfile;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EmployeeProfileResource extends Resource
{
    use LocalizesAdminResourceLabels;
    use ScopesToAccessibleOrganizations;

    protected static ?string $model = EmployeeProfile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    protected static string|\UnitEnum|null $navigationGroup = 'People';

    public static function form(Schema $schema): Schema
    {
        return EmployeeProfileForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EmployeeProfileInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmployeeProfilesTable::configure($table);
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
            'index' => ListEmployeeProfiles::route('/'),
            'create' => CreateEmployeeProfile::route('/create'),
            'view' => ViewEmployeeProfile::route('/{record}'),
            'edit' => EditEmployeeProfile::route('/{record}/edit'),
        ];
    }
}
