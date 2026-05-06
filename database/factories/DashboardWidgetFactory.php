<?php

namespace Database\Factories;

use App\Models\DashboardWidget;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DashboardWidget>
 */
class DashboardWidgetFactory extends Factory
{
    protected $model = DashboardWidget::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'report_definition_id' => null,
            'key' => 'widget_'.fake()->unique()->numberBetween(1000, 9999),
            'title' => fake()->words(3, true),
            'widget_type' => 'metric',
            'status' => 'active',
            'sort_order' => 0,
            'settings' => ['size' => 'sm'],
            'metadata' => ['source' => 'factory'],
        ];
    }
}
