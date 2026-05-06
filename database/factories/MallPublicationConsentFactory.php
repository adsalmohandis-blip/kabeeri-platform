<?php

namespace Database\Factories;

use App\Models\MallPublicationConsent;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MallPublicationConsent>
 */
class MallPublicationConsentFactory extends Factory
{
    protected $model = MallPublicationConsent::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'company_id' => null,
            'site_id' => null,
            'subject_type' => null,
            'subject_id' => null,
            'consent_type' => 'mall_publication',
            'status' => 'pending',
            'channels' => ['business_directory'],
            'granted_by' => null,
            'granted_at' => null,
            'revoked_at' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
