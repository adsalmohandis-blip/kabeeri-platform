<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Concerns\ScopesToAccessibleOrganizations;
use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\Pages\ViewProduct;
use App\Models\Product;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
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
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    use ScopesToAccessibleOrganizations;

    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Products';

    protected static string|\UnitEnum|null $navigationGroup = 'V2 Commerce';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)->schema([
                Select::make('organization_id')
                    ->relationship('organization', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('site_id')
                    ->relationship('site', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set): mixed => blank($state) ? null : $set('slug', Str::slug((string) $state))),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                TextInput::make('sku')
                    ->maxLength(255),
                TextInput::make('price')
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('currency_code')
                    ->maxLength(3)
                    ->default('USD'),
                Select::make('status')
                    ->required()
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ])
                    ->default('draft'),
                Select::make('visibility')
                    ->required()
                    ->options([
                        'public' => 'Public',
                        'private' => 'Private',
                    ])
                    ->default('public'),
                Select::make('stock_status')
                    ->options([
                        'in_stock' => 'In stock',
                        'out_of_stock' => 'Out of stock',
                        'backorder' => 'Backorder',
                    ])
                    ->default('in_stock'),
            ]),
            Textarea::make('short_description')->columnSpanFull(),
            Textarea::make('description')->columnSpanFull(),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('name'),
            TextEntry::make('organization.name')->label('Organization')->placeholder('-'),
            TextEntry::make('site.name')->label('Site')->placeholder('-'),
            TextEntry::make('category.name')->label('Category')->placeholder('-'),
            TextEntry::make('sku')->placeholder('-'),
            TextEntry::make('price')->money(fn (Product $record): string => $record->currency_code ?: 'USD')->placeholder('-'),
            TextEntry::make('status')->badge(),
            TextEntry::make('visibility')->badge(),
            TextEntry::make('stock_status')->badge()->placeholder('-'),
            TextEntry::make('updated_at')->dateTime(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('organization.name')->label('Organization')->searchable(),
                TextColumn::make('site.name')->label('Site')->searchable()->placeholder('-'),
                TextColumn::make('category.name')->label('Category')->searchable()->placeholder('-'),
                TextColumn::make('sku')->searchable()->placeholder('-'),
                TextColumn::make('price')->money('USD')->sortable()->placeholder('-'),
                TextColumn::make('status')->badge(),
                TextColumn::make('visibility')->badge(),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'draft' => 'Draft',
                    'published' => 'Published',
                    'archived' => 'Archived',
                ]),
                SelectFilter::make('visibility')->options([
                    'public' => 'Public',
                    'private' => 'Private',
                ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'view' => ViewProduct::route('/{record}'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
