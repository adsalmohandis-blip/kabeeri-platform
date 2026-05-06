<?php

namespace App\Filament\Resources\Leads\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('ulid')
                    ->required(),
                Select::make('organization_id')
                    ->relationship('organization', 'name')
                    ->required(),
                Select::make('site_id')
                    ->relationship('site', 'name')
                    ->default(null),
                Select::make('company_id')
                    ->relationship('company', 'id')
                    ->default(null),
                Select::make('form_submission_id')
                    ->relationship('formSubmission', 'id')
                    ->default(null),
                TextInput::make('name')
                    ->default(null),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->default(null),
                TextInput::make('phone')
                    ->tel()
                    ->default(null),
                TextInput::make('source')
                    ->required()
                    ->default('form'),
                TextInput::make('status')
                    ->required()
                    ->default('new'),
                TextInput::make('score')
                    ->numeric()
                    ->default(null),
                TextInput::make('assigned_to')
                    ->numeric()
                    ->default(null),
                Textarea::make('message')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('metadata')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
