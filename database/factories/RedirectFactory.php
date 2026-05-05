<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Redirect;
use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Redirect>
 */
class RedirectFactory extends Factory
{
    protected $model = Redirect::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'site_id' => Site::factory()->state(fn (array $attributes): array => [
                'organization_id' => $attributes['organization_id'],
            ]),
            'source_path' => '/'.fake()->unique()->slug(),
            'target_url' => '/'.fake()->unique()->slug(),
            'status_code' => 301,
            'source_type' => null,
            'source_id' => null,
            'hit_count' => 0,
            'last_hit_at' => null,
            'status' => 'active',
            'metadata' => ['source' => 'factory'],
        ];
    }
}
