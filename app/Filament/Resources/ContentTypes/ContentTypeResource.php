<?php

namespace App\Filament\Resources\ContentTypes;

use App\Filament\Resources\ContentTypes\Pages\CreateContentType;
use App\Filament\Resources\ContentTypes\Pages\EditContentType;
use App\Filament\Resources\ContentTypes\Pages\ListContentTypes;
use App\Filament\Resources\ContentTypes\Pages\ViewContentType;
use App\Filament\Resources\ContentTypes\Schemas\ContentTypeForm;
use App\Filament\Resources\ContentTypes\Schemas\ContentTypeInfolist;
use App\Filament\Resources\ContentTypes\Tables\ContentTypesTable;
use App\Models\ContentType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ContentTypeResource extends Resource
{
    protected static ?string $model = ContentType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected static ?string $navigationLabel = 'Content Types';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $modelLabel = 'Content Type';

    protected static ?string $pluralModelLabel = 'Content Types';

    public static function form(Schema $schema): Schema
    {
        return ContentTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ContentTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContentTypes::route('/'),
            'create' => CreateContentType::route('/create'),
            'view' => ViewContentType::route('/{record}'),
            'edit' => EditContentType::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return static::getEloquentQuery();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('organization', function (Builder $organizationQuery) use ($user): void {
            $organizationQuery
                ->where('owner_user_id', $user->id)
                ->orWhereHas('memberships', function (Builder $membershipQuery) use ($user): void {
                    $membershipQuery
                        ->where('user_id', $user->id)
                        ->where('status', 'active');
                });
        });
    }
}
