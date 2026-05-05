<?php

namespace Tests\Feature;

use App\Models\Theme;
use App\Models\ThemeAppRecipe;
use App\Modules\Core\Services\ThemeRecipeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemeAppRecipesTest extends TestCase
{
    use RefreshDatabase;

    public function test_theme_can_have_recipe(): void
    {
        $theme = Theme::query()->create([
            'name' => 'Recipe Theme',
            'slug' => 'recipe-theme',
            'status' => 'active',
            'type' => 'official',
        ]);

        $recipe = ThemeAppRecipe::factory()->create([
            'theme_id' => $theme->id,
            'project_type' => 'business',
            'app_type' => 'website',
        ]);

        $this->assertTrue($theme->appRecipes()->whereKey($recipe->id)->exists());
        $this->assertSame($recipe->id, app(ThemeRecipeService::class)->findRecipe($theme, 'business', 'website')?->id);
    }

    public function test_recipe_can_be_applied_as_preview_plan(): void
    {
        $recipe = ThemeAppRecipe::factory()->create([
            'required_packages' => ['cms'],
            'recommended_packages' => ['forms'],
            'optional_packages' => ['commerce-lite'],
            'setup_steps' => ['Create demo pages'],
        ]);

        $plan = app(ThemeRecipeService::class)->previewApply($recipe);

        $this->assertSame(['cms'], $plan['packages']['required']);
        $this->assertSame(['forms'], $plan['packages']['recommended']);
        $this->assertSame(['commerce-lite'], $plan['packages']['optional']);
        $this->assertTrue($plan['requires_approval']);
    }
}
