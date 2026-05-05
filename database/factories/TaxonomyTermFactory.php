<?php

namespace Database\Factories;

use App\Models\Taxonomy;
use App\Models\TaxonomyTerm;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TaxonomyTerm>
 */
class TaxonomyTermFactory extends Factory
{
    protected $model = TaxonomyTerm::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'taxonomy_id' => Taxonomy::factory(),
            'parent_id' => null,
            'name' => Str::title($name),
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1000, 9999),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
