<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\OrganizationOperatingMode;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<OrganizationOperatingMode>
 */
class OrganizationOperatingModeFactory extends Factory
{
    protected $model = OrganizationOperatingMode::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $key = fake()->randomElement(['standard', 'commerce', 'marketplace', 'agency', 'partner'])
            .'-'.fake()->unique()->numberBetween(100, 999);

        return [
            'organization_id' => Organization::factory(),
            'mode_key' => Str::slug($key),
            'mode_label' => str($key)->replace('-', ' ')->title()->toString(),
            'status' => 'draft',
            'is_active' => false,
            'activated_at' => null,
            'deactivated_at' => null,
            'settings' => ['source' => 'factory'],
            'metadata' => ['source' => 'factory'],
        ];
    }

    public function active(): self
    {
        return $this->state(fn (): array => [
            'status' => 'active',
            'is_active' => true,
            'activated_at' => now(),
            'deactivated_at' => null,
        ]);
    }
}
