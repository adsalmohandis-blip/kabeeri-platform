<?php

namespace Database\Factories;

use App\Models\BackofficeWorkspace;
use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<BackofficeWorkspace>
 */
class BackofficeWorkspaceFactory extends Factory
{
    protected $model = BackofficeWorkspace::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $site = Site::factory()->create();
        $name = fake()->company().' Backoffice';

        return [
            'organization_id' => $site->organization_id,
            'company_id' => $site->company_id,
            'site_id' => $site->id,
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1000, 9999),
            'workspace_type' => 'operations',
            'access_scope' => 'organization',
            'status' => 'draft',
            'entry_route' => null,
            'enabled_modules' => ['crm', 'reports'],
            'navigation' => [['label' => 'Overview', 'route' => 'dashboard']],
            'settings' => ['mode' => 'record_only'],
            'metadata' => ['source' => 'factory'],
        ];
    }
}
