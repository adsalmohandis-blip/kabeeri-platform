# KABEERI UI Route and Page Registry

Created on 2026-05-06.

The source of truth for machine-readable registry data is `config/kabeeri_ui.php` under `route_registry`.

## Current Laravel Public Bridge

| Key | Route | URI | Runtime | Owner |
| --- | --- | --- | --- | --- |
| root_dashboard | home | / | Blade bridge | Platform |
| mall_home | mall.index | /mall | Blade bridge | Mall |
| mall_businesses | mall.businesses.index | /mall/businesses | Blade bridge | Mall |
| mall_products | mall.products.index | /mall/products | Blade bridge | Mall |
| mall_services | mall.services.index | /mall/services | Blade bridge | Mall |
| mall_courses | mall.courses.index | /mall/courses | Blade bridge | Mall |
| mall_talent | mall.talent.index | /mall/talent | Blade bridge | Mall |
| mall_travel | mall.travel.index | /mall/travel | Blade bridge | Mall |
| public_content_entry | public.content-entry.show | /app/{username}/{contentEntry} | Blade bridge | CMS |
| language_switch | language.switch | /language/{locale} | Blade bridge | Localization |

## Current Admin Routes

| Key | Route | URI | Runtime | Space |
| --- | --- | --- | --- | --- |
| admin_dashboard | filament.admin.pages.dashboard | /admin | Filament | Platform |
| admin_login | filament.admin.auth.login | /admin/login | Filament | Platform |
| plans | filament.admin.resources.plans.index | /admin/plans | Filament | Billing |
| organizations | filament.admin.resources.organizations.index | /admin/organizations | Filament | Organization |
| sites | filament.admin.resources.sites.index | /admin/sites | Filament | Site |
| companies | filament.admin.resources.companies.index | /admin/companies | Filament | Company/Rabet |

## Current API Contract Candidates

| Key | Route | URI | Consumer |
| --- | --- | --- | --- |
| mobile_manifest | mobile.manifest | /api/mobile/manifest | Flutter |
| mobile_config | mobile.config | /api/mobile/config | Flutter |
| mobile_theme | mobile.theme | /api/mobile/theme | Flutter |
| desktop_register | desktop.register | /api/desktop/register | Electron |
| desktop_pull | desktop.sync.pull | /api/desktop/sync/pull | Electron |
| public_web_manifest | public-web.manifest | /api/public-web/manifest | Next.js public runtime |

## Planned Next.js Public Runtime

These are planned UI destinations and must not be confused with current Laravel routes until the Next.js runtime is scaffolded.

| Key | Planned URI | Version |
| --- | --- | --- |
| marketing_home | / | V11 |
| audience_selector | /for | V11 |
| pricing | /pricing | V11 |
| developer_marketplace | /marketplace | V12 |
| developer_portal | /developers | V12 |
| public_mall | /mall | V13 |

## Current Next.js Public Runtime Scaffold

| Key | Path | Runtime | Version |
| --- | --- | --- | --- |
| public_web_workspace | apps/public-web | Next.js App Router | V15 |
| localized_home | /ar | Next.js App Router | V15 |
| public_web_manifest_contract | /api/public-web/manifest | Laravel JSON contract | V15 |

## Registry Rules

- Every implemented page must have a named route or a documented Filament page/resource route.
- Every route must state its runtime: Blade bridge, Filament admin, API contract, or Next public runtime.
- Current routes are tested in `V9UiFoundationTest` using Laravel route lookup.
- Planned routes are allowed only when marked as planned and tied to a future UI version.
