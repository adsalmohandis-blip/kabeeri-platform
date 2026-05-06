<?php

namespace Database\Factories;

use App\Models\MallMirrorService;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MallMirrorService> */
class MallMirrorServiceFactory extends Factory
{
    protected $model = MallMirrorService::class;

    public function definition(): array
    {
        $serviceName = fake()->words(3, true);
        $categories = ['Consulting', 'Design', 'Development', 'Training', 'Support', 'Maintenance'];

        return [
            'organization_id' => Organization::factory(),
            'company_id' => null,
            'site_id' => null,
            'service_id' => null,
            'mall_publication_consent_id' => null,
            'service_name' => $serviceName,
            'slug' => str()->slug($serviceName),
            'description' => fake()->optional()->text(200),
            'service_category' => fake()->randomElement($categories),
            'hourly_rate' => fake()->randomFloat(2, 30, 500),
            'currency' => 'USD',
            'availability_info' => [
                'hours_per_week' => fake()->numberBetween(10, 40),
                'booking_method' => fake()->randomElement(['request', 'booking', 'calendar']),
                'response_time' => fake()->randomElement(['24 hours', '48 hours', '1 week']),
            ],
            'images' => ['image_url' => fake()->imageUrl()],
            'mirror_status' => 'draft',
            'published_at' => null,
            'last_refreshed_at' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
