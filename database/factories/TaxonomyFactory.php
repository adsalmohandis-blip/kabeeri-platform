<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Site;
use App\Models\Taxonomy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Taxonomy>
 */
class TaxonomyFactory extends Factory
{
    protected $model = Taxonomy::class;

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
            'site_id' => fake()->boolean(70)
                ? Site::factory()->state(fn (array $attributes): array => [
                    'organization_id' => $attributes['organization_id'],
                ])
                : null,
            'name' => Str::title($name),
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1000, 9999),
            'type' => fake()->randomElement(['category', 'tag']),
        ];
    }
}
