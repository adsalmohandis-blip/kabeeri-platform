# V2 Codex Operation Prompts

## T47 Safe Fix Prompt

Use this prompt when a V2 regression appears:

```text
Review the failing V2 behavior without reverting unrelated user changes. Identify the smallest safe backend fix, preserve tenant scoping, avoid destructive migration behavior, run the targeted V2 test, then report files changed and residual risk.
```

## T48 Pre-Commit Review Prompt

Use this before committing V2 changes:

```text
Perform a code-review pass focused on V2 backend regressions: database safety, tenant isolation, Filament route exposure, seed idempotency, preview-only import behavior, and missing tests. List findings first with file references, then run targeted tests.
```

## T49 Commit Message Prompt

Use this for V2 backend completion commits:

```text
Write a concise conventional commit message that states the V2 backend area completed, mentions tests run, and avoids overstating owner verification. Prefer: feat: complete V2 backend admin and seed coverage
```
