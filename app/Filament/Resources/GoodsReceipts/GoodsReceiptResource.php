<?php

namespace App\Filament\Resources\GoodsReceipts;

use App\Filament\Concerns\LocalizesAdminResourceLabels;
use App\Filament\Resources\Concerns\ScopesToAccessibleOrganizations;
use App\Filament\Resources\GoodsReceipts\Pages\CreateGoodsReceipt;
use App\Filament\Resources\GoodsReceipts\Pages\EditGoodsReceipt;
use App\Filament\Resources\GoodsReceipts\Pages\ListGoodsReceipts;
use App\Filament\Resources\GoodsReceipts\Pages\ViewGoodsReceipt;
use App\Filament\Resources\GoodsReceipts\Schemas\GoodsReceiptForm;
use App\Filament\Resources\GoodsReceipts\Schemas\GoodsReceiptInfolist;
use App\Filament\Resources\GoodsReceipts\Tables\GoodsReceiptsTable;
use App\Models\GoodsReceipt;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GoodsReceiptResource extends Resource
{
    use LocalizesAdminResourceLabels;
    use ScopesToAccessibleOrganizations;

    protected static ?string $model = GoodsReceipt::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory & Purchasing';

    public static function form(Schema $schema): Schema
    {
        return GoodsReceiptForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GoodsReceiptInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GoodsReceiptsTable::configure($table);
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
            'index' => ListGoodsReceipts::route('/'),
            'create' => CreateGoodsReceipt::route('/create'),
            'view' => ViewGoodsReceipt::route('/{record}'),
            'edit' => EditGoodsReceipt::route('/{record}/edit'),
        ];
    }
}
