<?php

namespace App\Filament\Resources\IntegrationConnectors;

use App\Filament\Resources\IntegrationConnectors\Pages\ListIntegrationConnectors;
use App\Models\IntegrationConnector;
use App\Models\OrganizationMembership;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class IntegrationConnectorResource extends Resource
{
    protected static ?string $model = IntegrationConnector::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static ?string $navigationLabel = 'Integration Connectors';

    protected static string|\UnitEnum|null $navigationGroup = 'V5 Integration Hub';

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
                TextColumn::make('provider')->badge(),
                TextColumn::make('connector_type')->badge(),
                TextColumn::make('status')->badge(),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIntegrationConnectors::route('/'),
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

        return $query->whereNull('organization_id')->orWhereIn('organization_id', $organizationIds);
    }
}
