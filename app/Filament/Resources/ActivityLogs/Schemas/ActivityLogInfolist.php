<?php

namespace App\Filament\Resources\ActivityLogs\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ActivityLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('created_at')
                    ->label('Created At')
                    ->dateTime(),
                TextEntry::make('action')
                    ->label('Action'),
                TextEntry::make('organization.name')
                    ->label('Organization'),
                TextEntry::make('company.trade_name')
                    ->label('Company')
                    ->placeholder('-'),
                TextEntry::make('site.name')
                    ->label('App')
                    ->placeholder('-'),
                TextEntry::make('actor.name')
                    ->label('Actor')
                    ->placeholder('-'),
                TextEntry::make('subject_type')
                    ->label('Subject Type')
                    ->placeholder('-'),
                TextEntry::make('subject_id')
                    ->label('Subject ID')
                    ->placeholder('-'),
                TextEntry::make('description')
                    ->label('Description')
                    ->placeholder('-'),
                TextEntry::make('ip_address')
                    ->label('IP Address')
                    ->placeholder('-'),
                TextEntry::make('user_agent')
                    ->label('User Agent')
                    ->placeholder('-'),
                KeyValueEntry::make('metadata')
                    ->label('Metadata')
                    ->placeholder('-'),
            ]);
    }
}
