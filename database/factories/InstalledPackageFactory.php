<?php

namespace Database\Factories;

use App\Models\InstalledPackage;
use App\Models\Organization;
use App\Models\Package;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstalledPackage>
 */
class InstalledPackageFactory extends Factory
{
    protected $model = InstalledPackage::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'site_id' => Site::factory()->state(fn (array $attributes): array => [
                'organization_id' => $attributes['organization_id'],
            ]),
            'package_id' => Package::factory(),
            'status' => 'active',
            'installed_by' => User::factory(),
            'installed_at' => now(),
            'settings' => ['source' => 'factory'],
        ];
    }
}
