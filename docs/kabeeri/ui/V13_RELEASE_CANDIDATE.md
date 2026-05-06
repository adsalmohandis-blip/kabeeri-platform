# KABEERI V13 Release Candidate Check

Created on 2026-05-07.

## Scope

V13 covers:

- Mall home redesign.
- Mall search/filter UX.
- Business directory index/detail upgrade.
- Product index/detail upgrade.
- Service index/detail upgrade.
- Course index/detail upgrade.
- Talent index/detail upgrade.
- Travel index/detail upgrade.
- Customer portal dashboard foundation.
- Customer theme/plugin selection UI.
- Customer products/services quick setup UI.
- Partner landing.
- Agency profile setup UI.
- Partner storefront builder UI.
- Referral dashboard UI.
- Marketer campaign resources UI.
- Work Network and Academy portal foundation.
- Mall vs Marketplace public copy separation.
- Mall trust, verification, moderation, claim, submit, and report flows.
- Public talent/professional profile path.
- Legal partner verification journey UI.

V13 does not implement final payout settlement, irreversible claim approvals, legal document review automation, or full authenticated customer dashboards.

## Acceptance Criteria

- V9 foundation remains ready.
- V10 admin command UX remains ready.
- V11 public UX remains ready.
- V12 marketplace/developer UX remains ready.
- Existing Mall routes remain registered.
- New V13 customer, partner, network, search, trust, and claim/report routes are registered.
- Mall pages render upgraded V13 shell.
- Mall public copy separates Mall from Marketplace.
- Trust badges, moderation, claim/report, customer steps, partner paths, referrals, campaign resources, network, and legal verification are represented.
- Mall and partner database tables exist.
- Moderation/review/partner admin resource routes exist.
- V13 docs exist.
- V13 smoke tests exist.
- V13 task tracker is synced to implementation truth.

## Verification Commands

```bash
php artisan test --filter=V13ExternalExperienceTest
php artisan test --filter=PublicMallNavigationUxTest
php artisan test --filter=V12MarketplaceExperienceTest
php artisan test --filter=V11PublicExperienceTest
php artisan test --filter=V10AdminExperienceTest
vendor/bin/pint --test
npm run build
```

Recommended before merge:

```bash
php artisan test
```

## Go / No-Go

Go when `App\Support\Ui\V13ExternalExperience::isReleaseCandidateReady()` returns true after task tracker sync.

No-Go if any Mall route regresses, Mall and Marketplace copy is mixed, trust/claim/report routes are missing, partner/customer/network pages do not render, V9-V12 readiness regresses, docs are absent, tests are absent, or the task tracker says done before implementation exists.

## Known Limits

- Blade remains a bridge for V13 external portal pages.
- Authenticated customer dashboard internals are represented as UX foundation, not full account operations.
- Commission and payout UI is a placeholder until billing settlement policy is implemented.
- Claim/report flows are explanatory UI in V13; irreversible admin approvals remain future workflow work.
