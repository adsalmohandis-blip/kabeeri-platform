# Database and Migration Waves

## قاعدة التنفيذ

قاعدة البيانات تُبنى على موجات. لا ننتقل من موجة لموجة إلا بعد:

- `migrate:fresh` ينجح.
- rollback آمن حيث يمكن.
- seed بسيط ينجح.
- smoke tests تمر.

## Build Waves

- Wave 00: Reference and platform basics.
- Wave 01: Users and profiles.
- Wave 02: Ownership core: organizations, memberships, companies, sites.
- Wave 03: RBAC.
- Wave 04: Settings, flags, logs, modules.
- Wave 05: Media foundation.
- Wave 06: Billing and entitlements foundation.
- Wave 07: Cloud foundation.
- Wave 08: CMS foundation.
- Wave 09: Rabet and verification.
- Wave 10: Commerce foundation.
- Wave 11: Mall mirror and sync.
- Wave 12: Events and workflows.
- Wave 13: Notifications and inbox.
- Wave 14: Security and compliance base.
- Wave 15: Frontend Anywhere / Portable / Dedicated registries.
- Wave 16: Search, reviews, moderation, disputes.
- Wave 17: ERP Essentials.
- Wave 18: ERP Pro and advanced operations.
- Wave 19: Integration Hub.
- Wave 20: AI Co-builder.
- Wave 21: Analytics, GRC, Data Platform, Enterprise.

## أول 40 Migration مرجعية

1. `create_countries_table`
2. `create_currencies_table`
3. `create_users_table`
4. `create_user_profiles_table`
5. `create_organizations_table`
6. `create_organization_memberships_table`
7. `create_companies_table`
8. `create_company_memberships_table`
9. `create_sites_table`
10. `create_permissions_table`
11. `create_roles_table`
12. `create_role_permission_table`
13. `create_membership_role_assignments_table`
14. `create_membership_permission_overrides_table`
15. `create_settings_table`
16. `create_feature_flags_table`
17. `create_feature_flag_overrides_table`
18. `create_activity_logs_table`
19. `create_audit_logs_table`
20. `create_notifications_table`
21. `create_modules_table`
22. `create_module_installations_table`
23. `create_media_assets_table`
24. `create_media_usages_table`
25. `create_content_types_table`
26. `create_content_entries_table`
27. `create_content_revisions_table`
28. `create_taxonomies_table`
29. `create_taxonomy_terms_table`
30. `create_content_term_table`
31. `create_menus_table`
32. `create_menu_items_table`
33. `create_seo_metadata_table`
34. `create_redirects_table`
35. `create_themes_table`
36. `create_theme_settings_table`
37. `create_installed_packages_table`
38. `create_business_profiles_table`
39. `create_verification_requests_table`
40. `create_verification_documents_table`

## ممنوعات

- ممنوع tenant fields مباشرة داخل `users`.
- ممنوع ERP قبل permissions/company memberships/activity logs.
- ممنوع Mall بدون moderation/visibility.
- ممنوع Marketplace بدون package security/permission disclosure.
- ممنوع AI write actions بدون plan/preview/approval/apply log/rollback.
