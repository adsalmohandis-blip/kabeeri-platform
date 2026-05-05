<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CompanyForm
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
                        TextInput::make('trade_name')
                            ->label('Trade Name')
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
                            ->alphaDash()
                            ->unique(ignoreRecord: true),
                        TextInput::make('legal_name')
                            ->label('Legal Name')
                            ->maxLength(255),
                        Select::make('country_id')
                            ->label('Country')
                            ->relationship('country', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Not set'),
                        TextInput::make('city')
                            ->label('City')
                            ->maxLength(255),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ])
                            ->default('draft')
                            ->required(),
                        Select::make('verification_status')
                            ->label('Verification Status')
                            ->options([
                                'not_submitted' => 'Not Submitted',
                                'submitted' => 'Submitted',
                                'under_review' => 'Under Review',
                                'verified' => 'Verified',
                                'rejected' => 'Rejected',
                            ])
                            ->default('not_submitted')
                            ->required(),
                    ]),
            ]);
    }
}
