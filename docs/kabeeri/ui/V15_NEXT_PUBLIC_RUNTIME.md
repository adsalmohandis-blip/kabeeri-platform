# KABEERI V15 Next.js Public Runtime

V15 turns the documented public-runtime decision into a real scaffold under `apps/public-web`.

## Decision

- Laravel remains the API-first backend, policy layer, database owner, jobs owner, and admin runtime host.
- Filament remains the internal admin runtime.
- Blade remains a temporary bridge for current public pages.
- Next.js becomes the public theme/runtime scaffold for future commercial themes and audience-led pages.

## Implemented Artifacts

- `config/kabeeri_public_runtime.php`: source of truth for V15 rules, workspace, API contract, public routes, audience paths, theme manifest, required files, and quality gates.
- `App\Support\Ui\V15PublicRuntime`: manifest and release-readiness service.
- `App\Http\Controllers\Public\PublicWebRuntimeController`: public runtime manifest endpoint.
- Route: `GET /api/public-web/manifest` named `public-web.manifest`.
- `apps/public-web`: Next.js App Router workspace with TypeScript, Tailwind CSS 4, RTL-first tokens, UI primitives, page sections, API client, theme manifest, navigation manifest, and smoke test.
- `tests/Feature/V15PublicRuntimeTest.php`: Laravel feature tests for contract, route registry, files, scripts, and release readiness.

## Runtime Boundary

The Next.js app must not import Laravel models, Blade views, database calls, policies, or PHP internals. It consumes JSON contracts such as:

- `/api/public-web/manifest`
- future public content APIs
- future theme profile APIs
- future menu/navigation APIs
- future pricing/plans APIs
- future Mall listing APIs
- future Marketplace catalog APIs

## Current Scope

V15 is a scaffold and contract layer. It does not replace the current Blade public pages in production yet. It creates the safe place where V11 public marketing, V12 Marketplace/developer UX, and V13 Mall/customer/partner UX can later be moved into Next.js page-by-page.

## Commands

```bash
npm run public-web:smoke
npm run public-web:typecheck
npm run public-web:build
php artisan test --filter=V15PublicRuntimeTest
```