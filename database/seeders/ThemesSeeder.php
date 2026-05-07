<?php

namespace Database\Seeders;

use App\Models\Theme;
use Illuminate\Database\Seeder;

class ThemesSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Theme::query()->updateOrCreate(
            ['slug' => 'kabeeri-starter'],
            [
                'name' => 'kabeeri Starter Theme',
                'version' => '1.0.0',
                'publisher' => 'kabeeri',
                'status' => 'active',
                'type' => 'official',
                'category' => 'starter',
                'industries' => ['general', 'services'],
                'app_types' => ['website', 'business_profile'],
                'price_type' => 'free',
                'demo_url' => 'https://demo.kabeeri.local/starter',
                'preview_images' => [],
                'performance_score' => 90,
                'compatibility' => ['v2' => true],
                'supports_rtl' => true,
                'supports_dark_mode' => false,
                'manifest' => [
                    'layouts' => ['default'],
                    'supports' => ['rtl'],
                ],
                'metadata' => [
                    'seeded' => true,
                ],
            ],
        );

        Theme::query()->updateOrCreate(
            ['slug' => 'kabeeri-commerce-lite'],
            [
                'name' => 'kabeeri Commerce Lite',
                'version' => '1.0.0',
                'publisher' => 'kabeeri',
                'status' => 'active',
                'type' => 'official',
                'category' => 'commerce',
                'industries' => ['retail', 'services'],
                'app_types' => ['store', 'catalog'],
                'price_type' => 'free',
                'demo_url' => 'https://demo.kabeeri.local/commerce-lite',
                'preview_images' => [],
                'performance_score' => 86,
                'compatibility' => ['v2' => true, 'commerce_lite' => true],
                'supports_rtl' => true,
                'supports_dark_mode' => false,
                'manifest' => [
                    'layouts' => ['default', 'product'],
                    'supports' => ['rtl', 'commerce_lite'],
                ],
                'metadata' => [
                    'seeded' => true,
                ],
            ],
        );
    }
}
