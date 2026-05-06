<?php

namespace App\Filament\Resources\Redirects;

use App\Filament\Resources\Concerns\ScopesToAccessibleOrganizations;
use App\Filament\Resources\Redirects\Pages\CreateRedirect;
use App\Filament\Resources\Redirects\Pages\EditRedirect;
use App\Filament\Resources\Redirects\Pages\ListRedirects;
use App\Filament\Resources\Redirects\Pages\ViewRedirect;
use App\Models\Redirect;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
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

class RedirectResource extends Resource
{
    use ScopesToAccessibleOrganizations;

    protected static ?string $model = Redirect::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Redirects';

    protected static string|\UnitEnum|null $navigationGroup = 'V2 CMS';

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
                TextInput::make('source_path')
                    ->required()
                    ->maxLength(2048)
                    ->placeholder('/old-page'),
                TextInput::make('target_url')
                    ->required()
                    ->maxLength(2048)
                    ->placeholder('/new-page'),
                Select::make('status_code')
                    ->required()
                    ->options([
                        301 => '301 Permanent',
                        302 => '302 Temporary',
                        307 => '307 Temporary',
                        308 => '308 Permanent',
                    ])
                    ->default(301),
                Select::make('status')
                    ->required()
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->default('active'),
            ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('source_path')->label('Source'),
            TextEntry::make('target_url')->label('Target'),
            TextEntry::make('status_code')->badge(),
            TextEntry::make('status')->badge(),
            TextEntry::make('organization.name')->label('Organization')->placeholder('-'),
            TextEntry::make('site.name')->label('Site')->placeholder('-'),
            TextEntry::make('hit_count')->numeric(),
            TextEntry::make('last_hit_at')->dateTime()->placeholder('-'),
            TextEntry::make('updated_at')->dateTime(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('source_path')->label('Source')->searchable(),
                TextColumn::make('target_url')->label('Target')->searchable()->limit(50),
                TextColumn::make('organization.name')->label('Organization')->searchable(),
                TextColumn::make('site.name')->label('Site')->searchable()->placeholder('-'),
                TextColumn::make('status_code')->badge()->sortable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('hit_count')->numeric()->sortable(),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'active' => 'Active',
                    'inactive' => 'Inactive',
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
            'index' => ListRedirects::route('/'),
            'create' => CreateRedirect::route('/create'),
            'view' => ViewRedirect::route('/{record}'),
            'edit' => EditRedirect::route('/{record}/edit'),
        ];
    }
}
