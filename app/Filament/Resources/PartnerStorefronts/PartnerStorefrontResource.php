<?php

namespace App\Filament\Resources\PartnerStorefronts;

use App\Filament\Concerns\LocalizesAdminResourceLabels;
use App\Filament\Resources\PartnerStorefronts\Pages\ListPartnerStorefronts;
use App\Filament\Resources\PartnerStorefronts\Tables\PartnerStorefrontsTable;
use App\Models\PartnerStorefront;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PartnerStorefrontResource extends Resource
{
    use LocalizesAdminResourceLabels;

    protected static ?string $model = PartnerStorefront::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static ?string $navigationLabel = 'Partner Storefronts';

    protected static string|\UnitEnum|null $navigationGroup = 'V4 Partners';

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return PartnerStorefrontsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPartnerStorefronts::route('/'),
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
