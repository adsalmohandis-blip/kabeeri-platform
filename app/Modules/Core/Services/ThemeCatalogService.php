<?php

namespace App\Modules\Core\Services;

use App\Models\Theme;
use Illuminate\Database\Eloquent\Collection;

class ThemeCatalogService
{
    /**
     * @param  array{industry?: string, app_type?: string, price_type?: string}  $filters
     * @return Collection<int, Theme>
     */
    public function list(array $filters = []): Collection
    {
        $query = Theme::query()
            ->where('type', 'official')
            ->where('status', 'active')
            ->orderByDesc('performance_score')
            ->orderBy('name');

        if (isset($filters['industry'])) {
            $query->whereJsonContains('industries', $filters['industry']);
        }

        if (isset($filters['app_type'])) {
            $query->whereJsonContains('app_types', $filters['app_type']);
        }

        if (isset($filters['price_type'])) {
            $query->where('price_type', $filters['price_type']);
        }

        return $query->get();
    }
}
