# KABEERI V12 Marketplace and Developer UX

Created on 2026-05-07.

## Goal

V12 makes themes, plugins, packages, connectors, creators, and developers understandable and actionable. It turns the developer economy from a hidden technical feature into a first-class product path with catalog browsing, manifest documentation, lifecycle status, QA, governance, licensing, and revenue-share explanation.

## Runtime Boundary

- Current implementation: Laravel Blade bridge.
- Long-term commercial theme runtime: Next.js + React + TypeScript + Tailwind.
- Laravel owns backend logic, APIs, permissions, signing records, governance, admin review, package data, and audit trails.
- Filament remains the internal review/governance/admin runtime.
- V12 must not treat Blade as the permanent public theme runtime.

## Marketplace vs Mall

KABEERI Marketplace and KABEERI Mall are separate products:

- Marketplace: internal extension economy for owners, admins, developers, agencies, platform operators, themes, plugins, connectors, AI skills, templates, and industry solutions.
- Mall: external public discovery for businesses, products, services, talent, travel, courses, verified companies, and public leads.

Marketplace install/update UX must show:

- Requested permissions.
- Compatibility.
- Dependencies.
- Signing status.
- License.
- Backup and rollback expectations.
- Safe uninstall.
- Activity/audit expectations.

## Implemented Routes

Marketplace:

- Marketplace Home: `/marketplace`.
- Theme Catalog: `/marketplace/themes`.
- Theme Detail: `/marketplace/themes/{theme}`.
- Theme Recipes: `/marketplace/theme-recipes`.
- Plugin Bundle Catalog: `/marketplace/plugins`.
- Plugin Detail: `/marketplace/plugins/{package}`.
- Licensing and Revenue Share: `/marketplace/licensing`.
- Governance: `/marketplace/governance`.
- Review Status: `/marketplace/review-status`.

Developer Portal:

- Developer Portal: `/developers`.
- Developer Onboarding: `/developers/onboarding`.
- Theme Builder Documentation: `/developers/docs/themes`.
- Plugin Manifest Documentation: `/developers/docs/plugin-manifest`.
- Connector SDK Documentation: `/developers/docs/connectors`.
- Submission Checklist: `/developers/submission-checklist`.
- Listing Management: `/developers/listings`.
- Sales and Usage: `/developers/sales`.
- Developer Profile and Creator Page: `/developers/profile`.
- QA Center: `/developers/qa`.

## Developer Workflow

V12 models the expected package workflow:

- Choose identity.
- Create publisher account.
- Build manifest.
- Run local checks.
- Submit for review.
- Publish and support.

Lifecycle statuses:

- draft.
- submitted.
- under_review.
- needs_changes.
- approved.
- published.
- suspended.
- deprecated.
- removed.

Every lifecycle status must show an owner and next action. A developer should not see a vague `pending` state.

## Manifest Model

V12 documents manifest families:

- Theme manifest: runtime, components, sections, API contracts, mobile profile, RTL, SEO, performance budget, license, and support.
- Plugin manifest: permissions, dependencies, migrations, uninstall, webhooks, jobs, rate limits, compatibility, license, and support.
- Connector manifest: provider, scopes, auth type, sync objects, webhooks, rate limits, conflict strategy, secrets policy, rollback, and support.

## QA Gates

Theme QA includes:

- Manifest validity.
- RTL/LTR pass.
- Responsive pass.
- Accessibility pass.
- SEO metadata.
- Performance budget.
- Demo import and rollback.
- API-only rendering.
- No business logic inside theme.
- Mobile profile.
- License metadata.

Plugin QA includes:

- Declared permissions.
- Declared dependencies.
- No hidden data access.
- Safe migrations.
- Safe uninstall.
- Audit logs.
- Rate limits.
- Automated tests.
- Security scan.
- Performance impact.
- Compatibility matrix.
- No secrets logging.
- Signing.
- Support policy.

## Next.js Component Library Foundation

V12 defines the target component library foundation:

- Next.js App Router.
- React components.
- TypeScript props.
- Tailwind tokens.
- Direction provider.
- Locale-aware typography.
- Component prop contracts.
- Section registry.
- Manifest resolver.

Manifest section keys map to React components. Laravel provides data through APIs. Themes do not own business logic.

## Licensing and Revenue Share

V12 represents marketplace monetization as:

- Free/community.
- Paid theme.
- Plugin subscription.
- Agency pack.
- Enterprise contract.

Developer dashboards should eventually expose:

- Gross sales.
- Refunds.
- Platform share.
- Tax hold.
- Support SLA.
- Payout status.

## Implementation Artifacts

- `config/kabeeri_marketplace.php`.
- `App\Support\Ui\V12MarketplaceExperience`.
- `App\Http\Controllers\Web\MarketplaceDeveloperController`.
- `resources/views/marketplace/v12-page.blade.php`.
- `tests/Feature/V12MarketplaceExperienceTest.php`.
