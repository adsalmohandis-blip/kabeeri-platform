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

## Active V5 Domains

`ERP Pro Foundation`:
- CRM Pro opportunity records over existing contacts, leads, pipelines, and stages.
- CLM basic contract records and helpdesk ticket records.
- Record-only commission plans, commission events, and partner payout review placeholders.

`Integration Hub`:
- Connector registry, credential vault references, external object links, preview sync jobs, and sync logs.
- Credentials store references only; raw API keys, tokens, and passwords remain out of database scope.
- Sync defaults to preview/queued records rather than unsafe live execution.

`Billing Usage Foundation`:
- Metered usage records by organization, module, meter, date, and optional billable source.

## V5 Boundaries

Allowed in V5:
- ERP Pro, CLM, helpdesk, commission, payout-review, integration hub, and billing usage foundations.
- Preview-first integration records and auditable sync logs.
- Partner payout placeholders with pending review and risk-check metadata.

Not allowed in V5:
- Raw secret storage, live unsafe sync, real payment rails, card storage, automatic payouts, escrow, or tax/legal finalization.
- Adding tenant columns directly to `users`.
- Running third-party connector code from marketplace submissions.

## Active V6 Domains

`Enterprise Security and Compliance`:
- MFA, SSO, SCIM, SIEM export streams, security event export queues, GRC policies, obligations, risks, controls, audit evidence, and remediation foundations.

`Developer and Integration Platform`:
- Public developer marketplace, package release governance, developer certifications/docs, universal connector SDK registry, universal sync profiles, API gateway routes, and versioned public API docs records.

`Data and BI Platform`:
- Ingestion pipelines, data marts, metric definitions, BI dashboards, data dictionary terms, and lineage links.

`Enterprise Mall and Industry Suites`:
- Advanced Mall sections for LMS, travel, RFQ, deals, properties, and assets.
- Industry solution templates plus ESG/EHS, PLM, manufacturing, retail, HCM/payroll, PMO, contact center, and legal/CLM advanced foundation records.

`AI, Work Network, and Marketplace Economics`:
- Guarded AI co-builder agents, AI skills marketplace listings, work network levels, agency operations, academy assessments, revenue-share rules, and payout batch placeholders.

## V6 Boundaries

Allowed in V6:
- Enterprise-grade record foundations, governance queues, preview/draft states, and admin visibility.
- Vault/reference fields for SSO, SCIM, SIEM, webhook, package signing, and AI marketplace governance.
- Performance/queue profiles and release-check records.

Not allowed in V6:
- Raw secrets, raw certificates, tokens, passwords, or card data in database columns.
- Live identity provisioning, external sync execution, AI code execution, automatic payouts, payroll execution, or legal/tax finalization.
- Adding tenant columns directly to `users`.

## Active V7 Domains

`Mobile Platform`:
- Mobile app configuration, theme profiles, API manifests, device registry, push token registry, public mobile APIs, and mobile auth APIs.

## V7 Boundaries

Allowed in V7:
- Backend foundations for mobile clients.
- JSON APIs for config, manifest, theme, registration, login, device registration, and push token registration.
- Hashed token storage for push and auth tokens.

Not allowed in V7:
- Native iOS/Android binaries.
- Raw push token or auth token storage.
- Adding tenant columns directly to `users`.

## Active V8 Domains

`Desktop Platform`:
- Desktop client registry, sync sessions, pull API, push dry-run API, outbox operations, conflict detection, and file queue records.

## V8 Boundaries

Allowed in V8:
- Backend foundations for desktop/offline clients.
- Read-oriented pull manifests, sync cursors, dry-run operation validation, auditable outbox records, and conflict records.
- File queue metadata with hash-based storage references.

Not allowed in V8:
- Native Windows/macOS/Linux binaries.
- Applying push payloads directly to domain tables.
- Raw file byte storage through the queue API.
- Silent conflict resolution or automatic overwrites.
