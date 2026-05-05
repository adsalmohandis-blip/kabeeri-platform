<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Country;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tradeName = fake()->unique()->company();

        return [
            'organization_id' => Organization::factory(),
            'legal_name' => fake()->optional()->company().' LLC',
            'trade_name' => $tradeName,
            'slug' => Str::slug($tradeName).'-'.fake()->unique()->numberBetween(1000, 9999),
            'country_id' => fake()->boolean(70) ? Country::factory() : null,
            'city' => fake()->optional()->city(),
            'legal_type' => fake()->optional()->randomElement(['llc', 'corp', 'establishment']),
            'registration_number' => fake()->optional()->regexify('[A-Z0-9]{8,12}'),
            'tax_number' => fake()->optional()->regexify('[0-9]{10,15}'),
            'industry' => fake()->optional()->randomElement(['healthcare', 'technology', 'retail']),
            'company_size' => fake()->optional()->randomElement(['1-10', '11-50', '51-200']),
            'status' => 'draft',
            'verification_status' => 'not_submitted',
            'verified_at' => null,
            'verification_expires_at' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
