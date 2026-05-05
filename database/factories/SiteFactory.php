<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Organization;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Site>
 */
class SiteFactory extends Factory
{
    protected $model = Site::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);
        $slug = Str::slug($name).'-'.fake()->unique()->numberBetween(1000, 9999);

        return [
            'organization_id' => Organization::factory(),
            'company_id' => null,
            'name' => Str::title($name),
            'slug' => $slug,
            'domain' => fake()->optional(30)->domainName(),
            'subdomain' => fake()->optional()->lexify('app-????'),
            'site_type' => fake()->randomElement([
                'website',
                'blog',
                'store',
                'portal',
                'landing',
                'internal_portal',
                'mall_seller_site',
            ]),
            'status' => 'active',
            'language' => 'ar',
            'timezone' => 'Africa/Cairo',
            'theme_id' => null,
            'settings' => ['app_label' => 'App'],
            'metadata' => ['source' => 'factory'],
            'created_by' => fake()->boolean(70) ? User::factory() : null,
        ];
    }

    public function forCompany(Company $company): static
    {
        return $this->state(fn (): array => [
            'organization_id' => $company->organization_id,
            'company_id' => $company->id,
        ]);
    }
}
