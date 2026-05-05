<?php

namespace App\Modules\Core\Services;

use App\Models\Setting;

class SettingService
{
    public function set(
        string $scopeType,
        ?int $scopeId,
        string $key,
        mixed $value,
        string $valueType = 'string',
        bool $isEncrypted = false,
        ?int $organizationId = null,
    ): Setting {
        $storedValue = $isEncrypted
            ? $this->encodeValue($value)
            : ['value' => $value];

        return Setting::query()->updateOrCreate(
            [
                'scope_type' => $scopeType,
                'scope_id' => $scopeId,
                'key' => $key,
            ],
            [
                'organization_id' => $organizationId,
                'value' => $storedValue,
                'value_type' => $valueType,
                'is_encrypted' => $isEncrypted,
            ],
        );
    }

    public function get(
        string $scopeType,
        ?int $scopeId,
        string $key,
        mixed $default = null,
    ): mixed {
        $setting = Setting::query()
            ->where('scope_type', $scopeType)
            ->where('scope_id', $scopeId)
            ->where('key', $key)
            ->first();

        if (! $setting) {
            return $default;
        }

        return $setting->is_encrypted
            ? $this->decodeValue($setting->value)
            : ($setting->value['value'] ?? $default);
    }

    /**
     * @param  array<int, array{scope_type: string, scope_id: int|null}>  $scopes
     */
    public function getForScopes(array $scopes, string $key, mixed $default = null): mixed
    {
        foreach ($scopes as $scope) {
            $value = $this->get(
                $scope['scope_type'],
                $scope['scope_id'],
                $key,
                default: null,
            );

            if ($value !== null) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * Placeholder only for V1 foundation.
     */
    protected function encodeValue(mixed $value): array
    {
        return [
            'encoded' => base64_encode(json_encode($value, JSON_THROW_ON_ERROR)),
            'driver' => 'base64_placeholder',
        ];
    }

    protected function decodeValue(?array $value): mixed
    {
        if (! is_array($value) || ! isset($value['encoded'])) {
            return null;
        }

        return json_decode(base64_decode($value['encoded'], true) ?: 'null', true, 512, JSON_THROW_ON_ERROR);
    }
}
