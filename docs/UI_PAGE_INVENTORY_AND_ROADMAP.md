# KABEERI UI Page Inventory and Execution Roadmap

Created on 2026-05-06.

## 1. Technology Direction

Current project stack from local project files:

- Backend and routing: Laravel 13.
- Internal admin UI: Filament 5 with Livewire-powered resources.
- Public UI: Blade views under `resources/views`.
- Frontend assets: Vite 8, Tailwind CSS 4 via `@tailwindcss/vite`.
- Test baseline: PHPUnit via `php artisan test`.

Recommended UI direction:

- Use Filament for all internal admin dashboards, CRUD resources, review queues, governance screens, and operational workflows.
- Use Blade plus Vite/Tailwind for public marketing, onboarding, marketplace, Mall, public content, developer, partner, mobile web, and documentation pages.
- Keep public pages fast, crawlable, and Arabic/RTL-first.
- Keep unsafe actions behind explicit admin flows, confirmations, policies, and tests.
- Do not introduce a separate SPA framework until a documented requirement demands it.

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

### V9 UI Foundation and Navigation

Goal: define the visual system, shell structure, page inventory, and navigation architecture.

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
- Every new public page must be mobile-responsive and RTL-safe.
- Every new admin page must respect policies/tenant boundaries.
- Every flow must have smoke tests or feature tests where possible.
- Do not mark a UI task `codex_done` until the route/page renders and tests pass.
- Keep task tracker truth aligned with actual implementation state.
