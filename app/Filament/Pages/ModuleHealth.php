<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\LocalizesAdminPageLabels;
use App\Filament\Pages\Concerns\HasV10AdminPageData;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ModuleHealth extends Page
{
    use HasV10AdminPageData;
    use LocalizesAdminPageLabels;

    protected static string $pageKey = 'module_health';

    protected static ?string $slug = 'module-health';

    protected static ?string $title = 'Module Health';

    protected static ?string $navigationLabel = 'Module Health';

    protected static string|\UnitEnum|null $navigationGroup = 'V10 Admin Command';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::RectangleGroup;

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.v10-admin-page';
}
