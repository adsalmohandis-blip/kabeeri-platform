# KABEERI V13 Mall, Customer, Partner, and Network UX

Created on 2026-05-07.

## Goal

V13 makes the external non-developer surfaces understandable and testable: Kabeeri Mall, customer portal, agency/partner/marketer paths, Work Network, Academy, talent, claim/report, trust, moderation, and legal verification journey.

## Product Boundary

- Kabeeri Mall is public discovery.
- Kabeeri Marketplace is internal extensions.
- Mall pages must not explain package install permissions or signing as if a visitor is installing a plugin.
- Marketplace pages must not behave like public Mall listing lead flows.

## Implemented Mall Surfaces

Existing Mall routes were upgraded instead of replaced:

- `/mall`.
- `/mall/businesses`.
- `/mall/businesses/{business}`.
- `/mall/products`.
- `/mall/products/{product}`.
- `/mall/services`.
- `/mall/services/{service}`.
- `/mall/courses`.
- `/mall/courses/{course}`.
- `/mall/talent`.
- `/mall/talent/{talent}`.
- `/mall/travel`.
- `/mall/travel/{listing}`.

New V13 routes:

- `/mall/search`.
- `/mall/trust`.
- `/mall/claim-report`.

## Trust Model

V13 explains trust as a system, not a decorative badge:

- Published with consent.
- Rabet verification.
- Moderation reviewed.
- Reputation snapshot.

Mall listing pages should explain claim/report paths, public contact safety, and the difference between public listing data and internal admin data.

## Customer Portal

Customer portal routes:

- `/customer`.
- `/customer/theme-plugins`.
- `/customer/quick-setup`.

The customer flow is:

- Workspace readiness.
- Theme and plugin selection.
- Products/services setup.
- Trust and Mall publication.
- Launch and measure.

## Partner and Marketer Paths

Partner routes:

- `/partners`.
- `/partners/agency-profile`.
- `/partners/storefront`.
- `/partners/referrals`.
- `/partners/campaigns`.
- `/partners/legal-verification`.

Partner paths include:

- Agency partner.
- Marketer.
- Partner storefront.
- Legal verification partner.

Referral dashboards are intentionally placeholders for commissions until billing settlement, payout, and tax policy are implemented.

## Work Network and Academy

Network routes:

- `/network`.
- `/network/talent-path`.

V13 frames talent as a professional path with:

- Professional profile.
- Talent listing.
- Skills evidence.
- Academy badges.
- Public consent.
- Moderation and report flow.

## Admin Handoff

V13 public surfaces hand off governance work to existing admin routes:

- Moderation cases.
- Reviews.
- Agency partners.
- Partner storefronts.

## Implementation Artifacts

- `config/kabeeri_external.php`.
- `App\Support\Ui\V13ExternalExperience`.
- `App\Http\Controllers\Web\ExternalPortalController`.
- `resources/views/external/v13-page.blade.php`.
- `resources/views/mall/layout.blade.php`.
- Upgraded Mall Blade views.
- `tests/Feature/V13ExternalExperienceTest.php`.
