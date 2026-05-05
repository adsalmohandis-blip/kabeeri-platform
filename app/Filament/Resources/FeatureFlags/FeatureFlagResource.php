<?php

namespace App\Filament\Resources\FeatureFlags;

use App\Filament\Resources\FeatureFlags\Pages\ListFeatureFlags;
use App\Filament\Resources\FeatureFlags\Pages\ViewFeatureFlag;
use App\Filament\Resources\FeatureFlags\RelationManagers\OverridesRelationManager;
use App\Filament\Resources\FeatureFlags\Schemas\FeatureFlagInfolist;
use App\Filament\Resources\FeatureFlags\Tables\FeatureFlagsTable;
use App\Models\FeatureFlag;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FeatureFlagResource extends Resource
{
    protected static ?string $model = FeatureFlag::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBolt;

    protected static ?string $navigationLabel = 'Feature Flags';

    protected static ?string $modelLabel = 'Feature Flag';

    protected static ?string $pluralModelLabel = 'Feature Flags';

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function infolist(Schema $schema): Schema
    {
        return FeatureFlagInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FeatureFlagsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            OverridesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFeatureFlags::route('/'),
            'view' => ViewFeatureFlag::route('/{record}'),
        ];
    }
}
