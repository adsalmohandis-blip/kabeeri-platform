<?php

namespace Database\Factories;

use App\Models\AgencyPartnerProfile;
use App\Models\PartnerStorefront;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PartnerStorefront>
 */
class PartnerStorefrontFactory extends Factory
{
    protected $model = PartnerStorefront::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $agency = AgencyPartnerProfile::factory()->create();
        $name = $agency->display_name.' Storefront';

        return [
            'organization_id' => $agency->organization_id,
            'agency_partner_profile_id' => $agency->id,
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1000, 9999),
            'storefront_type' => 'partner_catalog',
            'status' => 'draft',
            'visibility' => 'private',
            'settings' => ['mode' => 'draft'],
            'metadata' => ['source' => 'factory'],
        ];
    }
}
