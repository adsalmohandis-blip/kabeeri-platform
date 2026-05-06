# KABEERI

KABEERI is a Laravel + Filament multi-tenant platform foundation. V1 provides the stable organization/app/CMS core, V2 expands CMS, migration, packages, themes, and Commerce Lite, V3 adds Business Operations foundations, and V4 adds cloud/Mall/partner/trust marketplace foundations.

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
- V3 operations demo records
- V4 Mall, moderation, marketplace, partner, trust, academy, and referral demo records

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

## V2 Feature Summary

- CMS menus, redirects, SEO fields, sitemap, and robots output.
- Forms, submissions, optional lead capture, and simple contact inbox states.
- WordPress migration foundation: jobs, XML preview, mappings, reports, warnings, redirects, and rollback.
- Official theme/package catalogs, recipes, demo importer, manifest validation, bundles, and safe official package installs.
- Commerce Lite products, variants, carts, draft orders, coupons, and manual payment placeholders.
- External source registry and preview-only CSV/WooCommerce-like parsers.

## V3 Feature Summary

- CRM contacts, leads, lead sources/scoring, pipelines, activities, customer timeline, and service requests.
- Quotations, invoices, quote-to-invoice conversion, manual payments, and receipts.
- Inventory items, warehouses, stock movements, suppliers, purchase orders, and goods receipts.
- Starter chart of accounts and journal entries.
- Employee profiles, departments, positions, evaluations, projects, tasks, workflows, approvals, reports, and dashboard widgets.
- V3 smoke/security/performance test coverage.

## V4 Feature Summary

- Cloud-site operational records, domains, backups, and health checks.
- Public Mall mirroring with opt-in publication consent, preview-first sync events, public browsing, and `/mall` navigation.
- Moderation cases, flags, queue service, reviews, reputation snapshots, trust badges, and security report.
- Internal marketplace catalog over official packages/themes, theme store filters, and package installation governance.
- Legal partner, agency partner, creator/publisher, Work Network, Academy badge, referral, and partner storefront draft foundations.
- V4 demo seed data, smoke tests, Filament list resources for key V4 surfaces, and fresh seed verification.

Public Mall entry point:
- `http://127.0.0.1:8000/mall`

## More Docs

- [MODULES.md](MODULES.md)
- [docs/V1_LOCAL_SETUP.md](docs/V1_LOCAL_SETUP.md)
- [docs/V1_DEMO_SEED.md](docs/V1_DEMO_SEED.md)
- [docs/V2_IMPLEMENTATION_NOTES.md](docs/V2_IMPLEMENTATION_NOTES.md)
- [docs/V3_IMPLEMENTATION_NOTES.md](docs/V3_IMPLEMENTATION_NOTES.md)
- [docs/V3_DEMO_SEED.md](docs/V3_DEMO_SEED.md)
- [docs/V4_IMPLEMENTATION_NOTES.md](docs/V4_IMPLEMENTATION_NOTES.md)
- [docs/V4_DEMO_SEED.md](docs/V4_DEMO_SEED.md)
- [docs/V4_SECURITY_PRIVACY_REPORT.md](docs/V4_SECURITY_PRIVACY_REPORT.md)
