<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\HasV10AdminPageData;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class SystemCheck extends Page
{
    use HasV10AdminPageData;

    protected static string $pageKey = 'system_check';

    protected static ?string $slug = 'system-check';

    protected static ?string $title = 'System Check';

    protected static ?string $navigationLabel = 'System Check';

    protected static string|\UnitEnum|null $navigationGroup = 'V10 Admin Command';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::ShieldCheck;

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.v10-admin-page';
}
