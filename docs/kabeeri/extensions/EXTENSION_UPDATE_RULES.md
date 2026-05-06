# KABEERI Extension Update Rules

Date: 2026-05-06
Status: Active governance addendum

## Purpose

Extension updates cover themes, plugins, packages, developer marketplace assets, and related API/database addenda. They must never bypass core release gates, tenant safety, manifest validation, or task tracking.

## Rules

- Every extension update must have a named task version and task IDs before implementation begins.
- Runtime code must be additive unless an approved migration/refactor task explicitly allows change.
- Package/theme/plugin manifests must be validated before installation, publication, update, or marketplace listing.
- Extension code cannot store raw secrets; only vault references or configured connector references are allowed.
- Extension install/update flows must be preview-first, reviewable, signed where applicable, and rollback-aware.
- Paid features must be checked through entitlements, not feature flags alone.
- Public APIs must preserve authentication, authorization, rate limits, versioning, and compatibility guarantees.
- Filament actions must not trigger destructive install/update behavior without an explicit confirmation and backend service guard.
- Task tracker status must reflect implementation truth: `codex_done` means implemented and tested by Codex; `verified` is owner-only.

## Required Evidence

- Database/API addendum for schema or endpoint changes.
- Business architecture note for monetization or marketplace-facing changes.
- Technical architecture note for runtime, signing, validation, queues, or rollback behavior.
- Acceptance checklist before a release candidate.
