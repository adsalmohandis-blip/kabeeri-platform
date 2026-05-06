<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\LegalPartnerProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<LegalPartnerProfile>
 */
class LegalPartnerProfileFactory extends Factory
{
    protected $model = LegalPartnerProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $company = Company::factory()->create();
        $name = fake()->company().' Legal';

        return [
            'organization_id' => $company->organization_id,
            'company_id' => $company->id,
            'user_id' => null,
            'display_name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1000, 9999),
            'partner_type' => 'legal_consultant',
            'practice_areas' => ['contracts', 'company_formation'],
            'jurisdictions' => ['EG'],
            'languages' => ['ar', 'en'],
            'contact_channels' => ['email' => fake()->safeEmail()],
            'verification_status' => 'not_submitted',
            'network_status' => 'draft',
            'verified_at' => null,
            'activated_at' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
