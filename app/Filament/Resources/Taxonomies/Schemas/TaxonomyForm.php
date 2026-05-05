<?php

namespace App\Filament\Resources\Taxonomies\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class TaxonomyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Select::make('organization_id')
                            ->label('Organization')
                            ->relationship(
                                name: 'organization',
                                titleAttribute: 'name',
                                modifyQueryUsing: function (Builder $query): void {
                                    $user = auth()->user();

                                    if (! $user) {
                                        $query->whereRaw('1 = 0');

                                        return;
                                    }

                                    $query->where(function (Builder $organizationQuery) use ($user): void {
                                        $organizationQuery
                                            ->where('owner_user_id', $user->id)
                                            ->orWhereHas('memberships', function (Builder $membershipQuery) use ($user): void {
                                                $membershipQuery->where('user_id', $user->id)->where('status', 'active');
                                            });
                                    });
                                },
                            )
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('site_id')
                            ->label('App (Optional)')
                            ->relationship('site', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Organization level'),
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set): void {
                                $set('slug', Str::slug((string) $state));
                            }),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->alphaDash(),
                        Select::make('type')
                            ->label('Type')
                            ->required()
                            ->options([
                                'category' => 'Category',
                                'tag' => 'Tag',
                            ])
                            ->default('category'),
                    ]),
            ]);
    }
}
