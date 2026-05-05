<?php

namespace App\Filament\Resources\MediaAssets\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class MediaAssetForm
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
                            ->placeholder('No app'),
                        Select::make('company_id')
                            ->label('Company (Optional)')
                            ->relationship('company', 'trade_name')
                            ->searchable()
                            ->preload()
                            ->placeholder('No company'),
                        Select::make('visibility')
                            ->label('Visibility')
                            ->required()
                            ->options([
                                'public' => 'Public',
                                'private' => 'Private',
                                'organization_only' => 'Organization Only',
                                'company_only' => 'Company Only',
                                'sensitive' => 'Sensitive',
                            ])
                            ->default('private'),
                    ]),
                FileUpload::make('uploaded_file')
                    ->label('File')
                    ->disk('public')
                    ->directory('media-assets')
                    ->preserveFilenames()
                    ->downloadable()
                    ->openable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(false)
                    ->columnSpanFull(),
                TextInput::make('alt_text')
                    ->label('Alt Text')
                    ->maxLength(255),
                Textarea::make('caption')
                    ->label('Caption')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
