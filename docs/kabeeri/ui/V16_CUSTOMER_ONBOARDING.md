# KABEERI V16 Customer Onboarding

V16 implements the ordinary customer path that was missing after V15.

## What V16 Adds

- Public customer entry: `/start`.
- Customer web auth, separate from Filament admin: `/login`, `/register`, `/logout`.
- Guided onboarding: `/customer/onboarding`.
- Workspace provisioning using existing backend foundations:
  - User.
  - UserProfile.
  - Organization.
  - optional Company draft.
  - Site/App.
  - selected Theme.
  - ThemeSetting records.
  - starter CMS content.
- Authenticated customer dashboard: `/customer/dashboard`.
- Customer app detail: `/customer/apps/{site}`.
- Capability upgrade path for customer owner, developer/creator, marketer/partner, implementation builder, and needs-builder-help.

## Important Naming

V16 separates two developer concepts:

- Theme/plugin developer: builds platform extensions for Marketplace.
- Kabeeri Builder / Implementation Partner: helps customers build their app, company, content, services, and setup inside the platform.

## Boundaries

- `/customer` remains the public V13 customer portal explanation page.
- `/customer/dashboard` is the authenticated customer workspace.
- `/admin/login` remains Filament admin login.
- `/login` is customer web login.
- Theme install in V16 assigns an approved starter theme and settings only. It does not execute arbitrary package code.

## Customer Flow

1. Visitor opens `/start`.
2. Visitor chooses a path such as business owner, store owner, service business, creator/developer, marketer/partner, or needs builder.
3. Visitor registers through `/register`.
4. Customer completes `/customer/onboarding`.
5. System provisions workspace, app, theme, settings, and starter content.
6. Customer lands on `/customer/dashboard`.
7. Customer can update profile capabilities and request builder help.

## Verification Commands

```bash
php artisan test --filter=V16CustomerExperienceTest
php artisan test --filter=RootDashboardPageTest
vendor/bin/pint --test
php artisan test
```