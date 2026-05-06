# KABEERI V14 UI Release Candidate

Created on 2026-05-07.

## Goal

V14 verifies that the UI work from V9 through V13 is consistent, testable, documented, and ready for owner review as a release candidate.

## Go Criteria

Go when:

- V9 foundation readiness is true.
- V10 admin readiness is true.
- V11 public readiness is true.
- V12 marketplace/developer readiness is true.
- V13 external/Mall readiness is true.
- Full UI route inventory has no missing configured routes.
- Accessibility coverage is represented.
- Responsive coverage is represented.
- Security and permission UI coverage is represented.
- Design system coverage is represented.
- Theme and plugin QA checklist coverage is represented.
- Marketplace vs Mall separation is represented.
- V14 docs exist.
- V14 tests exist and pass.
- V14 task tracker is synced to implementation truth.

## No-Go Criteria

No-Go if:

- Any V9-V13 readiness check regresses.
- Any configured UI route is missing.
- Mall and Marketplace language is mixed.
- Package permissions or signing are hidden from Marketplace UX.
- Mall trust, claim, report, or moderation language is absent.
- Permission-aware navigation is not represented.
- Build, Pint, or full test suite fails.
- Task tracker status does not match implementation state.

## Verification Commands

```bash
php artisan test --filter=V14UiReleaseCandidateTest
php artisan test --filter=V13ExternalExperienceTest
php artisan test --filter=V12MarketplaceExperienceTest
php artisan test --filter=V11PublicExperienceTest
php artisan test --filter=V10AdminExperienceTest
php artisan test --filter=V9UiFoundationTest
vendor/bin/pint --test
npm run build
php artisan test
```

## Release Center

The UI release candidate center route is:

`/ui/release-candidate`

It displays:

- Previous version readiness.
- Full route inventory.
- Go/No-Go gates.
- Quality coverage.
- Verification commands.

## Known Limits

- V14 is a QA and release-readiness layer, not a replacement for human visual review.
- Next.js public runtime remains documented and planned; it is not scaffolded in this pass.
- Some future authenticated dashboards are represented as UX foundations rather than complete production workflows.
- Commission, payout, and irreversible claim approval flows remain future backend/workflow implementation items.
