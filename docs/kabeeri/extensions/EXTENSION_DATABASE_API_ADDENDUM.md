# Extension Database and API Addendum

## Database Addendum Pattern

Every extension schema task must define:

- Table name and purpose.
- Tenant scope columns.
- Status lifecycle.
- JSON fields and their expected shape.
- Indexes and uniqueness rules.
- Rollback behavior.
- Seed/demo data requirements.
- Tests required before task closure.

## Recommended Tables

- `extension_registry_items`
- `extension_manifest_checks`
- `extension_release_versions`
- `extension_review_runs`
- `extension_installation_plans`
- `extension_update_jobs`
- `extension_rollback_points`
- `extension_license_grants`
- `extension_revenue_share_rules`

These are future-facing recommendations. Existing package/theme/plugin tables may continue to be used until a dedicated migration wave approves consolidation.

## API Addendum Pattern

Every extension API task must define:

- Method and path.
- Auth requirements.
- Tenant authorization rule.
- Request schema.
- Response schema.
- Error contract.
- Rate limit bucket.
- Audit events.
- Backward compatibility notes.

## Baseline API Areas

- Developer publisher onboarding.
- Manifest validation preview.
- Marketplace listing submission.
- Review status.
- Extension install preview.
- Extension install apply.
- Extension update preview.
- Extension rollback request.
