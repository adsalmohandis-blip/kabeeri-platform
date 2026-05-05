<?php

namespace Database\Factories;

use App\Models\Package;
use App\Models\PluginBundle;
use App\Models\PluginBundleItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PluginBundleItem>
 */
class PluginBundleItemFactory extends Factory
{
    protected $model = PluginBundleItem::class;

    public function definition(): array
    {
        return [
            'plugin_bundle_id' => PluginBundle::factory(),
            'package_id' => Package::factory(),
            'requirement_level' => fake()->randomElement(['required', 'recommended', 'optional']),
            'sort_order' => fake()->numberBetween(0, 20),
            'settings' => ['source' => 'factory'],
        ];
    }
}
