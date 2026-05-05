<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\ContentEntry;
use App\Models\ContentType;
use App\Models\Organization;
use App\Models\Site;
use App\Models\Theme;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class V1DemoSeedDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_v1_demo_foundation_data(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@kabeeri.local')->first();
        $organization = Organization::query()->where('slug', 'kabeeri-demo-org')->first();
        $company = Company::query()->where('slug', 'kabeeri-demo-company')->first();
        $site = Site::query()->where('slug', 'kabeeri-demo-app')->first();
        $theme = Theme::query()->where('slug', 'kabeeri-starter')->first();

        $this->assertNotNull($admin);
        $this->assertTrue(Hash::check('password', (string) $admin?->password));
        $this->assertNotNull($organization);
        $this->assertNotNull($company);
        $this->assertNotNull($site);
        $this->assertNotNull($theme);
        $this->assertSame($theme?->id, $site?->theme_id);

        $this->assertDatabaseHas('organization_memberships', [
            'organization_id' => $organization?->id,
            'user_id' => $admin?->id,
            'membership_type' => 'owner',
            'status' => 'active',
        ]);

        $this->assertTrue(
            ContentType::query()
                ->where('organization_id', $organization?->id)
                ->where('site_id', $site?->id)
                ->whereIn('slug', ['page', 'post'])
                ->count() === 2,
        );

        $this->assertSame(
            4,
            ContentEntry::query()
                ->where('site_id', $site?->id)
                ->whereIn('slug', ['home', 'about', 'services', 'contact'])
                ->where('status', 'published')
                ->count(),
        );

        $this->assertDatabaseHas('media_assets', [
            'organization_id' => $organization?->id,
            'relative_path' => 'demo/media/office-placeholder.jpg',
        ]);

        $this->assertDatabaseHas('business_profiles', [
            'organization_id' => $organization?->id,
            'company_id' => $company?->id,
            'slug' => 'kabeeri-demo-business',
            'status' => 'draft',
            'visibility' => 'draft',
        ]);
    }

    public function test_seeded_home_page_renders_publicly(): void
    {
        $this->seed(DatabaseSeeder::class);

        $site = Site::query()->where('slug', 'kabeeri-demo-app')->firstOrFail();
        $home = ContentEntry::query()
            ->where('site_id', $site->id)
            ->where('slug', 'home')
            ->firstOrFail();

        $this->get('/app/'.$site->slug.'/'.$home->slug)
            ->assertOk()
            ->assertSee('Home');
    }

    public function test_database_seeder_is_idempotent_for_demo_core_records(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $organization = Organization::query()->where('slug', 'kabeeri-demo-org')->firstOrFail();
        $site = Site::query()->where('slug', 'kabeeri-demo-app')->firstOrFail();

        $this->assertSame(1, User::query()->where('email', 'admin@kabeeri.local')->count());
        $this->assertSame(1, Organization::query()->where('slug', 'kabeeri-demo-org')->count());
        $this->assertSame(
            1,
            Company::query()
                ->where('organization_id', $organization->id)
                ->where('slug', 'kabeeri-demo-company')
                ->count(),
        );
        $this->assertSame(
            4,
            ContentEntry::query()
                ->where('site_id', $site->id)
                ->whereIn('slug', ['home', 'about', 'services', 'contact'])
                ->count(),
        );
    }
}
