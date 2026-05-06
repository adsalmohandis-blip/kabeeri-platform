# Extension Technical Architecture

## Core Components

- Extension registry: source of truth for package/theme/plugin identity, publisher, status, compatibility, and distribution channel.
- Manifest validator: validates metadata, permissions, dependencies, compatibility, assets, and declared hooks.
- Review pipeline: automated checks plus manual approval for marketplace publication.
- Signing and release governance: release versions must be traceable and signed before trusted installation.
- Install/update service: preview-first, idempotent, rollback-aware, and tenant-scoped.
- Entitlement checks: required before installing premium assets or enabling paid extension capabilities.

## Runtime Rules

- Extensions must not run arbitrary migrations without a reviewed database addendum.
- Extension data must be tenant-scoped when customer data is involved.
- Extension hooks must declare permissions and expected payload shape.
- Long-running installs or updates must use queue jobs and audit logs.
- Public extension APIs must use versioned endpoints and documented response contracts.
- Rollback must be explicit for data mutations and best-effort for asset/config changes.

## Filament/Admin Rules

- List and review screens may be read-only by default.
- Approve, sign, publish, install, update, and rollback actions must call backend services with guards.
- Dangerous actions must not be implemented as direct table edits.
