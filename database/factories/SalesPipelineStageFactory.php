<?php

namespace Database\Factories;

use App\Models\SalesPipeline;
use App\Models\SalesPipelineStage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SalesPipelineStage>
 */
class SalesPipelineStageFactory extends Factory
{
    protected $model = SalesPipelineStage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'sales_pipeline_id' => SalesPipeline::factory(),
            'name' => $name,
            'slug' => Str::slug($name.'-'.fake()->unique()->numberBetween(100, 999)),
            'sort_order' => fake()->numberBetween(0, 100),
            'probability' => fake()->numberBetween(0, 100),
            'is_won' => false,
            'is_lost' => false,
            'status' => 'active',
            'metadata' => ['source' => 'factory'],
        ];
    }
}
