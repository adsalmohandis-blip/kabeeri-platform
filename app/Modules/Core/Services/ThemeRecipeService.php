<?php

namespace App\Modules\Core\Services;

use App\Models\Theme;
use App\Models\ThemeAppRecipe;

class ThemeRecipeService
{
    public function findRecipe(Theme $theme, string $projectType, string $appType): ?ThemeAppRecipe
    {
        return ThemeAppRecipe::query()
            ->where('theme_id', $theme->id)
            ->where('project_type', $projectType)
            ->where('app_type', $appType)
            ->first();
    }

    /**
     * @return array<string, mixed>
     */
    public function previewApply(ThemeAppRecipe $recipe): array
    {
        return [
            'theme_id' => $recipe->theme_id,
            'project_type' => $recipe->project_type,
            'app_type' => $recipe->app_type,
            'packages' => [
                'required' => $recipe->required_packages ?? [],
                'recommended' => $recipe->recommended_packages ?? [],
                'optional' => $recipe->optional_packages ?? [],
            ],
            'demo_content_ref' => $recipe->demo_content_ref,
            'setup_steps' => $recipe->setup_steps ?? [],
            'requires_approval' => true,
        ];
    }
}
