<?php

namespace App\Modules\Platform\Services;

use App\Models\MobileApiManifest;
use App\Models\MobileAppConfig;
use App\Models\MobileAuthToken;
use App\Models\MobileDevice;
use App\Models\MobilePushToken;
use App\Models\MobileThemeProfile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MobileAppService
{
    public function activeConfig(?string $appKey = null): ?MobileAppConfig
    {
        return MobileAppConfig::query()
            ->where('status', 'active')
            ->when($appKey, fn ($query) => $query->where('app_key', $appKey))
            ->orderBy('id')
            ->first();
    }

    public function createDefaultConfig(): MobileAppConfig
    {
        $config = MobileAppConfig::query()->updateOrCreate(
            ['organization_id' => null, 'app_key' => 'kabeeri-mobile'],
            [
                'name' => 'kabeeri Mobile',
                'platform' => 'universal',
                'status' => 'active',
                'min_supported_version' => '1.0.0',
                'current_version' => '1.0.0',
                'settings' => ['public_mall' => true, 'auth' => true],
                'metadata' => ['source' => 'v7_default'],
            ],
        );

        MobileThemeProfile::query()->updateOrCreate(
            ['mobile_app_config_id' => $config->id, 'name' => 'kabeeri Mobile Default'],
            [
                'status' => 'active',
                'colors' => ['primary' => '#000000', 'surface' => '#f0f0f0'],
                'typography' => ['heading' => 'serif', 'body' => 'sans'],
                'layout' => ['navigation' => 'tabs'],
            ],
        );

        MobileApiManifest::query()->updateOrCreate(
            ['mobile_app_config_id' => $config->id, 'version' => 'v1'],
            [
                'status' => 'active',
                'endpoints' => [
                    'config' => '/api/mobile/config',
                    'manifest' => '/api/mobile/manifest',
                    'theme' => '/api/mobile/theme',
                    'login' => '/api/mobile/auth/login',
                    'register' => '/api/mobile/auth/register',
                    'device' => '/api/mobile/devices',
                    'push_token' => '/api/mobile/push-tokens',
                ],
                'capabilities' => ['public_config', 'auth_tokens', 'device_registry', 'push_tokens'],
            ],
        );

        return $config->refresh();
    }

    public function registerDevice(?User $user, MobileAppConfig $config, array $attributes): MobileDevice
    {
        return MobileDevice::query()->updateOrCreate(
            [
                'mobile_app_config_id' => $config->id,
                'device_uuid' => $attributes['device_uuid'],
            ],
            [
                'user_id' => $user?->id,
                'platform' => $attributes['platform'] ?? 'unknown',
                'app_version' => $attributes['app_version'] ?? null,
                'status' => 'active',
                'last_seen_at' => now(),
                'metadata' => $attributes['metadata'] ?? null,
            ],
        );
    }

    public function registerPushToken(MobileDevice $device, string $provider, string $plainToken): MobilePushToken
    {
        return MobilePushToken::query()->updateOrCreate(
            ['provider' => $provider, 'token_hash' => hash('sha256', $plainToken)],
            [
                'mobile_device_id' => $device->id,
                'status' => 'active',
                'last_used_at' => now(),
            ],
        );
    }

    /**
     * @return array{token: string, record: MobileAuthToken}
     */
    public function issueToken(User $user, ?MobileDevice $device = null): array
    {
        $token = Str::random(64);

        $record = MobileAuthToken::query()->create([
            'user_id' => $user->id,
            'mobile_device_id' => $device?->id,
            'token_hash' => hash('sha256', $token),
            'status' => 'active',
            'expires_at' => now()->addDays(30),
            'abilities' => ['mobile:*'],
        ]);

        return ['token' => $token, 'record' => $record];
    }

    public function login(string $email, string $password, ?MobileDevice $device = null): array
    {
        $user = User::query()->where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Invalid mobile credentials.',
            ]);
        }

        return ['user' => $user, ...$this->issueToken($user, $device)];
    }
}
