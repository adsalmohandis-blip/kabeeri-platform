<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'organization_id' => Organization::factory(),
            'company_id' => null,
            'name' => $name,
            'slug' => Str::slug($name.'-'.fake()->unique()->numberBetween(100, 999)),
            'supplier_code' => 'SUP-'.fake()->unique()->numberBetween(1000, 9999),
            'email' => fake()->optional()->companyEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'website' => fake()->optional()->url(),
            'status' => 'active',
            'city' => fake()->optional()->city(),
            'country_code' => 'EG',
            'address' => fake()->optional()->address(),
            'metadata' => ['source' => 'factory'],
        ];
    }
}
