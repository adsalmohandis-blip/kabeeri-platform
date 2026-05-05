<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Currency>
 */
class CurrencyFactory extends Factory
{
    protected $model = Currency::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->currencyCode().' Currency',
            'code' => strtoupper(fake()->unique()->lexify('???')),
            'symbol' => fake()->randomElement(['$', '€', '£', '¥']),
            'decimals' => 2,
            'status' => 'active',
        ];
    }
}
