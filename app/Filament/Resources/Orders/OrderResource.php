<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Concerns\ScopesToAccessibleOrganizations;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\Pages\ViewOrder;
use App\Models\Order;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    use ScopesToAccessibleOrganizations;

    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Orders';

    protected static string|\UnitEnum|null $navigationGroup = 'V2 Commerce';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)->schema([
                Select::make('organization_id')
                    ->relationship('organization', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled(),
                Select::make('site_id')
                    ->relationship('site', 'name')
                    ->searchable()
                    ->preload()
                    ->disabled(),
                Select::make('status')
                    ->required()
                    ->options([
                        'draft' => 'Draft',
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                Select::make('payment_status')
                    ->required()
                    ->options([
                        'unpaid' => 'Unpaid',
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'refunded' => 'Refunded',
                    ]),
                TextInput::make('customer_name')->maxLength(255),
                TextInput::make('customer_email')->email()->maxLength(255),
                TextInput::make('customer_phone')->maxLength(255),
                TextInput::make('currency_code')->maxLength(3),
                TextInput::make('subtotal')->numeric()->disabled(),
                TextInput::make('discount_total')->numeric()->disabled(),
                TextInput::make('total')->numeric()->disabled(),
            ]),
            Textarea::make('notes')->columnSpanFull(),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('ulid')->label('Order ULID'),
            TextEntry::make('organization.name')->label('Organization')->placeholder('-'),
            TextEntry::make('site.name')->label('Site')->placeholder('-'),
            TextEntry::make('customer_name')->placeholder('-'),
            TextEntry::make('customer_email')->placeholder('-'),
            TextEntry::make('status')->badge(),
            TextEntry::make('payment_status')->badge(),
            TextEntry::make('subtotal')->money(fn (Order $record): string => $record->currency_code ?: 'USD'),
            TextEntry::make('discount_total')->money(fn (Order $record): string => $record->currency_code ?: 'USD'),
            TextEntry::make('total')->money(fn (Order $record): string => $record->currency_code ?: 'USD'),
            TextEntry::make('items_count')->label('Items'),
            TextEntry::make('updated_at')->dateTime(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('ulid')->label('Order')->searchable()->limit(12),
                TextColumn::make('organization.name')->label('Organization')->searchable(),
                TextColumn::make('site.name')->label('Site')->searchable()->placeholder('-'),
                TextColumn::make('customer_name')->searchable()->placeholder('-'),
                TextColumn::make('customer_email')->searchable()->placeholder('-'),
                TextColumn::make('status')->badge(),
                TextColumn::make('payment_status')->badge(),
                TextColumn::make('total')->money('USD')->sortable(),
                TextColumn::make('items_count')->label('Items')->counts('items')->sortable(),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'draft' => 'Draft',
                    'pending' => 'Pending',
                    'processing' => 'Processing',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ]),
                SelectFilter::make('payment_status')->options([
                    'unpaid' => 'Unpaid',
                    'pending' => 'Pending',
                    'paid' => 'Paid',
                    'refunded' => 'Refunded',
                ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'view' => ViewOrder::route('/{record}'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }
}
