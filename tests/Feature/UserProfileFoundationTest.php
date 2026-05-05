<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserProfileFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_have_a_profile(): void
    {
        $user = User::factory()->create();
        $country = Country::factory()->create();

        $profile = UserProfile::factory()->create([
            'user_id' => $user->id,
            'country_id' => $country->id,
            'visibility' => 'private',
        ]);

        $this->assertTrue($user->profile->is($profile));
        $this->assertSame($user->id, $profile->user->id);
    }

    public function test_users_table_does_not_include_tenant_fields(): void
    {
        $this->assertFalse(Schema::hasColumn('users', 'organization_id'));
        $this->assertFalse(Schema::hasColumn('users', 'company_id'));
        $this->assertFalse(Schema::hasColumn('users', 'site_id'));
        $this->assertFalse(Schema::hasColumn('users', 'role'));
    }

    public function test_new_user_receives_ulid(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->ulid);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'ulid' => $user->ulid,
        ]);
    }
}
