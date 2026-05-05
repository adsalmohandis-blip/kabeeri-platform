<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\SalesPipeline;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SalesPipeline>
 */
class SalesPipelineFactory extends Factory
{
    protected $model = SalesPipeline::class;

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
            'name' => $name,
            'slug' => Str::slug($name.'-'.fake()->unique()->numberBetween(100, 999)),
            'status' => 'active',
            'is_default' => false,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
