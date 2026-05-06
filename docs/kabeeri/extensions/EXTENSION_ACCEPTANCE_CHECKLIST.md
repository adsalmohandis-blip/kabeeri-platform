# Extension Acceptance Checklist

## Governance

- Task tracker version and task IDs exist.
- Business architecture is updated when monetization, marketplace, or audience impact changes.
- Technical architecture is updated when runtime behavior changes.
- Database/API addendum exists for schema or endpoint changes.

## Security

- Tenant scope is enforced.
- Raw secrets are not stored.
- Manifest permissions are explicit.
- Dangerous actions go through guarded services.
- Audit/log events exist for install, update, publish, and rollback actions.

## Product

- Free/pro/paid behavior is entitlement-aware.
- Upgrade triggers are clear and not blocking too early.
- Developer marketplace expectations are documented.
- Customer-facing compatibility/support signals are defined.

## Engineering

- Migrations are additive and reversible.
- Services are testable without UI.
- Filament actions are safe and non-destructive by default.
- Tests cover success, denial, tenant isolation, and edge cases.
- Documentation and task history match implementation truth.

## Release

- Targeted tests pass.
- Full test suite is run or residual risk is documented.
- Commit message summarizes actual implementation and tests.
- Owner verification remains separate from Codex completion.
