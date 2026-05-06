# KABEERI V9 UI Foundation

Created on 2026-05-06.

## Decision

V9 establishes the UI foundation before broad page production. The goal is to prevent beautiful chaos: every screen must know its runtime, audience, context, route, permissions, and quality gate before implementation.

## Runtime Boundary

- Laravel owns backend domain logic, database, RBAC, policies, jobs, events, REST JSON APIs, and OpenAPI-ready contracts.
- Filament owns internal admin UI, dashboards, review queues, governance screens, and operational CRUD.
- Blade is a bridge/fallback for the current root dashboard, Mall fallback pages, and current public content rendering.
- Next.js + React + TypeScript + Tailwind is the long-term public theme/runtime target.
- Flutter consumes mobile config/theme/manifest APIs.
- Electron consumes desktop registration and sync APIs.

## Non Goals

- Do not build a full public theme marketplace in Blade.
- Do not start random public pages before the route registry and audience journey are clear.
- Do not expose all platform modules to public visitors at once.
- Do not treat hidden UI as security. Policies and permissions remain mandatory.

## Design System

The shared design tokens live in `config/kabeeri_ui.php` and `resources/css/app.css`.

Token families:

- Colors: ink, paper, sand, bronze, olive, sage, clay, date, sky, success, warning, danger, neutral.
- Typography: Arabic-first using IBM Plex Sans Arabic and Almarai, with Latin/system fallback.
- Spacing: xs to 3xl.
- Radius: sm, md, lg, xl, pill.
- Shadows: soft, strong, focus.
- Breakpoints: mobile, tablet, desktop.
- Motion: purposeful disclosure and page-load motion only.

Required component families:

- Button, Input, Select, Textarea, Card, Badge, Tabs, Accordion, Modal, Dropdown, Navbar, Footer.
- Hero, ServicesGrid, ProductCard, CourseCard, BookingForm, ReviewCard, PricingTable, FAQ, CTASection, Gallery, MapBlock, ProfileCard.

## Admin Spaces

Admin UI is organized as spaces, not one huge menu.

- Personal Space.
- Organization Workspace.
- Kabeeri App / Site Admin.
- Company Admin / Rabet OS.
- Commerce Admin.
- ERP Admin.
- Kabeeri Mall Console.
- Talent Console.
- Kabeeri Teams.
- Developer Console.
- Billing Console.
- Platform Admin.

Every admin page must answer:

- What context am I in?
- What needs action?
- What am I allowed to do?
- What is blocked and why?
- What is the safest next step?

## Public Audience Journeys

The public UI must be progressive. Do not sell the whole platform in one breath.

- Business owner: website, commerce, CRM, operations, Mall visibility.
- Agency: client apps, kits, delivery, partner revenue.
- Developer/Creator: package/theme, review, signing, marketplace revenue.
- Marketer/Partner: referral, campaign, lead handoff, revenue visibility.
- Enterprise buyer: governance, security, integrations, GRC/BI.
- Mall visitor: discover, compare, trust, lead/order/contact.

## Naming Rules

- Use Kabeeri App for customer-facing website/store/landing/blog/portal copy.
- Use Site when the screen is technical/admin/database-oriented.
- Organization is the workspace/admin scope.
- Company is the legal/business entity.
- Business Profile is the public Mall appearance.
- Professional Profile is the public talent appearance.
- Kabeeri Marketplace is internal extensions.
- Kabeeri Mall is public discovery.

## Permission-Aware UI

- Hide actions the user cannot use, but never rely on hiding for security.
- Explain blocked actions when helpful.
- Sensitive actions require explicit policies, confirmations, and auditability.
- Examples: billing.manage, company.verification.approve, finance.journal.post, users.role.assign, plugin.install, ai.apply_sensitive_change.

## Release Gate

V9 can be considered Codex complete when:

- `config/kabeeri_ui.php` exists and contains runtime decisions, tokens, spaces, route registry, standards, and boundaries.
- `App\Support\Ui\V9UiFoundation::isReleaseCandidateReady()` returns true in tests.
- Current route registry routes exist.
- V9 docs exist.
- `php artisan test --filter=V9UiFoundationTest` passes.
- `vendor/bin/pint --test` passes.
- `npm run build` passes.