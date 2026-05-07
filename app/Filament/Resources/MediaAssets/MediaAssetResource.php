<?php

namespace App\Filament\Resources\MediaAssets;

use App\Filament\Concerns\LocalizesAdminResourceLabels;
use App\Filament\Resources\MediaAssets\Pages\CreateMediaAsset;
use App\Filament\Resources\MediaAssets\Pages\EditMediaAsset;
use App\Filament\Resources\MediaAssets\Pages\ListMediaAssets;
use App\Filament\Resources\MediaAssets\Pages\ViewMediaAsset;
use App\Filament\Resources\MediaAssets\Schemas\MediaAssetForm;
use App\Filament\Resources\MediaAssets\Schemas\MediaAssetInfolist;
use App\Filament\Resources\MediaAssets\Tables\MediaAssetsTable;
use App\Models\MediaAsset;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MediaAssetResource extends Resource
{
    use LocalizesAdminResourceLabels;

    protected static ?string $model = MediaAsset::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $navigationLabel = 'Media Library';

    protected static string|\UnitEnum|null $navigationGroup = 'Media';

    protected static ?string $modelLabel = 'Media Asset';

    protected static ?string $pluralModelLabel = 'Media Assets';

    public static function form(Schema $schema): Schema
    {
        return MediaAssetForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MediaAssetInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MediaAssetsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMediaAssets::route('/'),
            'create' => CreateMediaAsset::route('/create'),
            'view' => ViewMediaAsset::route('/{record}'),
            'edit' => EditMediaAsset::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return static::getEloquentQuery();
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
                    $membershipQuery
                        ->where('user_id', $user->id)
                        ->where('status', 'active');
                });
        });
    }
}
