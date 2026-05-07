<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\LocalizesAdminPageLabels;
use App\Filament\Pages\Concerns\HasV10AdminPageData;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class AdminWorkspaces extends Page
{
    use HasV10AdminPageData;
    use LocalizesAdminPageLabels;

    protected static string $pageKey = 'admin_workspaces';

    protected static ?string $slug = 'admin-workspaces';

    protected static ?string $title = 'Admin Workspaces';

    protected static ?string $navigationLabel = 'Admin Workspaces';

    protected static string|\UnitEnum|null $navigationGroup = 'V10 Admin Command';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::BuildingOffice2;

    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.pages.v10-admin-page';
}
