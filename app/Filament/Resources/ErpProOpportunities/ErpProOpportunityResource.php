<?php

namespace App\Filament\Resources\ErpProOpportunities;

use App\Filament\Concerns\LocalizesAdminResourceLabels;
use App\Filament\Resources\ErpProOpportunities\Pages\ListErpProOpportunities;
use App\Models\ErpProOpportunity;
use App\Models\OrganizationMembership;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ErpProOpportunityResource extends Resource
{
    use LocalizesAdminResourceLabels;

    protected static ?string $model = ErpProOpportunity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'ERP Pro Opportunities';

    protected static string|\UnitEnum|null $navigationGroup = 'V5 ERP Pro';

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('priority')->badge(),
                TextColumn::make('expected_value')->money('EGP'),
                TextColumn::make('probability')->suffix('%'),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListErpProOpportunities::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        $organizationIds = OrganizationMembership::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->pluck('organization_id');

        return $query->whereIn('organization_id', $organizationIds);
    }
}
