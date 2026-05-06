<?php

namespace App\Filament\Resources\Organizations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrganizationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Organization Name'),
                TextEntry::make('slug')
                    ->label('Slug'),
                TextEntry::make('owner.name')
                    ->label('Owner')
                    ->placeholder('-'),
                TextEntry::make('account_type')
                    ->label('Account Type')
                    ->badge(),
                TextEntry::make('status')
                    ->label('Status')
                    ->badge(),
                TextEntry::make('plan_code')
                    ->label('Plan')
                    ->badge()
                    ->placeholder('free'),
                TextEntry::make('country.name')
                    ->label('Country')
                    ->placeholder('-'),
                TextEntry::make('locale')
                    ->label('Locale'),
                TextEntry::make('timezone')
                    ->label('Timezone'),
                TextEntry::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
