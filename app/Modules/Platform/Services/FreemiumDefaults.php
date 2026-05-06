<?php

namespace App\Modules\Platform\Services;

class FreemiumDefaults
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function plans(): array
    {
        return [
            'free' => [
                'name' => 'Free / Community',
                'tier' => 'free',
                'price_cents' => 0,
                'sort_order' => 10,
                'entitlements' => self::entitlements([
                    'can_create_app' => true,
                    'max_apps' => 1,
                    'max_pages' => 25,
                    'max_storage_mb' => 250,
                    'can_use_custom_domain' => false,
                    'can_remove_branding' => false,
                    'can_install_free_themes' => true,
                    'can_install_pro_themes' => false,
                    'can_install_free_plugins' => true,
                    'can_install_paid_plugins' => false,
                    'ai_credits_monthly' => 0,
                    'support_level' => 'community',
                    'commerce_products_max' => 25,
                    'team_members_max' => 1,
                ], 'starter'),
            ],
            'starter' => [
                'name' => 'Starter / Pro',
                'tier' => 'starter',
                'price_cents' => 1900,
                'sort_order' => 20,
                'entitlements' => self::entitlements([
                    'can_create_app' => true,
                    'max_apps' => 3,
                    'max_pages' => 150,
                    'max_storage_mb' => 5120,
                    'can_use_custom_domain' => true,
                    'can_remove_branding' => true,
                    'can_install_free_themes' => true,
                    'can_install_pro_themes' => true,
                    'can_install_free_plugins' => true,
                    'can_install_paid_plugins' => true,
                    'ai_credits_monthly' => 1000,
                    'support_level' => 'standard',
                    'commerce_products_max' => 500,
                    'team_members_max' => 5,
                ], 'business'),
            ],
            'business' => [
                'name' => 'Business',
                'tier' => 'business',
                'price_cents' => 4900,
                'sort_order' => 30,
                'entitlements' => self::entitlements([
                    'can_create_app' => true,
                    'max_apps' => 10,
                    'max_pages' => 1000,
                    'max_storage_mb' => 51200,
                    'can_use_custom_domain' => true,
                    'can_remove_branding' => true,
                    'can_install_free_themes' => true,
                    'can_install_pro_themes' => true,
                    'can_install_free_plugins' => true,
                    'can_install_paid_plugins' => true,
                    'ai_credits_monthly' => 10000,
                    'support_level' => 'priority',
                    'commerce_products_max' => 5000,
                    'team_members_max' => 25,
                ], 'agency'),
            ],
            'agency' => [
                'name' => 'Agency',
                'tier' => 'agency',
                'price_cents' => 9900,
                'sort_order' => 40,
                'entitlements' => self::entitlements([
                    'can_create_app' => true,
                    'max_apps' => 50,
                    'max_pages' => 5000,
                    'max_storage_mb' => 204800,
                    'can_use_custom_domain' => true,
                    'can_remove_branding' => true,
                    'can_install_free_themes' => true,
                    'can_install_pro_themes' => true,
                    'can_install_free_plugins' => true,
                    'can_install_paid_plugins' => true,
                    'ai_credits_monthly' => 50000,
                    'support_level' => 'partner',
                    'commerce_products_max' => 25000,
                    'team_members_max' => 100,
                ], 'enterprise'),
            ],
            'enterprise' => [
                'name' => 'Enterprise',
                'tier' => 'enterprise',
                'price_cents' => 0,
                'sort_order' => 50,
                'entitlements' => self::entitlements([
                    'can_create_app' => true,
                    'max_apps' => null,
                    'max_pages' => null,
                    'max_storage_mb' => null,
                    'can_use_custom_domain' => true,
                    'can_remove_branding' => true,
                    'can_install_free_themes' => true,
                    'can_install_pro_themes' => true,
                    'can_install_free_plugins' => true,
                    'can_install_paid_plugins' => true,
                    'ai_credits_monthly' => null,
                    'support_level' => 'sla',
                    'commerce_products_max' => null,
                    'team_members_max' => null,
                ], null),
            ],
        ];
    }

    /**
     * @param  array<string, bool|int|string|null>  $values
     * @return array<string, array<string, mixed>>
     */
    private static function entitlements(array $values, ?string $upgradePlanCode): array
    {
        $entitlements = [];

        foreach ($values as $key => $value) {
            $entitlements[$key] = match (true) {
                is_bool($value) => [
                    'value_type' => 'boolean',
                    'limit_value' => null,
                    'bool_value' => $value,
                    'string_value' => null,
                    'behavior' => $value ? 'allow' : 'upgrade',
                    'upgrade_plan_code' => $value ? null : $upgradePlanCode,
                ],
                is_string($value) => [
                    'value_type' => 'string',
                    'limit_value' => null,
                    'bool_value' => null,
                    'string_value' => $value,
                    'behavior' => 'allow',
                    'upgrade_plan_code' => null,
                ],
                default => [
                    'value_type' => 'integer',
                    'limit_value' => $value,
                    'bool_value' => null,
                    'string_value' => null,
                    'behavior' => 'metered',
                    'upgrade_plan_code' => $upgradePlanCode,
                ],
            };
        }

        return $entitlements;
    }
}
