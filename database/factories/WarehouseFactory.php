<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Warehouse>
 */
class WarehouseFactory extends Factory
{
    protected $model = Warehouse::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'organization_id' => Organization::factory(),
            'company_id' => null,
            'site_id' => null,
            'name' => $name,
            'slug' => Str::slug($name.'-'.fake()->unique()->numberBetween(100, 999)),
            'code' => 'WH-'.fake()->unique()->numberBetween(1000, 9999),
            'status' => 'active',
            'is_default' => false,
            'address' => fake()->optional()->streetAddress(),
            'city' => fake()->optional()->city(),
            'country_code' => 'EG',
            'metadata' => ['source' => 'factory'],
        ];
    }
}
