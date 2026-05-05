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
                'name' => 'Kabeeri Starter Theme',
                'version' => '1.0.0',
                'publisher' => 'Kabeeri',
                'status' => 'active',
                'type' => 'official',
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
    }
}
