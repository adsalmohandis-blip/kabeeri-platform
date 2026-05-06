<?php

namespace App\Filament\Resources\DataIngestionPipelines;

use App\Filament\Resources\DataIngestionPipelines\Pages\ListDataIngestionPipelines;
use App\Models\DataIngestionPipeline;
use App\Models\OrganizationMembership;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DataIngestionPipelineResource extends Resource
{
    protected static ?string $model = DataIngestionPipeline::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCircleStack;

    protected static ?string $navigationLabel = 'Data Pipelines';

    protected static string|\UnitEnum|null $navigationGroup = 'V6 Data Platform';

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable(),
            TextColumn::make('source_type')->badge(),
            TextColumn::make('status')->badge(),
            TextColumn::make('updated_at')->dateTime()->sortable(),
        ])->recordActions([])->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return ['index' => ListDataIngestionPipelines::route('/')];
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
