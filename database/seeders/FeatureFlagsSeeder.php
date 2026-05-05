<?php

namespace Database\Seeders;

use App\Models\FeatureFlag;
use Illuminate\Database\Seeder;

class FeatureFlagsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $flags = [
            'enable_cms',
            'enable_media_library',
            'enable_theme_foundation',
            'enable_package_foundation',
            'enable_rabet_foundation',
            'enable_onboarding_basic',
            'enable_activity_logs',
        ];

        foreach ($flags as $key) {
            FeatureFlag::query()->updateOrCreate(
                ['key' => $key],
                [
                    'description' => null,
                    'default_value' => false,
                    'status' => 'active',
                ],
            );
        }
    }
}
