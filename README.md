# KABEERI V1

KABEERI V1 is the first stable Laravel + Filament foundation for multi-tenant organizations, apps, CMS content, media, settings, feature flags, activity logs, onboarding, and Rabet draft profile basics.

## V1 Scope

Included in V1:
- Multi-tenant core: organizations, memberships, companies, apps/sites.
- CMS core: content types, content entries, revisions, taxonomies.
- CMS actions/policies: create, update, publish, archive.
- Public page rendering route: `/app/{site:slug}/{contentEntry:slug}` for published/public pages.
- Media metadata foundation + media library resource.
- Feature flags and scoped settings resources with policy restrictions.
- Activity logs (read-only resource, tenant-scoped).
- Basic onboarding flow (`CreateFirstWorkspace`).
- Rabet foundation: business profile + verification request/documents (draft foundation).
- V1 demo seeding and smoke/security tests.

Intentionally postponed to V2+:
- Public marketplace, paid packages, developer economy submissions.
- Commerce/ERP workflows and Mall network logic.
- Advanced page builder / visual builder.
- WordPress importer implementation.
- Enterprise security tracks (SSO, SIEM, MFA, GRC).

## Quick Start

Requirements:
- PHP `^8.3`
- Composer `^2`
- SQLite (default) or MySQL

Install and run:

```powershell
composer install
Copy-Item .env.example .env -Force
php artisan key:generate
if (!(Test-Path .\database\database.sqlite)) { New-Item .\database\database.sqlite -ItemType File | Out-Null }
php artisan migrate --seed
php artisan serve
```

Open:
- App: `http://127.0.0.1:8000`
- Admin: `http://127.0.0.1:8000/admin`

If `php` points to an old binary on this machine, use the explicit PHP 8.3 path documented in [docs/V1_LOCAL_SETUP.md](docs/V1_LOCAL_SETUP.md).

## Demo Seed

Local demo admin (seeded by `DatabaseSeeder`):
- Email: `admin@kabeeri.local`
- Password: `password`

Seeded demo records:
- Demo organization + owner membership
- Demo company
- Demo app/site (`kabeeri-demo-app`)
- Starter theme attached
- Content types: `page`, `post`
- Published pages: `home`, `about`, `services`, `contact`
- Demo media metadata record
- Business profile draft

Public demo page URL:
- `http://127.0.0.1:8000/app/kabeeri-demo-app/home`

## Basic Admin Flow (V1)

1. Login to `/admin` with demo account or create a user.
2. Go to `Organizations` and create/select an organization.
3. Go to `Apps` and create an app under that organization.
4. Go to `Content > Content Types` and ensure `Page` exists.
5. Go to `Content > Content Entries` and create a draft page.
6. Open the content entry and click `Publish`.
7. Open the public URL `/app/{app-slug}/{page-slug}`.

## Testing & Quality

Run tests:

```powershell
php artisan test
```

Run formatter:

```powershell
vendor/bin/pint
```

Check formatting only:

```powershell
vendor/bin/pint --test
```

## V1 Feature Summary

- Core tenancy + scoped policies
- Filament resources grouped by:
  - `Core`
  - `Organizations`
  - `Apps`
  - `Content`
  - `Media`
  - `Rabet Foundation`
  - `System`
- Read-only activity logs with tenant isolation
- Tenant-aware settings and feature flag override controls
- V1 smoke + security test coverage

## More Docs

- [MODULES.md](MODULES.md)
- [docs/V1_LOCAL_SETUP.md](docs/V1_LOCAL_SETUP.md)
- [docs/V1_DEMO_SEED.md](docs/V1_DEMO_SEED.md)
