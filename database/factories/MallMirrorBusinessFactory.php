<?php

namespace Database\Factories;

use App\Models\BusinessProfile;
use App\Models\MallMirrorBusiness;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MallMirrorBusiness>
 */
class MallMirrorBusinessFactory extends Factory
{
    protected $model = MallMirrorBusiness::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $profile = BusinessProfile::factory()->create();

        return [
            'organization_id' => $profile->organization_id,
            'company_id' => $profile->company_id,
            'site_id' => $profile->site_id,
            'business_profile_id' => $profile->id,
            'mall_publication_consent_id' => null,
            'display_name' => $profile->display_name,
            'slug' => $profile->slug,
            'description' => $profile->description,
            'public_contacts' => [
                'email' => $profile->public_email,
                'phone' => $profile->public_phone,
                'website_url' => $profile->website_url,
            ],
            'mirror_status' => 'draft',
            'published_at' => null,
            'last_refreshed_at' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
