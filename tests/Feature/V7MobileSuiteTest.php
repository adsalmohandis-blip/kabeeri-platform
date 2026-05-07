<?php

namespace Tests\Feature;

use App\Models\MobileAuthToken;
use App\Models\MobileDevice;
use App\Models\MobilePushToken;
use App\Models\User;
use App\Modules\Platform\Services\MobileAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class V7MobileSuiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_v7_mobile_tables_exist(): void
    {
        foreach ([
            'mobile_app_configs',
            'mobile_theme_profiles',
            'mobile_api_manifests',
            'mobile_devices',
            'mobile_push_tokens',
            'mobile_auth_tokens',
        ] as $table) {
            $this->assertTrue(Schema::hasTable($table), "Missing V7 table [{$table}].");
        }
    }

    public function test_mobile_service_creates_config_device_push_and_auth_records_safely(): void
    {
        $service = app(MobileAppService::class);
        $config = $service->createDefaultConfig();
        $user = User::factory()->create(['password' => 'password']);
        $device = $service->registerDevice($user, $config, [
            'device_uuid' => 'device-1',
            'platform' => 'ios',
            'app_version' => '1.0.0',
        ]);
        $push = $service->registerPushToken($device, 'fcm', 'plain-push-token');
        $auth = $service->issueToken($user, $device);

        $this->assertSame('active', $config->status);
        $this->assertSame('active', $device->status);
        $this->assertSame(hash('sha256', 'plain-push-token'), $push->token_hash);
        $this->assertNotSame('plain-push-token', $push->token_hash);
        $this->assertSame(hash('sha256', $auth['token']), $auth['record']->token_hash);
        $this->assertDatabaseMissing('mobile_auth_tokens', ['token_hash' => $auth['token']]);
    }

    public function test_public_mobile_apis_return_config_manifest_and_theme(): void
    {
        app(MobileAppService::class)->createDefaultConfig();

        $this->getJson('/api/mobile/config')
            ->assertOk()
            ->assertJsonPath('app_key', 'kabeeri-mobile');

        $this->getJson('/api/mobile/manifest')
            ->assertOk()
            ->assertJsonPath('endpoints.login', '/api/mobile/auth/login');

        $this->getJson('/api/mobile/theme')
            ->assertOk()
            ->assertJsonPath('name', 'kabeeri Mobile Default');
    }

    public function test_mobile_auth_and_device_apis_work_without_raw_token_storage(): void
    {
        app(MobileAppService::class)->createDefaultConfig();
        $user = User::factory()->create([
            'email' => 'mobile@example.test',
            'password' => 'password',
        ]);

        $login = $this->postJson('/api/mobile/auth/login', [
            'email' => 'mobile@example.test',
            'password' => 'password',
            'device_uuid' => 'login-device',
            'platform' => 'android',
        ])->assertOk()
            ->assertJsonPath('token_type', 'Bearer')
            ->json();

        $this->assertNotEmpty($login['token']);
        $this->assertDatabaseHas('mobile_auth_tokens', [
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $login['token']),
        ]);
        $this->assertDatabaseMissing('mobile_auth_tokens', ['token_hash' => $login['token']]);

        $deviceResponse = $this->postJson('/api/mobile/devices', [
            'device_uuid' => 'push-device',
            'platform' => 'android',
            'app_version' => '1.0.0',
        ])->assertCreated()->json();

        $this->postJson('/api/mobile/push-tokens', [
            'device_id' => $deviceResponse['device_id'],
            'provider' => 'fcm',
            'token' => 'raw-device-push-token',
        ])->assertCreated();

        $this->assertSame(2, MobileDevice::query()->count());
        $this->assertSame(1, MobilePushToken::query()->count());
        $this->assertSame(hash('sha256', 'raw-device-push-token'), MobilePushToken::query()->firstOrFail()->token_hash);
        $this->assertSame(1, MobileAuthToken::query()->count());
    }

    public function test_mobile_register_api_creates_user_and_token(): void
    {
        app(MobileAppService::class)->createDefaultConfig();

        $response = $this->postJson('/api/mobile/auth/register', [
            'name' => 'Mobile User',
            'email' => 'new-mobile@example.test',
            'password' => 'password',
            'device_uuid' => 'register-device',
            'platform' => 'ios',
        ])->assertCreated()->json();

        $this->assertDatabaseHas('users', ['email' => 'new-mobile@example.test']);
        $this->assertNotEmpty($response['token']);
        $this->assertSame(1, MobileAuthToken::query()->count());
        $this->assertSame(1, MobileDevice::query()->count());
    }
}
