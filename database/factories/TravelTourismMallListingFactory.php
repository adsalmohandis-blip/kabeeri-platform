<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\TravelTourismMallListing;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<TravelTourismMallListing> */
class TravelTourismMallListingFactory extends Factory
{
    protected $model = TravelTourismMallListing::class;

    public function definition(): array
    {
        $title = fake()->words(4, true);

        return [
            'organization_id' => Organization::factory(),
            'company_id' => null,
            'site_id' => null,
            'mall_publication_consent_id' => null,
            'listing_type' => fake()->randomElement(['tour', 'hotel', 'destination', 'package']),
            'title' => $title,
            'slug' => str()->slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'description' => fake()->optional()->paragraph(),
            'destination' => fake()->city(),
            'country_code' => 'EGY',
            'price_from' => fake()->randomFloat(2, 50, 5000),
            'currency' => 'USD',
            'availability' => ['season' => 'year_round'],
            'contact_channels' => ['email' => fake()->safeEmail()],
            'images' => ['image_url' => fake()->imageUrl()],
            'listing_status' => 'draft',
            'published_at' => null,
            'last_refreshed_at' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
