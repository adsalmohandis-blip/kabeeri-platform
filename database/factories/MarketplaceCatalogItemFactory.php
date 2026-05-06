<?php

namespace Database\Factories;

use App\Models\MarketplaceCatalogItem;
use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MarketplaceCatalogItem>
 */
class MarketplaceCatalogItemFactory extends Factory
{
    protected $model = MarketplaceCatalogItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $package = Package::factory()->create();

        return [
            'catalogable_type' => $package::class,
            'catalogable_id' => $package->id,
            'item_kind' => 'package',
            'listing_status' => 'draft',
            'visibility' => 'internal',
            'governance_status' => 'pending',
            'is_featured' => false,
            'sort_order' => 0,
            'submitted_by_user_id' => null,
            'approved_by_user_id' => null,
            'approved_at' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
