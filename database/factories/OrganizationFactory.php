<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Organization>
 */
class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1000, 9999),
            'owner_user_id' => User::factory(),
            'account_type' => fake()->randomElement([
                'individual',
                'business',
                'agency',
                'enterprise',
                'legal_partner',
                'platform_internal',
            ]),
            'status' => 'active',
            'plan_code' => fake()->optional()->randomElement(['starter', 'pro', 'enterprise']),
            'country_id' => fake()->boolean(70) ? Country::factory() : null,
            'locale' => 'ar',
            'timezone' => 'Africa/Cairo',
            'settings' => ['theme' => 'default'],
            'metadata' => ['source' => 'factory'],
        ];
    }
}
