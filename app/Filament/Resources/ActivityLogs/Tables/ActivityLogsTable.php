<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use App\Models\ActivityLog;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No activity logs yet')
            ->emptyStateDescription('User and system activities will appear here once actions are performed.')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('action')
                    ->label('Action')
                    ->searchable(),
                TextColumn::make('organization.name')
                    ->label('Organization')
                    ->searchable(),
                TextColumn::make('company.trade_name')
                    ->label('Company')
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('site.name')
                    ->label('App')
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('actor.name')
                    ->label('Actor')
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(70)
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('organization_id')
                    ->label('Organization')
                    ->relationship('organization', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('company_id')
                    ->label('Company')
                    ->relationship('company', 'trade_name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('site_id')
                    ->label('App')
                    ->relationship('site', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('actor_user_id')
                    ->label('Actor')
                    ->relationship('actor', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('action')
                    ->label('Action')
                    ->options(function (): array {
                        $user = auth()->user();

                        if (! $user) {
                            return [];
                        }

                        return ActivityLog::query()
                            ->whereNotNull('organization_id')
                            ->whereHas('organization', function (Builder $organizationQuery) use ($user): void {
                                $organizationQuery
                                    ->where('owner_user_id', $user->id)
                                    ->orWhereHas('memberships', function (Builder $membershipQuery) use ($user): void {
                                        $membershipQuery
                                            ->where('user_id', $user->id)
                                            ->where('status', 'active');
                                    });
                            })
                            ->select('action')
                            ->distinct()
                            ->orderBy('action')
                            ->pluck('action', 'action')
                            ->all();
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }
}
