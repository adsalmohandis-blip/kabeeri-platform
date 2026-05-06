# KABEERI UI Smoke Test Strategy

Created on 2026-05-06.

## Purpose

UI smoke tests protect the team from shipping pages that render but do not follow Kabeeri boundaries, RTL expectations, route registry rules, or task tracker truth.

## V9 Smoke Tests

Required commands:

```bash
php artisan test --filter=V9UiFoundationTest
php artisan test --filter=RootDashboardPageTest
vendor/bin/pint --test
npm run build
```

Recommended broader checks before merge:

```bash
php artisan test
```

## Route Rendering Smoke

- `/` renders the root command dashboard.
- `/admin/login` renders the Filament login page.
- `/mall` renders public Mall bridge navigation.
- `/mall/products` renders cross-section Mall navigation.
- `/api/mobile/manifest` returns the current mobile manifest contract.

## Visual Smoke

- `html` direction is RTL where Arabic UI is shown.
- Navigation is visible and understandable on mobile.
- No horizontal scroll at 360px width.
- Focus states are visible.
- Cards/buttons/links use design tokens rather than one-off colors where possible.

## Data Smoke

- Root dashboard reads task JSON files and not hardcoded task totals.
- V9 foundation config contains design tokens, admin spaces, external audiences, route registry, and runtime boundaries.
- Current route registry routes exist in Laravel.
- Planned Next.js routes are documented as planned, not implemented.

## Accessibility Smoke

- Semantic headings are present.
- Interactive elements are keyboard reachable.
- Focus-visible styles are not removed.
- Important status is expressed in text, not color only.

## Permission-Aware Smoke

- Admin navigation may hide unavailable actions, but backend policies must still protect sensitive operations.
- Blocked-action copy should explain permission, plan, context, or review requirements.
- Sensitive actions must be reviewed again in V10, V12, and V14.