# KABEERI V4 Implementation Notes

Started on 2026-05-06.

## Scope Direction

V4 builds on the completed V1, V2, and V3 foundations. It may add cloud-site operations, public Mall mirroring, moderation, reviews, partner networks, internal marketplace governance, creator/agency/work/academy/referral foundations, V4 admin resources, public navigation, seed data, smoke tests, security, documentation, and release checks.

Organizations remain the tenant root. Sites remain user-facing apps stored in `sites` unless a V4 task explicitly adds a separate cloud-site operational record that references existing organizations/sites.

## V4 Hard Boundaries

- Do not break V1, V2, or V3 tests and public/admin flows.
- Do not add `organization_id`, `company_id`, `site_id`, or `role` columns directly to `users`.
- Do not implement real payment gateways, card storage, payouts, commissions, escrow, or revenue share in V4 unless a later task explicitly designs that compliance boundary.
- Do not implement unsafe live external sync. External feeds must be preview-first, explicit-confirmation-first, or record-only until safe processors exist.
- Do not store API keys, tokens, passwords, or secrets in plain text.
- Do not execute third-party package code or marketplace submissions.
- Do not implement full ERP, payroll, tax engine, POS, shipping fulfillment, advanced AI automation, or enterprise security tracks unless a later version explicitly owns them.
- Keep public Mall records mirrored from source data with consent/status controls; do not publish private tenant records automatically.
- Add focused tests for each feature and keep migrations reversible.
- Keep changes small, modular, and compatible with the existing Laravel/Filament patterns.

## V4 Safety Defaults

- Publication is opt-in.
- Sync is one-way or preview-first unless explicitly approved.
- Moderation status defaults to pending/draft where public exposure is possible.
- Partner, agency, academy, work-network, and referral features start as foundations, not payout or full operations systems.
- Admin resources must use tenant-scoped queries and policy-aware actions.

## Current Tracker State

V4 has 52 tasks, T00 through T51. Codex may mark tasks as `codex_done`; owner verification remains separate.

## Implemented V4 Foundations

- Cloud operations records: sites, domains, backups, and health checks.
- Public Mall mirrors, consent-gated publishing, preview-first sync records, public browsing, and Mall navigation.
- Moderation, reports/flags, queue service, reviews, reputation snapshots, and trust badges.
- Internal marketplace catalog, theme store filters, and package installation governance.
- Rabet legal partners, verification assignment, agency accreditation/dashboard, creator profiles, Work Network profiles, Academy badges, growth referrals, and partner storefront drafts.
- V4 Filament list resources, demo seed data, smoke tests, and security/privacy report.

## Known Missing Source

The task tracker references `16_CODE_PROPMPTS_TO_CREATE_BY_CODEX/kabeeri_v4_codex_prompt_pack_ar.docx`, but that source file is not present in the current workspace. Until it is added, implementation will follow the task titles, existing architecture, and these V4 boundaries conservatively.
