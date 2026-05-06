<?php

namespace Database\Factories;

use App\Models\MallMirrorTalent;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MallMirrorTalent> */
class MallMirrorTalentFactory extends Factory
{
    protected $model = MallMirrorTalent::class;

    public function definition(): array
    {
        $name = fake()->name();

        return [
            'organization_id' => Organization::factory(),
            'company_id' => null,
            'site_id' => null,
            'employee_profile_id' => null,
            'mall_publication_consent_id' => null,
            'display_name' => $name,
            'slug' => str()->slug($name).'-'.fake()->unique()->numberBetween(1000, 9999),
            'headline' => fake()->jobTitle(),
            'bio' => fake()->optional()->paragraph(),
            'skills' => fake()->randomElements(['design', 'development', 'sales', 'support', 'training'], 3),
            'availability' => ['status' => 'available'],
            'location_label' => fake()->city(),
            'mirror_status' => 'draft',
            'published_at' => null,
            'last_refreshed_at' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
