<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class SettingForm
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
                        Select::make('scope_type')
                            ->label('Scope Type')
                            ->required()
                            ->options([
                                'organization' => 'Organization',
                                'company' => 'Company',
                                'site' => 'Site',
                                'module' => 'Module',
                                'user' => 'User',
                            ]),
                        TextInput::make('scope_id')
                            ->label('Scope ID')
                            ->numeric()
                            ->placeholder('Optional'),
                        TextInput::make('key')
                            ->label('Key')
                            ->required(),
                        TextInput::make('value_type')
                            ->label('Value Type')
                            ->required()
                            ->default('string'),
                        Toggle::make('is_encrypted')
                            ->label('Encrypted')
                            ->default(false),
                    ]),
                KeyValue::make('value')
                    ->label('Value')
                    ->keyLabel('Key')
                    ->valueLabel('Value')
                    ->columnSpanFull(),
            ]);
    }
}
