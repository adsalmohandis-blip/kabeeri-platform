<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Organization;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
{
    protected $model = Contact::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        return [
            'organization_id' => Organization::factory(),
            'company_id' => null,
            'site_id' => null,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'display_name' => "{$firstName} {$lastName}",
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'mobile' => fake()->optional()->phoneNumber(),
            'job_title' => fake()->optional()->jobTitle(),
            'contact_type' => 'customer',
            'status' => 'active',
            'source' => 'manual',
            'owner_user_id' => fake()->boolean(30) ? User::factory() : null,
            'last_contacted_at' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }

    public function forCompany(?Company $company = null): self
    {
        return $this->state(function () use ($company): array {
            $company ??= Company::factory()->create();

            return [
                'organization_id' => $company->organization_id,
                'company_id' => $company->id,
            ];
        });
    }

    public function forSite(?Site $site = null): self
    {
        return $this->state(function () use ($site): array {
            $site ??= Site::factory()->create();

            return [
                'organization_id' => $site->organization_id,
                'company_id' => $site->company_id,
                'site_id' => $site->id,
            ];
        });
    }
}
