<?php

namespace App\Filament\Pages\Concerns;

use App\Support\Ui\V10AdminExperience;

trait HasV10AdminPageData
{
    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'data' => V10AdminExperience::pageData(static::$pageKey),
        ];
    }
}
