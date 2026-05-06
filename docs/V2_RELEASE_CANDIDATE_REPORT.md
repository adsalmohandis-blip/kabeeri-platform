# KABEERI V2 Release Candidate Check

Date: 2026-05-06
Status: Ready for owner verification.

## Release Candidate Gates

- Database foundations: present for CMS menus, redirects, forms, migration jobs, external sources, CSV imports, products, variants, carts, orders, coupons, payment methods, themes, and packages.
- Backend services: present for WordPress import preview/import/mapping/report/rollback, redirect resolution, forms/lead capture, package governance, commerce lite, CSV feed preview, and WooCommerce listing preview.
- Admin resources: V2 resources added for operational visibility and safe management.
- Demo readiness: `V2DemoSeeder` creates practical CMS, commerce, migration, source registry, CSV, and order data.
- Security: V2 resources are tenant-scoped and unauthenticated access returns empty queries.
- Tests: targeted V2 suite passes.

## Known Deliberate Limits

- No live WooCommerce remote sync is enabled in V2; only safe preview parsing and event recording are included.
- Import job execution remains outside Filament create/edit actions to avoid accidental destructive migrations.
- Payment gateway integrations are placeholders only; real payment gateways belong to a later payment-provider task.

## Owner Verification Checklist

- Run `php artisan migrate:fresh --seed` in a disposable database.
- Open `/admin` and confirm the V2 CMS, V2 Migration, and V2 Commerce groups render.
- Confirm demo product, coupon, order, form, menu, redirect, and import records are visible to the demo admin only.
- Run `php artisan test --filter=V2` and optionally the full `php artisan test` suite.
