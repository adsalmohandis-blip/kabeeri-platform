<?php

namespace App\Filament\Resources\FeatureFlags\RelationManagers;

use App\Models\FeatureFlagOverride;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OverridesRelationManager extends RelationManager
{
    protected static string $relationship = 'overrides';

    protected static ?string $title = 'Overrides';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                    ->options([
                        'organization' => 'Organization',
                        'company' => 'Company',
                        'site' => 'Site',
                        'module' => 'Module',
                        'user' => 'User',
                    ])
                    ->placeholder('None'),
                TextInput::make('scope_id')
                    ->label('Scope ID')
                    ->numeric()
                    ->placeholder('Optional'),
                Toggle::make('value')
                    ->label('Enabled')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): void {
                $user = auth()->user();

                if (! $user) {
                    $query->whereRaw('1 = 0');

                    return;
                }

                $query
                    ->whereNotNull('organization_id')
                    ->whereHas('organization', function (Builder $organizationQuery) use ($user): void {
                        $organizationQuery
                            ->where('owner_user_id', $user->id)
                            ->orWhereHas('memberships', function (Builder $membershipQuery) use ($user): void {
                                $membershipQuery->where('user_id', $user->id)->where('status', 'active');
                            });
                    });
            })
            ->columns([
                TextColumn::make('organization.name')->label('Organization')->searchable(),
                TextColumn::make('scope_type')->label('Scope')->placeholder('-'),
                TextColumn::make('scope_id')->label('Scope ID')->placeholder('-'),
                IconColumn::make('value')->label('Enabled')->boolean(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->using(function (array $data): FeatureFlagOverride {
                        return FeatureFlagOverride::query()->create([
                            'feature_flag_id' => $this->getOwnerRecord()->id,
                            'organization_id' => $data['organization_id'],
                            'scope_type' => $data['scope_type'] ?? null,
                            'scope_id' => $data['scope_id'] ?? null,
                            'value' => (bool) ($data['value'] ?? false),
                        ]);
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([]);
    }
}
