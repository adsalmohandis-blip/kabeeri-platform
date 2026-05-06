<?php

namespace App\Modules\Core\Services;

use App\Models\MarketplaceCatalogItem;
use App\Models\Theme;
use Illuminate\Database\Eloquent\Collection;

class ThemeStoreCatalogService
{
    /**
     * @param  array{
     *     industry?: string,
     *     app_type?: string,
     *     category?: string,
     *     price_type?: string,
     *     min_performance?: int,
     *     featured?: bool
     * }  $filters
     * @return Collection<int, MarketplaceCatalogItem>
     */
    public function list(array $filters = []): Collection
    {
        $query = MarketplaceCatalogItem::query()
            ->with('catalogable')
            ->where('item_kind', 'theme')
            ->where('listing_status', 'active')
            ->where('governance_status', 'approved')
            ->where('visibility', 'internal')
            ->whereHasMorph('catalogable', [Theme::class], function ($themeQuery) use ($filters): void {
                $themeQuery
                    ->where('status', 'active')
                    ->where('type', 'official');

                if (isset($filters['industry'])) {
                    $themeQuery->whereJsonContains('industries', $filters['industry']);
                }

                if (isset($filters['app_type'])) {
                    $themeQuery->whereJsonContains('app_types', $filters['app_type']);
                }

                if (isset($filters['category'])) {
                    $themeQuery->where('category', $filters['category']);
                }

                if (isset($filters['price_type'])) {
                    $themeQuery->where('price_type', $filters['price_type']);
                }

                if (isset($filters['min_performance'])) {
                    $themeQuery->where('performance_score', '>=', $filters['min_performance']);
                }
            })
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('id');

        if (isset($filters['featured'])) {
            $query->where('is_featured', $filters['featured']);
        }

        return $query->get();
    }
}
