# Extension Codex Prompt Pack

## Triage Prompt

```text
Inspect the requested extension update. Identify whether it is business architecture, technical architecture, database/API addendum, runtime implementation, Filament/admin UI, test coverage, or documentation. Do not edit runtime code until required governance files and task tracker status are clear.
```

## Safe Implementation Prompt

```text
Implement the smallest additive extension backend change. Preserve tenant scoping, validate manifests, avoid raw secrets, add tests, update docs, and mark the task codex_done only after targeted tests pass. Do not mark verified.
```

## Manifest Review Prompt

```text
Review the extension manifest for identity, version, publisher, permissions, dependencies, compatibility, assets, hooks, licensing, revenue share metadata, and security risks. Return findings first, then required fixes.
```

## Release Candidate Prompt

```text
Run the extension acceptance checklist. Confirm database/API addenda, service guards, entitlement checks, tests, docs, task tracker updates, and rollback notes before recommending owner verification.
```
