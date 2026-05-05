# KABEERI V1 Modules

This document describes how V1 is organized today and what is intentionally out of scope.

## Active V1 Domains

`Core`:
- Organizations, memberships, companies, apps/sites
- Roles/permissions and scoped permission checks
- Settings, feature flags, activity logging
- Onboarding (`CreateFirstWorkspace`, `OnboardingService`)

`CMS`:
- Content types, entries, revisions
- Taxonomies and terms
- CMS actions for create/update/publish/archive
- Public rendering route for published/public pages

`Media`:
- Media asset metadata storage
- Scoped media access policies
- Basic media library admin resource

`Rabet`:
- Business profile draft foundation
- Verification request/document draft foundation

## Filament Admin Navigation (V1)

- `Core`
- `Organizations`
- `Apps`
- `Content`
- `Media`
- `Rabet Foundation`
- `System`

## V1 Boundaries

Allowed in V1:
- Safe multi-tenant foundation
- Admin operational workflows
- Basic public page rendering
- Demo seed data for local validation

Not allowed in V1 (postponed):
- Marketplace and developer submissions
- Paid package lifecycle
- V2+ commerce/ERP features
- External sync stack
- Visual website builder

## Tenant Rules (Must Keep)

- Do not add `role`, `organization_id`, `company_id`, or `site_id` columns to `users`.
- Enforce access through memberships, roles, and scoped policies.
- Keep activity logs read-only in admin.
- Prevent cross-organization access by query scope + policy checks.
