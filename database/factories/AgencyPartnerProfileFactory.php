<?php

namespace Database\Factories;

use App\Models\AgencyPartnerProfile;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<AgencyPartnerProfile>
 */
class AgencyPartnerProfileFactory extends Factory
{
    protected $model = AgencyPartnerProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $company = Company::factory()->create();
        $name = $company->trade_name.' Agency';

        return [
            'organization_id' => $company->organization_id,
            'company_id' => $company->id,
            'user_id' => null,
            'display_name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1000, 9999),
            'agency_type' => 'implementation_partner',
            'status' => 'draft',
            'accreditation_status' => 'not_submitted',
            'accreditation_level' => null,
            'service_categories' => ['implementation', 'migration'],
            'regions' => ['EG'],
            'languages' => ['ar', 'en'],
            'submitted_at' => null,
            'accredited_at' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
