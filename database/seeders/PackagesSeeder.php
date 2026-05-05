<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackagesSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'key' => 'kabeeri.forms',
                'name' => 'Kabeeri Forms',
                'slug' => 'kabeeri-forms',
                'package_type' => 'module',
                'category' => 'forms',
                'permissions' => ['form.view', 'form.create'],
            ],
            [
                'key' => 'kabeeri.commerce-lite',
                'name' => 'Kabeeri Commerce Lite',
                'slug' => 'kabeeri-commerce-lite',
                'package_type' => 'module',
                'category' => 'commerce',
                'permissions' => ['product.view', 'product.create'],
            ],
        ];

        foreach ($packages as $package) {
            Package::query()->updateOrCreate(
                ['key' => $package['key']],
                $package + [
                    'publisher_type' => 'official',
                    'status' => 'active',
                    'short_description' => null,
                    'description' => null,
                    'dependencies' => [],
                    'compatibility' => ['v2' => true],
                    'metadata' => ['seeded' => true],
                ],
            );
        }
    }
}
