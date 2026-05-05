<?php

namespace App\Filament\Resources\ContentEntries\Tables;

use App\Models\ContentEntry;
use App\Models\User;
use App\Modules\CMS\Actions\PublishContentEntry as PublishContentEntryAction;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContentEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Title')->searchable(),
                TextColumn::make('site.name')->label('App')->searchable(),
                TextColumn::make('contentType.name')->label('Type')->searchable(),
                TextColumn::make('status')->label('Status')->badge(),
                TextColumn::make('visibility')->label('Visibility')->badge(),
                TextColumn::make('published_at')->label('Published At')->dateTime()->placeholder('-'),
            ])
            ->recordActions([
                Action::make('publish')
                    ->label('Publish')
                    ->requiresConfirmation()
                    ->visible(fn (ContentEntry $record): bool => $record->status !== 'published')
                    ->authorize(function (ContentEntry $record): bool {
                        $user = auth()->user();

                        return $user instanceof User && $user->can('publish', $record);
                    })
                    ->action(function (ContentEntry $record): void {
                        $user = auth()->user();

                        if (! ($user instanceof User)) {
                            abort(403);
                        }

                        app(PublishContentEntryAction::class)($user, $record);
                    }),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
