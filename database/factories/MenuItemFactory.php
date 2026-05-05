<?php

namespace Database\Factories;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MenuItem>
 */
class MenuItemFactory extends Factory
{
    protected $model = MenuItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'menu_id' => Menu::factory(),
            'parent_id' => null,
            'label' => fake()->words(2, true),
            'url' => '/'.fake()->slug(),
            'link_type' => 'custom',
            'linked_type' => null,
            'linked_id' => null,
            'target' => 'self',
            'sort_order' => fake()->numberBetween(0, 20),
            'status' => 'active',
            'metadata' => ['source' => 'factory'],
        ];
    }
}
