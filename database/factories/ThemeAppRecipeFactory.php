<?php

namespace Database\Factories;

use App\Models\Theme;
use App\Models\ThemeAppRecipe;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ThemeAppRecipe>
 */
class ThemeAppRecipeFactory extends Factory
{
    protected $model = ThemeAppRecipe::class;

    public function definition(): array
    {
        return [
            'theme_id' => Theme::query()->first()?->id ?? Theme::query()->create([
                'name' => 'Factory Theme',
                'slug' => 'factory-theme-'.fake()->unique()->numberBetween(1000, 9999),
                'status' => 'active',
                'type' => 'official',
            ])->id,
            'project_type' => 'business',
            'app_type' => 'website',
            'required_packages' => ['cms'],
            'recommended_packages' => ['forms'],
            'optional_packages' => ['commerce-lite'],
            'demo_content_ref' => null,
            'setup_steps' => ['Create pages', 'Configure navigation'],
            'metadata' => ['source' => 'factory'],
        ];
    }
}
