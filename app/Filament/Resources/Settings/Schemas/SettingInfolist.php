<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SettingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('organization.name')->label('Organization'),
                TextEntry::make('scope_type')->label('Scope Type'),
                TextEntry::make('scope_id')->label('Scope ID')->placeholder('-'),
                TextEntry::make('key')->label('Key'),
                TextEntry::make('value_type')->label('Value Type'),
                TextEntry::make('is_encrypted')->label('Encrypted')->badge(),
            ]);
    }
}
