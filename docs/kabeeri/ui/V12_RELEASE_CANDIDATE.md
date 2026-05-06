# KABEERI V12 Release Candidate Check

Created on 2026-05-07.

## Scope

V12 covers the UI and product contract for:

- Theme catalog and preview.
- Theme recipes and apply-preview explanation.
- Plugin bundle catalog.
- Plugin detail, permissions, dependencies, compatibility, signing, and support.
- Marketplace home.
- Developer portal.
- Creator/publisher onboarding.
- Theme builder documentation.
- Plugin manifest documentation.
- Connector SDK documentation.
- Submission checklist.
- Listing management.
- Sales and usage dashboard.
- Marketplace governance and review.
- Lifecycle statuses.
- Licensing and revenue share.
- Design Market taxonomy and filters.
- QA checklists for themes and plugins.

V12 does not build the final Next.js runtime, package upload binaries, payment payouts, or irreversible package install actions.

## Acceptance Criteria

- V9 foundation remains ready.
- V10 admin command UX remains ready.
- V11 public bridge remains ready.
- V12 defines at least 19 Marketplace/Developer pages.
- Public/developer routes are registered.
- Marketplace admin resource routes exist.
- Marketplace database tables exist.
- Theme catalog includes taxonomy, filters, compatibility, RTL, and performance.
- Plugin catalog includes permissions, dependencies, support, risk, and compatibility.
- Lifecycle statuses include owner and next action.
- Manifest permissions/signing/compatibility UX is represented.
- Theme and plugin QA checklists are represented.
- Licensing/revenue-share UX is represented.
- V12 docs exist.
- V12 task tracker is synced to real implementation state.

## Verification Commands

```bash
php artisan test --filter=V12MarketplaceExperienceTest
php artisan test --filter=V11PublicExperienceTest
php artisan test --filter=V10AdminExperienceTest
php artisan test --filter=V9UiFoundationTest
vendor/bin/pint --test
npm run build
```

Recommended before merge:

```bash
php artisan test
```

## Go / No-Go

Go when `App\Support\Ui\V12MarketplaceExperience::isReleaseCandidateReady()` returns true after task tracker sync.

No-Go if any V12 configured route is missing, admin resource route is missing, marketplace database table is missing, V9/V10/V11 readiness regresses, docs are absent, smoke tests are absent, or the task tracker says done before the UI exists.

## Known Limits

- Blade pages are a bridge; Next.js remains the target theme runtime.
- Catalog items are configuration-backed UX examples until richer marketplace APIs are exposed.
- No package install action is executed in V12.
- No real payout workflow is executed in V12.
- Governance links hand off to existing Filament resources where available.
