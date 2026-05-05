<?php

namespace App\Filament\Resources\ContentEntries\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ContentEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Select::make('site_id')
                            ->label('App')
                            ->relationship(
                                name: 'site',
                                titleAttribute: 'name',
                                modifyQueryUsing: function (Builder $query): void {
                                    $user = auth()->user();

                                    if (! $user) {
                                        $query->whereRaw('1 = 0');

                                        return;
                                    }

                                    $query->whereHas('organization', function (Builder $organizationQuery) use ($user): void {
                                        $organizationQuery
                                            ->where('owner_user_id', $user->id)
                                            ->orWhereHas('memberships', function (Builder $membershipQuery) use ($user): void {
                                                $membershipQuery->where('user_id', $user->id)->where('status', 'active');
                                            });
                                    });
                                },
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('content_type_id')
                            ->label('Content Type')
                            ->relationship('contentType', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set): void {
                                $set('slug', Str::slug((string) $state));
                            }),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->alphaDash(),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'archived' => 'Archived',
                            ])
                            ->default('draft')
                            ->required(),
                        Select::make('visibility')
                            ->label('Visibility')
                            ->options([
                                'public' => 'Public',
                                'private' => 'Private',
                                'organization_only' => 'Organization Only',
                            ])
                            ->default('public')
                            ->required(),
                    ]),
                Textarea::make('excerpt')
                    ->label('Excerpt')
                    ->rows(3)
                    ->columnSpanFull(),
                Textarea::make('body')
                    ->label('Body')
                    ->rows(10)
                    ->columnSpanFull(),
                KeyValue::make('seo')
                    ->label('SEO')
                    ->keyLabel('Key')
                    ->valueLabel('Value')
                    ->columnSpanFull(),
            ]);
    }
}
