<?php

namespace App\Filament\Resources\Settings;

use App\Filament\Resources\Settings\Pages\CreateSetting;
use App\Filament\Resources\Settings\Pages\EditSetting;
use App\Filament\Resources\Settings\Pages\ListSettings;
use App\Filament\Resources\Settings\Pages\ViewSetting;
use App\Filament\Resources\Settings\Schemas\SettingForm;
use App\Filament\Resources\Settings\Schemas\SettingInfolist;
use App\Filament\Resources\Settings\Tables\SettingsTable;
use App\Models\Setting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsVertical;

    protected static ?string $navigationLabel = 'Settings';

    public static function form(Schema $schema): Schema
    {
        return SettingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SettingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SettingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSettings::route('/'),
            'create' => CreateSetting::route('/create'),
            'view' => ViewSetting::route('/{record}'),
            'edit' => EditSetting::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return static::getEloquentQuery();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->where('scope_type', '!=', 'platform')
            ->whereNotNull('organization_id');
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
