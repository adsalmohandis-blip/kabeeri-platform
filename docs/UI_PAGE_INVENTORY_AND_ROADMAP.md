# KABEERI UI Page Inventory and Execution Roadmap

Created on 2026-05-06.

## 1. Technology Direction

Current project stack from local project files:

- Backend and routing: Laravel 13.
- Internal admin UI: Filament 5 with Livewire-powered resources.
- Current public fallback UI: Blade views under `resources/views`.
- Current Laravel asset pipeline: Vite 8, Tailwind CSS 4 via `@tailwindcss/vite`.
- Test baseline: PHPUnit via `php artisan test`.

Recommended UI direction from KBR v1.6.6 docs:

- Laravel remains API-first modular monolith for domain logic, DB, RBAC, jobs, and APIs.
- Filament remains the primary internal admin UI.
- Public web themes should move to Next.js + React + TypeScript + Tailwind.
- Mobile direction remains Flutter shell consuming Mobile Theme Profiles and API Manifest.
- Desktop direction remains Electron + React + TypeScript + SQLite.

Execution direction for this repository:

- Use Filament for all internal admin dashboards, CRUD resources, review queues, governance screens, and operational workflows.
- Use Next.js + React + TypeScript + Tailwind as the primary public web/theme runtime according to KBR v1.6.6 Technology Governance. Use Blade only as current fallback/bridge pages until the Next.js runtime is scaffolded.
- Keep public pages fast, crawlable, and Arabic/RTL-first.
- Keep unsafe actions behind explicit admin flows, confirmations, policies, and tests.
- Do not build the long-term public theme marketplace as Laravel Blade-only; v1.6.6 explicitly separates Laravel backend/admin from the Next.js public web theme runtime.

## 2. Primary UI Sections

### A. Internal Admin UI

This section is for platform owner, super admin, organization owner/admin, site admin, operations admin, marketplace admin, developer ecosystem admin, security/compliance admin, support admin, and partner/program admin.

Admin UI must answer:

- Is the system healthy?
- What needs action?
- Which tenant, site, module, listing, or workflow needs review?
- What can this admin role safely do?
- What should be blocked, approved, published, archived, or escalated?

### B. External User UI

This section is for public visitors, business customers, enterprise buyers, developers, marketers, agencies, partners, creators, learners, and Mall users.

External UI must answer:

- What is KABEERI?
- Which path is right for me?
- How do I onboard?
- What can I build, sell, publish, or manage?
- What plan or subscription fits me?
- How do themes, plugins, developers, marketers, and partners work together?

## 3. Internal Admin UI Inventory

### Admin Shell and System Check

- Admin login page.
- Admin dashboard.
- System check dashboard.
- Task tracker status dashboard.
- Migration/database status dashboard.
- Release readiness dashboard.
- Module health overview.
- Feature flags overview.
- Activity/audit timeline.
- Environment readiness checklist.

### Platform Super Admin

- Organizations list/create/view/edit.
- Organization memberships and member management.
- Companies list/create/view/edit.
- Sites/apps list/create/view/edit.
- Settings list/create/view/edit.
- Feature flags list/view/create/edit.
- Feature flag overrides.
- Roles and permissions overview.
- Activity logs list/view.
- Audit logs/security events overview.
- Notifications center.

### Organization Owner and Organization Admin

- Organization home dashboard.
- Team and permissions dashboard.
- Company/site switcher.
- Workspace setup checklist.
- Operating mode settings.
- Site settings.
- Content publishing review.
- Business profile and verification status.
- Billing/module usage overview.

### Site Admin and Content Admin

- Content types list/create/view/edit.
- Content entries list/create/view/edit.
- Content revisions timeline.
- Taxonomies list/create/view/edit.
- Menus and menu items.
- Redirects and redirect suggestions.
- SEO metadata editor.
- Sitemap/robots preview.
- Forms list/create/edit.
- Form fields builder.
- Form submissions inbox.
- Media library list/create/view/edit.
- Theme selection and theme settings.
- Demo content import preview.

### Commerce and Mall Admin

- Product categories.
- Products list/create/view/edit.
- Product images/options/variants.
- Carts and draft order overview.
- Orders and order items.
- Coupons.
- Payment methods and manual payment placeholders.
- Mall publication consents.
- Mall sync sources and sync events.
- Mall mirror businesses.
- Mall mirror products.
- Mall mirror services.
- Mall mirror courses.
- Mall mirror talent.
- Travel and tourism listings.
- Marketplace catalog items.
- Reviews and reputation snapshots.
- Moderation cases and flags.

### Business Operations Admin

- Contacts list/create/view/edit.
- Leads list/create/view/edit.
- Lead sources and scoring.
- Sales pipelines and stages.
- CRM activities and customer timeline.
- Service requests.
- Quotations list/create/view/edit.
- Invoices list/create/view/edit.
- Payments list/create/view/edit.
- Inventory items list/create/view/edit.
- Warehouses list/create/view/edit.
- Stock movements.
- Suppliers and supplier contacts.
- Purchase orders list/create/view/edit.
- Goods receipts list/create/view/edit.
- Accounts and chart of accounts.
- Journal entries and journal lines.
- Projects and tasks.
- Workflow definitions and runs.
- Approval requests list/create/view/edit.
- Report definitions and snapshots.
- Dashboard widgets.

### People and Work Admin

- Employee profiles list/create/view/edit.
- Departments list/create/view/edit.
- Positions.
- Employee evaluations.
- Work Network profiles.
- Academy badges and awards.
- Agency partner profiles.
- Partner storefronts.
- Creator profiles.
- Growth referrals.

### Integration and Developer Platform Admin

- External sources.
- CSV imports.
- WordPress import jobs, batches, records, mappings, reports, warnings, rollback.
- Integration connectors.
- Integration credentials with vault/reference-only display.
- External object links.
- Integration sync jobs/logs.
- Webhook/rate-limit/conflict review screens.
- Developer marketplace listings.
- Package catalog.
- Plugin bundles.
- Installed packages.
- Package signing/review queue.
- Connector SDK registry.
- Universal sync preview profiles.
- API gateway route overview.
- Public API version/docs records.

### Enterprise, Security, Data, and GRC Admin

- Enterprise security dashboard.
- MFA/SSO/SCIM configuration records.
- SIEM export streams and queued security export events.
- Data ingestion pipelines.
- Data marts, metric definitions, BI dashboards, dictionary, and lineage.
- GRC risks.
- GRC policies, obligations, controls, audits, evidence, remediations.
- Privacy retention policies.
- Performance and queue profile overview.

### Mobile and Desktop Admin

- Mobile app configs.
- Mobile theme profiles.
- Mobile API manifests.
- Mobile devices.
- Mobile push token registry with hash-only display.
- Mobile auth token registry with hash-only display.
- Desktop clients.
- Desktop sync sessions.
- Desktop outbox operations.
- Desktop sync conflicts.
- Desktop file queue items.

## 4. External User UI Inventory

### Public Marketing and Onboarding

- Public landing page.
- Audience selector.
- Business owner path.
- Enterprise buyer path.
- Developer path.
- Marketer/partner path.
- Pricing/subscription overview.
- Plan comparison.
- Onboarding start.
- Workspace setup wizard.
- Industry/use-case templates.
- FAQ and trust page.
- Contact/sales inquiry.

### Public Mall

- Mall home.
- Business directory index.
- Business directory detail.
- Products index.
- Product detail.
- Services index.
- Service detail.
- Courses index.
- Course detail.
- Talent index.
- Talent detail.
- Travel index.
- Travel listing detail.
- Mall search and filters.
- Listing submit/claim interest.

### Public Site and CMS Rendering

- Public content page: `/app/{site}/{contentEntry}`.
- Themed site layout.
- Landing page template.
- About page template.
- Services page template.
- Blog/post page template.
- Contact page template.
- Form submission public UI.
- SEO-friendly sitemap and robots.

### Customer Portal

- Account dashboard.
- Workspace onboarding checklist.
- Site setup.
- Theme selection and preview.
- Plugin selection and bundle recommendation.
- Products/services quick setup.
- CRM/import quick start.
- Billing/subscription view.
- Team invitation flow.
- Support requests.

### Developer Portal

- Developer landing.
- Developer account onboarding.
- Theme builder documentation.
- Plugin manifest documentation.
- Connector SDK documentation.
- Submission checklist.
- Package/theme upload flow.
- Review status page.
- Marketplace listing management.
- Sales/usage dashboard.
- Developer payout/revenue-share placeholder view.

### Marketer and Partner Portal

- Partner landing.
- Partner onboarding.
- Agency profile setup.
- Partner storefront builder.
- Service catalog setup.
- Referral link/dashboard.
- Lead handoff and CRM view.
- Campaign resources.
- Commission/revenue placeholder status.
- Academy/work network progress.

### Mobile and Desktop Facing UI

- Mobile public config consumer screens.
- Mobile auth/register/login UI.
- Mobile app onboarding explanation.
- Desktop client registration explanation.
- Desktop sync status page.
- File queue status page.

## 5. Proposed UI Versions

### V9 UI Foundation, Navigation, and Frontend Runtime Decision

Goal: define the visual system, shell structure, page inventory, navigation architecture, and the transition plan from Blade fallback pages to the documented Next.js public theme runtime.

### V10 Internal Admin System Check and Core Admin UX

Goal: make the admin area understandable for platform owner and admin roles.

### V11 Public Marketing, Audience, and Onboarding UX

Goal: make the public-facing story, audience paths, onboarding, and subscriptions clear.

### V12 Themes, Plugins, Marketplace, and Developer Portal UX

Goal: make themes/plugins/developer marketplace understandable and actionable.

### V13 Mall, Customer Portal, Marketer, Partner, and Network UX

Goal: make external customer, Mall, partner, agency, marketer, Work Network, and Academy flows usable.

### V14 UI Quality, Accessibility, Responsive, Docs, and Release Candidate

Goal: make the entire UI pass quality checks before production/staging release.

## 6. Version Execution Rules

- Each version must start with rules/boundaries and readiness check tasks.
- Every implemented page must have a named route or Filament resource/page.
- Every new public page must be mobile-responsive and RTL-safe. Long-term public pages should target the Next.js theme runtime; Blade pages are allowed only as fallback/bridge until that runtime exists.
- Every new admin page must respect policies/tenant boundaries.
- Every flow must have smoke tests or feature tests where possible.
- Do not mark a UI task `codex_done` until the route/page renders and tests pass.
- Keep task tracker truth aligned with actual implementation state.

## 7. KBR v1.6.6 Documentation Alignment

User-provided source path reviewed:

`D:\My Project Ideas\Kabeeri\kabeeri_professional_knowledge_system_v1.6.6_task_tracking_auto\KBR_v1.6.6`

Key source documents used:

- `02_FOUNDATION_ARCHITECTURE/14_UI_UX_Admin_Navigation_AR.docx`
- `18_TECHNOLOGY_GOVERNANCE/01_Technology_Stack_Governance_Decision_Records_AR.docx`
- `18_TECHNOLOGY_GOVERNANCE/02_Frontend_Backend_Separation_Architecture_AR.docx`
- `11_THEME_PLUGIN_MARKETING_ECOSYSTEM/12_Design_System_UI_Components_Library_AR.docx`
- `11_THEME_PLUGIN_MARKETING_ECOSYSTEM/16_Marketplace_Licensing_Pricing_Revenue_Share_AR.docx`
- `08_DEVELOPER_ECONOMY/01_Theme_Plugin_Developer_Economy_Architecture_AR.docx`

Important alignment changes:

- The internal admin must be context-first, permission-aware, role-based, and progressively disclosed.
- The public web/theme runtime should be separated from Laravel and built with Next.js + React + TypeScript + Tailwind.
- Laravel Blade should not be treated as the primary public theme runtime.
- The theme/plugin marketplace must support manifests, permissions, review, compatibility checks, licensing metadata, and revenue share rules.
- UI components must support Arabic-first typography, RTL/LTR, accessibility, semantic HTML, and reusable component primitives such as buttons, inputs, cards, tabs, modals, hero, pricing, FAQ, product cards, service grids, and profile cards.