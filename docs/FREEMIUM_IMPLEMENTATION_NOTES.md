# KABEERI Freemium Backend Implementation Notes

Date: 2026-05-06
Status: Codex implementation complete, pending owner verification.

## Backend Scope

The Freemium backend adds a real entitlement layer over `organizations.plan_code` without changing the existing organization foundation.

Implemented database tables:

- `plans`
- `plan_entitlements`
- `usage_records`
- `entitlement_overrides`

Implemented services:

- `PlanResolverService`: resolves the current organization plan with a safe Free fallback.
- `EntitlementService`: checks boolean, numeric, string, metered, and override-based entitlements.
- `UsageMeterService`: records monthly usage and feeds entitlement checks.
- `UpgradeTriggerService`: returns upgrade-required or upgrade-suggested payloads.
- `FairUseGuardService`: flags near-limit and over-limit usage.
- `FreemiumPlanVisibilityService`: returns a public plan summary for UI/Admin visibility.

## Seeded Plans

- `free`: one app, 25 pages, 250 MB storage, free themes/plugins only, community support.
- `starter`: three apps, larger pages/storage, custom domain, branding removal, pro themes/plugins.
- `business`: more apps, commerce capacity, priority support, larger team limits.
- `agency`: multi-client scale and partner support.
- `enterprise`: custom/unlimited style limits and SLA support.

## Enforcement Points

The backend now has service-level checks for:

- Creating an app/site: `canCreateApp()` checks `can_create_app` and `max_apps`.
- Installing themes: `canInstallTheme()` checks free vs pro theme rights.
- Installing plugins: `canInstallPlugin()` checks free vs paid plugin rights.
- AI credits and metered usage: `UsageMeterService` + `EntitlementService`.
- Upgrade prompts: `UpgradeTriggerService`.
- Fair-use monitoring: `FairUseGuardService`.

## Filament Visibility

- Added `Plans & Entitlements` read-only resource under the `Freemium` navigation group.
- Added `plan_code` visibility and filter support to the existing Organization resource.

## Verification

Targeted command executed:

```bash
php artisan test --filter=Freemium
```

Result: 10 tests passed, 33 assertions.
