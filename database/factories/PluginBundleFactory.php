<?php

namespace Database\Factories;

use App\Models\PluginBundle;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PluginBundle>
 */
class PluginBundleFactory extends Factory
{
    protected $model = PluginBundle::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1000, 9999),
            'bundle_type' => 'starter',
            'description' => fake()->sentence(),
            'status' => 'active',
            'metadata' => ['source' => 'factory'],
        ];
    }
}
