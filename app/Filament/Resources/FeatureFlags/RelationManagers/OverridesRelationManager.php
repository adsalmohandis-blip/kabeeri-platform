<?php

namespace App\Filament\Resources\FeatureFlags\RelationManagers;

use App\Models\Company;
use App\Models\FeatureFlagOverride;
use App\Models\Site;
use App\Models\User;
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
use Illuminate\Validation\ValidationException;

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
                    ->authorize(function (): bool {
                        $user = auth()->user();

                        return $user instanceof User
                            && $user->can('create', FeatureFlagOverride::class);
                    })
                    ->using(function (array $data): FeatureFlagOverride {
                        $organizationId = (int) $data['organization_id'];
                        $scopeType = $data['scope_type'] ?? null;
                        $scopeId = $data['scope_id'] ?? null;

                        $this->assertScopeIsWithinOrganization(
                            organizationId: $organizationId,
                            scopeType: is_string($scopeType) ? $scopeType : null,
                            scopeId: is_numeric($scopeId) ? (int) $scopeId : null,
                        );

                        return FeatureFlagOverride::query()->create([
                            'feature_flag_id' => $this->getOwnerRecord()->id,
                            'organization_id' => $organizationId,
                            'scope_type' => $scopeType,
                            'scope_id' => $scopeId,
                            'value' => (bool) ($data['value'] ?? false),
                        ]);
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->authorize(function (FeatureFlagOverride $record): bool {
                        $user = auth()->user();

                        return $user instanceof User
                            && $user->can('update', $record);
                    })
                    ->using(function (FeatureFlagOverride $record, array $data): FeatureFlagOverride {
                        $organizationId = (int) ($data['organization_id'] ?? $record->organization_id);
                        $scopeType = $data['scope_type'] ?? $record->scope_type;
                        $scopeId = $data['scope_id'] ?? $record->scope_id;

                        $this->assertScopeIsWithinOrganization(
                            organizationId: $organizationId,
                            scopeType: is_string($scopeType) ? $scopeType : null,
                            scopeId: is_numeric($scopeId) ? (int) $scopeId : null,
                        );

                        $record->fill([
                            'organization_id' => $organizationId,
                            'scope_type' => $scopeType,
                            'scope_id' => $scopeId,
                            'value' => (bool) ($data['value'] ?? $record->value),
                        ])->save();

                        return $record;
                    }),
                DeleteAction::make()
                    ->authorize(function (FeatureFlagOverride $record): bool {
                        $user = auth()->user();

                        return $user instanceof User
                            && $user->can('delete', $record);
                    }),
            ])
            ->toolbarActions([]);
    }

    protected function assertScopeIsWithinOrganization(int $organizationId, ?string $scopeType, ?int $scopeId): void
    {
        if ($scopeId === null || $scopeType === null) {
            return;
        }

        if ($scopeType === 'company') {
            $isCompanyInOrganization = Company::query()
                ->where('id', $scopeId)
                ->where('organization_id', $organizationId)
                ->exists();

            if (! $isCompanyInOrganization) {
                throw ValidationException::withMessages([
                    'scope_id' => 'Selected company scope does not belong to the selected organization.',
                ]);
            }
        }

        if ($scopeType === 'site') {
            $isSiteInOrganization = Site::query()
                ->where('id', $scopeId)
                ->where('organization_id', $organizationId)
                ->exists();

            if (! $isSiteInOrganization) {
                throw ValidationException::withMessages([
                    'scope_id' => 'Selected app scope does not belong to the selected organization.',
                ]);
            }
        }
    }
}
