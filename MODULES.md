# KABEERI Modules

This document describes the active module foundation through V3 and what remains intentionally out of scope.

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

## Active V2 Domains

`CMS Expansion`:
- Menus, redirects, SEO fields, sitemap, and robots output.

`Forms and Leads`:
- Forms, form fields, submissions, optional lead capture, and contact inbox status handling.

`WordPress Migration`:
- Import jobs, batches, records, XML parser, preview summaries, author/taxonomy/content/media/SEO mapping, redirect suggestions, shortcode warnings, reports, and rollback foundation.

`Themes and Packages`:
- Official theme catalog, theme app recipes, demo importer, official package catalog, manifest validation, plugin bundles, and safe official package installation records.

`Commerce Lite and Integrations`:
- Products, variants, carts, draft orders, coupons, manual payment methods, external source registry, CSV preview parsing, and WooCommerce-like feed preview parsing.

## Active V3 Domains

`Business Operations / CRM`:
- Contacts, leads, lead sources, scoring, sales pipelines, CRM activities, customer timeline, and service requests.

`Sales`:
- Quotations, quotation lifecycle, invoices, quote-to-invoice conversion, manual payments, and receipts.

`Inventory and Purchasing`:
- Inventory items, warehouses, stock movements, suppliers, purchase orders, and goods receipts.

`Accounting Starter`:
- Starter chart of accounts and balanced journal entries.

`People and Projects`:
- Employee profiles, departments, positions, evaluations, business projects, and tasks.

`Workflow and Reporting`:
- Workflow definitions, runs, hooks, approval requests, report definitions, snapshots, and dashboard widgets.

## Filament Admin Navigation (V2/V3)

Additional V2/V3 groups include:

- Forms & Leads
- Migration
- Themes & Packages
- Commerce Lite
- Integrations
- CRM
- Sales
- Inventory
- People
- Workflow
- Reports

## V3 Boundaries

Allowed in V3:
- ERP-lite internal operations.
- Manual payments and receipts.
- Inventory and purchasing foundations.
- Accounting starter records.
- Basic workflow and approval records.

Not allowed in V3:
- Real payment gateway integrations.
- Payroll, tax engine, POS, shipping fulfillment, or advanced inventory costing.
- Live external system sync.
- Advanced AI automation or forecasting.
- Public marketplace, affiliate payouts, Academy, Work Network, or partner operations.

## Active V4 Domains

`Cloud Operations Foundation`:
- Cloud sites, domains, backups, and health checks as record-only operational tracking.

`Public Mall`:
- Publication consents, sync sources/events, preview-first CSV and WordPress/WooCommerce listing previews.
- Mall mirror businesses, products, services, courses, talent, and travel listings.
- Public browsing routes and `/mall` navigation for published mirrors only.

`Moderation and Trust`:
- Moderation cases, reports/flags, queue service, reviews/ratings, reputation snapshots, Rabet trust badges, and security/privacy report.

`Marketplace and Theme Store`:
- Internal marketplace catalog items for packages/themes.
- Theme store filters over approved internal marketplace themes.
- Package installation governance for approved listings, dependencies, and compatibility.

`Partner and Network Foundations`:
- Legal partner profiles, verification assignment workflow, agency partner accreditation, agency dashboard snapshots.
- Creator/publisher profiles, Work Network profiles, Academy badge awards, Growth Referral Lite, and partner storefront/catalog sharing drafts.

## Filament Admin Navigation (V4)

V4 added list resources for:

- Moderation Cases
- Reviews
- Marketplace Catalog
- Agency Partners
- Partner Storefronts

These resources use tenant-scoped queries where records belong to organizations. Full workflow UI remains intentionally incremental.

## V4 Boundaries

Allowed in V4:
- Record-only cloud operations.
- Public Mall mirrors with opt-in publication and status controls.
- Preview-first external listing sync foundations.
- Internal marketplace governance and official package/theme catalog surfaces.
- Partner, Work Network, Academy, and referral foundations without payouts.

Not allowed in V4:
- Live unsafe two-way external sync.
- Real payment gateways, card storage, payouts, commissions, escrow, or revenue share.
- Legal operations casework, full agency operations, full Academy LMS, or job marketplace workflows.
- SSO/MFA/SIEM enterprise security tracks.
