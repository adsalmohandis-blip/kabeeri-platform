<?php

namespace App\Filament\Resources\DeveloperMarketplaceListings;

use App\Filament\Resources\DeveloperMarketplaceListings\Pages\ListDeveloperMarketplaceListings;
use App\Models\DeveloperMarketplaceListing;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DeveloperMarketplaceListingResource extends Resource
{
    protected static ?string $model = DeveloperMarketplaceListing::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Developer Marketplace';

    protected static string|\UnitEnum|null $navigationGroup = 'V6 Marketplace';

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable(),
            TextColumn::make('listing_type')->badge(),
            TextColumn::make('status')->badge(),
            TextColumn::make('visibility')->badge(),
            TextColumn::make('updated_at')->dateTime()->sortable(),
        ])->recordActions([])->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return ['index' => ListDeveloperMarketplaceListings::route('/')];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        return auth()->user() ? $query : $query->whereRaw('1 = 0');
    }
}
