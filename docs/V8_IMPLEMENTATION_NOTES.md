# KABEERI V8 Implementation Notes

Started on 2026-05-06.

## Scope Direction

V8 adds desktop client and offline-sync backend foundations. It provides client registration, sync sessions, pull API, push dry-run validation, outbox operation records, conflict detection, and a file queue contract.

## Implemented Foundations

- Desktop client registry with app/platform metadata and capabilities.
- Sync sessions with cursors and pull/push dry-run summaries.
- Pull API under `/api/desktop/sync/pull` that returns module manifest changes and advances the session cursor.
- Push dry-run API under `/api/desktop/sync/push-dry-run` that records operations without applying domain mutations.
- Outbox operation contract with client operation ids, entity identifiers, versions, payload previews, and dry-run statuses.
- Conflict detection records for version mismatches.
- File queue API under `/api/desktop/files` that stores metadata and content hash references only.
- V8 feature flags, permissions, module seed data, demo seeder, and tests.

## Safety Boundaries

- Push operations are dry-run only and do not mutate target domain records.
- File queue items store metadata, hash, and storage references only; no raw file bytes are accepted or stored.
- Conflict detection is record-first. Resolution workflows are represented as data, not automatic overwrites.
- V8 does not introduce native desktop binaries; it provides backend foundations only.
