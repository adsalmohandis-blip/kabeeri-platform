<?php

namespace Database\Factories;

use App\Models\Supplier;
use App\Models\SupplierContact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupplierContact>
 */
class SupplierContactFactory extends Factory
{
    protected $model = SupplierContact::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'name' => fake()->name(),
            'email' => fake()->optional()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'job_title' => fake()->optional()->jobTitle(),
            'is_primary' => false,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
