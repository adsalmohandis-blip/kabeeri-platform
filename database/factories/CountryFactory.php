<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Country>
 */
class CountryFactory extends Factory
{
    protected $model = Country::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->country(),
            'iso2' => strtoupper(fake()->unique()->lexify('??')),
            'iso3' => strtoupper(fake()->unique()->lexify('???')),
            'phone_code' => (string) fake()->numberBetween(1, 999),
            'status' => 'active',
        ];
    }
}
