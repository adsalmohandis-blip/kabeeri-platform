<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\HasV10AdminPageData;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class TaskTrackerStatus extends Page
{
    use HasV10AdminPageData;

    protected static string $pageKey = 'task_tracker';

    protected static ?string $slug = 'task-tracker-status';

    protected static ?string $title = 'Task Tracker Status';

    protected static ?string $navigationLabel = 'Task Tracker';

    protected static string|\UnitEnum|null $navigationGroup = 'V10 Admin Command';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentCheck;

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.v10-admin-page';
}
