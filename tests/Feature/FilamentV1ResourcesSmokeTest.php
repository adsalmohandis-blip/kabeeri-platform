<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Organization;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class FilamentV1ResourcesSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_access_v1_filament_resource_indexes(): void
    {
        $owner = User::factory()->create();
        Organization::factory()->create([
            'owner_user_id' => $owner->id,
        ]);

        $this->actingAs($owner);

        $this->get('/admin/companies')->assertOk();
        $this->get('/admin/companies/create')->assertOk();
        $this->get('/admin/sites')->assertOk();
        $this->get('/admin/sites/create')->assertOk();
        $this->get('/admin/content-types')->assertOk();
        $this->get('/admin/content-entries')->assertOk();
        $this->get('/admin/taxonomies')->assertOk();
        $this->get('/admin/activity-logs')->assertOk();
        $this->get('/admin/media-assets')->assertOk();
        $this->get('/admin/feature-flags')->assertOk();
        $this->get('/admin/settings')->assertOk();
    }

    public function test_outsider_cannot_edit_other_company_in_filament(): void
    {
        $owner = User::factory()->create();
        $outsider = User::factory()->create();
        $organization = Organization::factory()->create([
            'owner_user_id' => $owner->id,
        ]);
        $company = Company::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $this->actingAs($outsider);
        $response = $this->get('/admin/companies/'.$company->id.'/edit');

        $this->assertContains($response->getStatusCode(), [403, 404]);
    }

    public function test_feature_flag_resource_is_read_only_for_flags(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get('/admin/feature-flags/create')->assertNotFound();
        $this->get('/admin/feature-flags/1/edit')->assertNotFound();
    }

    public function test_platform_scope_setting_cannot_be_modified_by_regular_user(): void
    {
        $user = User::factory()->create();
        $setting = Setting::query()->create([
            'organization_id' => null,
            'scope_type' => 'platform',
            'scope_id' => null,
            'key' => 'platform.debug',
            'value' => ['value' => true],
            'value_type' => 'boolean',
            'is_encrypted' => false,
        ]);

        $this->assertTrue(Gate::forUser($user)->denies('update', $setting));
    }
}
