<?php

namespace App\Filament\Resources\Organizations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class OrganizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Organization Name')
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
                        Select::make('account_type')
                            ->label('Account Type')
                            ->required()
                            ->options([
                                'individual' => 'Individual',
                                'business' => 'Business',
                                'agency' => 'Agency',
                                'enterprise' => 'Enterprise',
                                'legal_partner' => 'Legal Partner',
                                'platform_internal' => 'Platform Internal',
                            ])
                            ->default('business'),
                        Select::make('status')
                            ->label('Status')
                            ->required()
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ])
                            ->default('active'),
                        Select::make('country_id')
                            ->label('Country')
                            ->relationship('country', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Not set'),
                        TextInput::make('locale')
                            ->label('Locale')
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
