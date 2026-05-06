# KABEERI V15 Release Candidate

V15 is release-candidate ready when the Next.js public runtime scaffold exists, the Laravel manifest contract is available, and task tracker truth matches implementation truth.

## Go Gates

- V14 UI release candidate remains ready.
- `public-web.manifest` route exists.
- `/api/public-web/manifest` returns `version: V15` and `contract: public-web.v1`.
- `apps/public-web` exists with App Router, locale route, tokens, UI primitives, sections, API client, navigation manifest, theme manifest, and smoke test.
- Root package scripts expose `public-web:build`, `public-web:typecheck`, and `public-web:smoke`.
- Workspace package scripts expose `build`, `typecheck`, and `test:smoke`.
- V15 docs exist.
- V15 tests pass.
- V15 task tracker is synced to actual implementation.

## No-Go Gates

- Any required Next.js scaffold file is missing.
- The public runtime route is missing or returns a non-V15 contract.
- The scaffold depends on Laravel domain internals instead of JSON contracts.
- Marketplace and Mall are presented as the same product path.
- Task tracker status says complete before files/tests exist.

## Verification Commands

```bash
npm run public-web:smoke
npm run public-web:typecheck
npm run public-web:build
vendor/bin/pint --test
php artisan test --filter=V15PublicRuntimeTest
php artisan test
```

## Owner Handoff

Use V15 as the starting point for moving public pages from Blade bridge to Next.js in controlled slices:

1. Public marketing and onboarding pages from V11.
2. Marketplace and developer pages from V12.
3. Mall/customer/partner pages from V13.
4. Route parity and visual QA against V14 gates.

Do not move admin pages to Next.js. Admin remains Filament.

## Known Dependency Note

`npm audit --omit=dev` currently reports a moderate PostCSS advisory through `next@16.2.5` because Next pins an internal `postcss@8.4.31`. The automatic `npm audit fix --force` path would downgrade Next to an incompatible old version, so it is not applied here. Re-check this when a stable Next release updates the pinned PostCSS dependency.
