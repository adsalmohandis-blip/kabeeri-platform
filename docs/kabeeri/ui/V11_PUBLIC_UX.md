# KABEERI V11 Public Marketing UX

Created on 2026-05-07.

## Goal

V11 makes the external audience understand what KABEERI does before they enter any admin workflow. It separates the internal system-check/admin experience from the public commercial story and gives each target audience a clear path, benefit, subscription context, and onboarding next step.

## Runtime Boundary

- Current implementation: Laravel Blade bridge.
- Target commercial public runtime: Next.js + React + TypeScript + Tailwind.
- Filament remains internal admin only.
- V11 does not replace the V9 runtime decision; it documents the migration path and proves the information architecture in Laravel first.

## Implemented Routes

The public page registry lives in `config/kabeeri_public.php`.

- Public Landing: `/public`.
- Audience Selector: `/for`.
- Business Owner Path: `/for/business-owners`.
- Enterprise Buyer Path: `/for/enterprise`.
- Developer and Creator Path: `/for/developers-creators`.
- Marketer and Partner Path: `/for/marketers-partners`.
- WordPress Alternative: `/wordpress-alternative`.
- Service Business Use Case: `/use-cases/service-business`.
- Use Cases and Templates: `/templates`.
- Onboarding Overview: `/onboarding`.
- Workspace Setup Wizard: `/onboarding/workspace-setup`.
- Pricing and Plans: `/pricing`.
- FAQ and Trust: `/trust`.
- Contact Sales: `/contact-sales`.

## Public Story

KABEERI must not be presented to customers as a giant feature list. The V11 story is progressive:

- Start as a clearer website/CMS and WordPress alternative.
- Add commerce only when the business is ready.
- Add Rabet trust and company identity.
- Grow into CRM, invoices, operations, workflows, and reporting.
- Publish moderated offerings into Kabeeri Mall.
- Open the platform economy for developers, creators, agencies, marketers, and partners.

## Audiences

V11 defines five external audience families:

- Business Owner: wants launch, leads, commerce, operations, and Mall growth without plugin sprawl.
- Enterprise Buyer: wants governance, auditability, integrations, BI/GRC, permissions, and deployment discipline.
- Developer / Creator: wants to build themes, plugins, connectors, and templates for a marketplace economy.
- Marketer / Partner: wants referral paths, campaign handoff, CRM visibility, and revenue-share readiness.
- Mall Visitor: wants public discovery with trust and moderation.

## Onboarding Model

The onboarding flow is deliberately small-step:

- Audience and intent.
- Workspace.
- App recipe.
- Theme and modules.
- Team and permissions.
- Launch and grow.

The workspace setup wizard uses progressive disclosure. It should not show ERP, Marketplace install, or Mall publishing before the visitor has enough context.

## Pricing Model

V11 explains monetization as layers, not only as a pricing table:

- Subscription.
- Modules.
- Verification.
- Mall visibility.
- AI and automation.
- Marketplace revenue.

Plans currently represented:

- Free / Community.
- Starter / Pro.
- Business.
- Agency.
- Enterprise.

## Contact Sales Flow

`POST /contact-sales` validates the public inquiry and creates a CRM `Lead` with:

- Source: `public_v11_inquiry`.
- Status: `new`.
- Audience metadata.
- Plan interest metadata.
- Source page metadata.

The flow creates or reuses an internal public inquiry organization using the contact settings in `config/kabeeri_public.php`.

## Next.js Migration Notes

V11 keeps Blade as a bridge while documenting the public runtime target:

- Keep Blade bridge pages stable.
- Confirm API contracts for pricing, audience journeys, lead capture, Mall listings, theme profile, and navigation.
- Scaffold `apps/public-web`.
- Move marketing and onboarding first.
- Move Marketplace and Mall public pages later.
- Run V14 parity, accessibility, RTL/LTR, and responsive QA before replacing the bridge.

## Implementation Artifacts

- `config/kabeeri_public.php`.
- `App\Support\Ui\V11PublicExperience`.
- `App\Http\Controllers\Web\PublicMarketingController`.
- `resources/views/public/v11-page.blade.php`.
- `tests/Feature/V11PublicExperienceTest.php`.
