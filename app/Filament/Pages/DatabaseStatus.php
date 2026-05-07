<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\LocalizesAdminPageLabels;
use App\Filament\Pages\Concerns\HasV10AdminPageData;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class DatabaseStatus extends Page
{
    use HasV10AdminPageData;
    use LocalizesAdminPageLabels;

    protected static string $pageKey = 'database_status';

    protected static ?string $slug = 'database-status';

    protected static ?string $title = 'Database Status';

    protected static ?string $navigationLabel = 'Database Status';

    protected static string|\UnitEnum|null $navigationGroup = 'V10 Admin Command';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::CircleStack;

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.v10-admin-page';
}
