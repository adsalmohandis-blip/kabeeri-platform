<?php

namespace App\Filament\Resources\ModerationCases;

use App\Filament\Concerns\LocalizesAdminResourceLabels;
use App\Filament\Resources\ModerationCases\Pages\ListModerationCases;
use App\Filament\Resources\ModerationCases\Tables\ModerationCasesTable;
use App\Models\ModerationCase;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ModerationCaseResource extends Resource
{
    use LocalizesAdminResourceLabels;

    protected static ?string $model = ModerationCase::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $navigationLabel = 'Moderation Cases';

    protected static string|\UnitEnum|null $navigationGroup = 'V4 Moderation';

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return ModerationCasesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListModerationCases::route('/'),
        ];
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
                    $membershipQuery->where('user_id', $user->id)->where('status', 'active');
                });
        });
    }
}
