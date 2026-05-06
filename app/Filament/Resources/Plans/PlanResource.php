<?php

namespace App\Filament\Resources\Plans;

use App\Filament\Resources\Plans\Pages\ListPlans;
use App\Filament\Resources\Plans\Pages\ViewPlan;
use App\Models\Plan;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Plans & Entitlements';

    protected static string|\UnitEnum|null $navigationGroup = 'Freemium';

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('name'),
            TextEntry::make('code')->badge(),
            TextEntry::make('tier')->badge(),
            TextEntry::make('price_cents')->label('Price cents')->numeric(),
            TextEntry::make('currency_code'),
            TextEntry::make('billing_interval')->placeholder('-'),
            TextEntry::make('entitlements_count')->label('Entitlements'),
            TextEntry::make('is_public')->formatStateUsing(fn (bool $state): string => $state ? 'Public' : 'Private'),
            TextEntry::make('is_active')->formatStateUsing(fn (bool $state): string => $state ? 'Active' : 'Inactive'),
            TextEntry::make('updated_at')->dateTime(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('sort_order')->label('#')->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('code')->badge()->searchable(),
                TextColumn::make('tier')->badge(),
                TextColumn::make('price_cents')->label('Price')->money('USD', divideBy: 100)->sortable(),
                TextColumn::make('billing_interval')->placeholder('-'),
                TextColumn::make('entitlements_count')->label('Entitlements')->counts('entitlements')->sortable(),
                IconColumn::make('is_public')->boolean(),
                IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                SelectFilter::make('tier')->options([
                    'free' => 'Free',
                    'starter' => 'Starter / Pro',
                    'business' => 'Business',
                    'agency' => 'Agency',
                    'enterprise' => 'Enterprise',
                ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlans::route('/'),
            'view' => ViewPlan::route('/{record}'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return static::getEloquentQuery();
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
