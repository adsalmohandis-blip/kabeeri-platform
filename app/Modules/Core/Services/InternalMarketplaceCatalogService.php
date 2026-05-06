<?php

namespace App\Modules\Core\Services;

use App\Models\MarketplaceCatalogItem;
use App\Models\Package;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class InternalMarketplaceCatalogService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function register(Model $catalogable, array $attributes = []): MarketplaceCatalogItem
    {
        $itemKind = $this->itemKind($catalogable);

        return MarketplaceCatalogItem::query()->updateOrCreate(
            [
                'catalogable_type' => $catalogable->getMorphClass(),
                'catalogable_id' => $catalogable->getKey(),
            ],
            [
                ...$attributes,
                'item_kind' => $itemKind,
                'listing_status' => $attributes['listing_status'] ?? 'draft',
                'visibility' => $attributes['visibility'] ?? 'internal',
                'governance_status' => $attributes['governance_status'] ?? 'pending',
            ],
        );
    }

    public function approve(MarketplaceCatalogItem $item, User $approver): MarketplaceCatalogItem
    {
        $item->forceFill([
            'listing_status' => 'active',
            'governance_status' => 'approved',
            'approved_by_user_id' => $approver->id,
            'approved_at' => now(),
        ])->save();

        return $item->refresh();
    }

    /**
     * @param  array{item_kind?: string, featured?: bool, visibility?: string}  $filters
     * @return Collection<int, MarketplaceCatalogItem>
     */
    public function list(array $filters = []): Collection
    {
        $query = MarketplaceCatalogItem::query()
            ->where('listing_status', 'active')
            ->where('governance_status', 'approved')
            ->where('visibility', $filters['visibility'] ?? 'internal')
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('id');

        if (isset($filters['item_kind'])) {
            $query->where('item_kind', $filters['item_kind']);
        }

        if (isset($filters['featured'])) {
            $query->where('is_featured', $filters['featured']);
        }

        return $query->get();
    }

    private function itemKind(Model $catalogable): string
    {
        return match ($catalogable::class) {
            Package::class => 'package',
            Theme::class => 'theme',
            default => throw ValidationException::withMessages([
                'catalogable' => 'Only official packages and themes can be registered in the internal marketplace catalog.',
            ]),
        };
    }
}
