<?php

namespace Database\Factories;

use App\Models\MallMirrorProduct;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MallMirrorProduct> */
class MallMirrorProductFactory extends Factory
{
    protected $model = MallMirrorProduct::class;

    public function definition(): array
    {
        $productName = fake()->words(3, true);

        return [
            'organization_id' => Organization::factory(),
            'company_id' => null,
            'site_id' => null,
            'product_id' => null,
            'mall_publication_consent_id' => null,
            'product_name' => $productName,
            'slug' => str()->slug($productName),
            'description' => fake()->optional()->text(200),
            'price' => fake()->randomFloat(2, 10, 1000),
            'currency' => 'USD',
            'images' => ['image_url' => fake()->imageUrl()],
            'attributes' => ['color' => fake()->colorName(), 'size' => fake()->randomElement(['S', 'M', 'L', 'XL'])],
            'mirror_status' => 'draft',
            'published_at' => null,
            'last_refreshed_at' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
