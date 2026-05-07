<?php

namespace App\Filament\Resources\Sites\Schemas;

use App\Models\Site;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class SiteForm
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
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('company_id')
                            ->label('Company (Optional)')
                            ->relationship(
                                name: 'company',
                                titleAttribute: 'trade_name',
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
                            ->placeholder('No company'),
                        TextInput::make('name')
                            ->label('App Name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set, ?Site $record): void {
                                if ($record === null) {
                                    $set('slug', Site::normalizeUsername((string) $state));
                                }
                            }),
                        TextInput::make('slug')
                            ->label('Username')
                            ->helperText('Fixed permalink. Used in /app/username and cannot be changed after creation.')
                            ->required()
                            ->maxLength(255)
                            ->alphaDash()
                            ->disabled(fn (?Site $record): bool => $record !== null)
                            ->dehydrated(fn (?Site $record): bool => $record === null),
                        Select::make('site_type')
                            ->label('App Type')
                            ->required()
                            ->options([
                                'website' => 'Website',
                                'blog' => 'Blog',
                                'store' => 'Store',
                                'portal' => 'Portal',
                                'landing' => 'Landing',
                                'internal_portal' => 'Internal Portal',
                                'mall_seller_site' => 'Mall Seller Site',
                            ])
                            ->default('website'),
                        Select::make('status')
                            ->label('Status')
                            ->required()
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'draft' => 'Draft',
                            ])
                            ->default('active'),
                        TextInput::make('language')
                            ->label('Language')
                            ->required()
                            ->default('ar')
                            ->maxLength(10),
                        TextInput::make('timezone')
                            ->label('Timezone')
                            ->required()
                            ->default('Africa/Cairo')
                            ->maxLength(255),
                    ]),
            ]);
    }
}
