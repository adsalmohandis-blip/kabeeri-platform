<?php

namespace Database\Factories;

use App\Models\BusinessProfile;
use App\Models\Company;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<BusinessProfile>
 */
class BusinessProfileFactory extends Factory
{
    protected $model = BusinessProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $displayName = fake()->unique()->company();

        return [
            'organization_id' => Organization::factory(),
            'company_id' => Company::factory()->state(fn (array $attributes): array => [
                'organization_id' => $attributes['organization_id'],
            ]),
            'site_id' => null,
            'display_name' => $displayName,
            'slug' => Str::slug($displayName).'-'.fake()->unique()->numberBetween(1000, 9999),
            'description' => fake()->optional()->sentence(),
            'public_email' => fake()->optional()->safeEmail(),
            'public_phone' => fake()->optional()->phoneNumber(),
            'website_url' => fake()->optional()->url(),
            'logo_media_id' => null,
            'cover_media_id' => null,
            'visibility' => 'draft',
            'status' => 'draft',
            'metadata' => ['source' => 'factory'],
        ];
    }
}
