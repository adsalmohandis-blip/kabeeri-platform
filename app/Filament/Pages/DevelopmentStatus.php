<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\LocalizesAdminPageLabels;
use App\Support\RootDashboardData;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class DevelopmentStatus extends Page
{
    use LocalizesAdminPageLabels;

    protected static ?string $slug = 'development-status';

    protected static string|\UnitEnum|null $navigationGroup = 'V10 Admin Command';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartLine;

    protected static ?string $navigationLabel = 'Development status';

    protected static ?int $navigationSort = 0;

    protected Width|string|null $maxContentWidth = Width::Full;

    protected string $view = 'filament.pages.development-status';

    public static function getNavigationLabel(): string
    {
        return static::copy('لوحة حالة التطوير', 'Development status');
    }

    public function getTitle(): string|Htmlable
    {
        return static::copy('لوحة حالة التطوير', 'Development status');
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'dashboard' => RootDashboardData::make(),
        ];
    }

    private static function copy(string $arabic, string $english): string
    {
        return app()->getLocale() === 'ar' ? $arabic : $english;
    }
}
