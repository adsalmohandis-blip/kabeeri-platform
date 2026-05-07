<?php

namespace App\Filament\Resources\GrcRisks;

use App\Filament\Concerns\LocalizesAdminResourceLabels;
use App\Filament\Resources\GrcRisks\Pages\ListGrcRisks;
use App\Models\GrcRisk;
use App\Models\OrganizationMembership;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GrcRiskResource extends Resource
{
    use LocalizesAdminResourceLabels;

    protected static ?string $model = GrcRisk::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?string $navigationLabel = 'GRC Risks';

    protected static string|\UnitEnum|null $navigationGroup = 'V6 GRC';

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable(),
            TextColumn::make('risk_level')->badge(),
            TextColumn::make('status')->badge(),
            TextColumn::make('updated_at')->dateTime()->sortable(),
        ])->recordActions([])->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return ['index' => ListGrcRisks::route('/')];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();
        if (! $user) {
            return $query->whereRaw('1 = 0');
        }
        $organizationIds = OrganizationMembership::query()->where('user_id', $user->id)->where('status', 'active')->pluck('organization_id');

        return $query->whereIn('organization_id', $organizationIds);
    }
}
