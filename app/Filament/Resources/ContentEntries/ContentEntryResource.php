<?php

namespace App\Filament\Resources\ContentEntries;

use App\Filament\Resources\ContentEntries\Pages\CreateContentEntry;
use App\Filament\Resources\ContentEntries\Pages\EditContentEntry;
use App\Filament\Resources\ContentEntries\Pages\ListContentEntries;
use App\Filament\Resources\ContentEntries\Pages\ViewContentEntry;
use App\Filament\Resources\ContentEntries\Schemas\ContentEntryForm;
use App\Filament\Resources\ContentEntries\Schemas\ContentEntryInfolist;
use App\Filament\Resources\ContentEntries\Tables\ContentEntriesTable;
use App\Models\ContentEntry;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ContentEntryResource extends Resource
{
    protected static ?string $model = ContentEntry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Content Entries';

    protected static ?string $modelLabel = 'Content Entry';

    protected static ?string $pluralModelLabel = 'Content Entries';

    public static function form(Schema $schema): Schema
    {
        return ContentEntryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ContentEntryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentEntriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContentEntries::route('/'),
            'create' => CreateContentEntry::route('/create'),
            'view' => ViewContentEntry::route('/{record}'),
            'edit' => EditContentEntry::route('/{record}/edit'),
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
