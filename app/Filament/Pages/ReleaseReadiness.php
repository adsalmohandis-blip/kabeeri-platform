<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\HasV10AdminPageData;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ReleaseReadiness extends Page
{
    use HasV10AdminPageData;

    protected static string $pageKey = 'release_readiness';

    protected static ?string $slug = 'release-readiness';

    protected static ?string $title = 'Release Readiness';

    protected static ?string $navigationLabel = 'Release Readiness';

    protected static string|\UnitEnum|null $navigationGroup = 'V10 Admin Command';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::RocketLaunch;

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.v10-admin-page';
}
