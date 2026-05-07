<?php

namespace App\Filament\Resources\AgencyPartnerProfiles;

use App\Filament\Concerns\LocalizesAdminResourceLabels;
use App\Filament\Resources\AgencyPartnerProfiles\Pages\ListAgencyPartnerProfiles;
use App\Filament\Resources\AgencyPartnerProfiles\Tables\AgencyPartnerProfilesTable;
use App\Models\AgencyPartnerProfile;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AgencyPartnerProfileResource extends Resource
{
    use LocalizesAdminResourceLabels;

    protected static ?string $model = AgencyPartnerProfile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $navigationLabel = 'Agency Partners';

    protected static string|\UnitEnum|null $navigationGroup = 'V4 Partners';

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return AgencyPartnerProfilesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAgencyPartnerProfiles::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('organization', function (Builder $organizationQuery) use ($user): void {
            $organizationQuery
                ->where('owner_user_id', $user->id)
                ->orWhereHas('memberships', function (Builder $membershipQuery) use ($user): void {
                    $membershipQuery->where('user_id', $user->id)->where('status', 'active');
                });
        });
    }
}
