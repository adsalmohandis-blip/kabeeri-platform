<?php

namespace App\Filament\Resources\Coupons;

use App\Filament\Concerns\LocalizesAdminResourceLabels;
use App\Filament\Resources\Concerns\ScopesToAccessibleOrganizations;
use App\Filament\Resources\Coupons\Pages\CreateCoupon;
use App\Filament\Resources\Coupons\Pages\EditCoupon;
use App\Filament\Resources\Coupons\Pages\ListCoupons;
use App\Filament\Resources\Coupons\Pages\ViewCoupon;
use App\Models\Coupon;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
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

class CouponResource extends Resource
{
    use LocalizesAdminResourceLabels;
    use ScopesToAccessibleOrganizations;

    protected static ?string $model = Coupon::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Coupons';

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
                TextInput::make('code')
                    ->required()
                    ->maxLength(255)
                    ->extraInputAttributes(['style' => 'text-transform: uppercase']),
                Select::make('discount_type')
                    ->required()
                    ->options([
                        'fixed' => 'Fixed amount',
                        'percent' => 'Percent',
                    ])
                    ->default('fixed'),
                TextInput::make('discount_value')
                    ->required()
                    ->numeric()
                    ->minValue(0),
                TextInput::make('usage_limit')
                    ->numeric()
                    ->minValue(1),
                TextInput::make('used_count')
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
                Select::make('status')
                    ->required()
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'expired' => 'Expired',
                    ])
                    ->default('active'),
                DateTimePicker::make('starts_at'),
                DateTimePicker::make('ends_at'),
            ]),
            Textarea::make('description')->columnSpanFull(),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('code'),
            TextEntry::make('organization.name')->label('Organization')->placeholder('-'),
            TextEntry::make('site.name')->label('Site')->placeholder('-'),
            TextEntry::make('discount_type')->badge(),
            TextEntry::make('discount_value'),
            TextEntry::make('usage_limit')->placeholder('Unlimited'),
            TextEntry::make('used_count'),
            TextEntry::make('status')->badge(),
            TextEntry::make('starts_at')->dateTime()->placeholder('-'),
            TextEntry::make('ends_at')->dateTime()->placeholder('-'),
            TextEntry::make('updated_at')->dateTime(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('code')->searchable()->sortable(),
                TextColumn::make('organization.name')->label('Organization')->searchable(),
                TextColumn::make('site.name')->label('Site')->searchable()->placeholder('-'),
                TextColumn::make('discount_type')->badge(),
                TextColumn::make('discount_value')->numeric()->sortable(),
                TextColumn::make('usage_limit')->placeholder('Unlimited'),
                TextColumn::make('used_count')->numeric()->sortable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                    'expired' => 'Expired',
                ]),
                SelectFilter::make('discount_type')->options([
                    'fixed' => 'Fixed amount',
                    'percent' => 'Percent',
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
            'index' => ListCoupons::route('/'),
            'create' => CreateCoupon::route('/create'),
            'view' => ViewCoupon::route('/{record}'),
            'edit' => EditCoupon::route('/{record}/edit'),
        ];
    }
}
