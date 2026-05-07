<?php

namespace App\Filament\Resources\MarketplaceCatalogItems;

use App\Filament\Concerns\LocalizesAdminResourceLabels;
use App\Filament\Resources\MarketplaceCatalogItems\Pages\ListMarketplaceCatalogItems;
use App\Filament\Resources\MarketplaceCatalogItems\Tables\MarketplaceCatalogItemsTable;
use App\Models\MarketplaceCatalogItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MarketplaceCatalogItemResource extends Resource
{
    use LocalizesAdminResourceLabels;

    protected static ?string $model = MarketplaceCatalogItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Marketplace Catalog';

    protected static string|\UnitEnum|null $navigationGroup = 'V4 Marketplace';

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return MarketplaceCatalogItemsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMarketplaceCatalogItems::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (! auth()->user()) {
            return $query->whereRaw('1 = 0');
        }

        return $query;
    }
}
