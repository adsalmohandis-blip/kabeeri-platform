<?php

namespace Database\Factories;

use App\Models\Package;
use App\Models\PartnerCatalogShare;
use App\Models\PartnerStorefront;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PartnerCatalogShare>
 */
class PartnerCatalogShareFactory extends Factory
{
    protected $model = PartnerCatalogShare::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $storefront = PartnerStorefront::factory()->create();
        $package = Package::factory()->create();

        return [
            'organization_id' => $storefront->organization_id,
            'partner_storefront_id' => $storefront->id,
            'catalogable_type' => $package::class,
            'catalogable_id' => $package->id,
            'share_type' => 'catalog_item',
            'status' => 'draft',
            'visibility' => 'private',
            'sort_order' => 0,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
