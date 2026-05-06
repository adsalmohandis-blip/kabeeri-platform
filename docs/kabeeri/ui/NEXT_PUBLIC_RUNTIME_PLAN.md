# KABEERI Next.js Public Runtime Plan

Created on 2026-05-06.

## Decision

The long-term public web/theme runtime is Next.js + React + TypeScript + Tailwind. Laravel Blade remains a bridge/fallback only for the current root dashboard, current Mall pages, and current public content rendering.

## Why

KBR v1.6.6 separates Laravel backend/admin from public commercial themes. Theme developers should not need Laravel internals. They should build against manifests, API contracts, components, and stable public data responses.

## Proposed Scaffold

Path: `apps/public-web`.

Minimum structure:

```text
apps/public-web/
  app/[locale]/
  components/ui/
  components/sections/
  lib/api/
  lib/theme-manifest/
  styles/tokens.css
  tests/smoke/
```

## Required Stack

- Next.js.
- React.
- TypeScript.
- Tailwind.
- ESLint.
- RTL-aware component primitives.
- API client generated or documented from OpenAPI contracts.

## Required API Contracts

Public web needs APIs for:

- App/site public content.
- Theme profile and settings.
- Menu/navigation.
- Pricing/plans.
- Mall listings.
- Developer marketplace catalog.
- Package/theme manifest metadata.

## Rules Before Scaffolding

- V9 route registry must exist.
- OpenAPI requirements must be documented.
- Blade fallback boundary must be documented.
- The first Next.js scaffold must not duplicate Laravel domain logic.
- Theme packages must not contain hidden database access or business logic.

## Migration Path

1. Keep current Blade pages as bridge pages.
2. Build API contracts and response versioning.
3. Scaffold `apps/public-web`.
4. Recreate public marketing/onboarding in Next.js in V11.
5. Recreate developer marketplace/theme UX in V12.
6. Recreate Mall external UX in V13.
7. Validate separation, accessibility, responsive, RTL/LTR, and route parity in V14.