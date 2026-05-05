<?php

namespace Database\Factories;

use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Package>
 */
class PackageFactory extends Factory
{
    protected $model = Package::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);
        $key = 'kabeeri.'.Str::slug($name, '.');

        return [
            'key' => $key,
            'name' => Str::title($name),
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1000, 9999),
            'package_type' => 'module',
            'publisher_type' => 'official',
            'status' => 'active',
            'short_description' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'category' => fake()->randomElement(['content', 'forms', 'commerce']),
            'permissions' => [],
            'dependencies' => [],
            'compatibility' => ['v2' => true],
            'metadata' => ['source' => 'factory'],
        ];
    }
}
