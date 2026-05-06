# KABEERI V5 Implementation Notes

Started on 2026-05-06.

## Scope Direction

V5 starts the ERP Pro, Integration Hub, and Partner Commerce foundations referenced by the V5 tracker and knowledge-system docs. This first implementation pack is intentionally foundational: it creates safe records, services, seed data, and tests without introducing irreversible financial, sync, or credential-handling behavior.

## Implemented Start Pack

- ERP Pro opportunities tied to organizations, contacts, leads, and pipeline records.
- Contract / CLM basic records.
- Helpdesk ticket records.
- Commission plans, commission events, and partner payout review placeholders.
- Integration connectors, credential vault references, external object links, preview sync jobs, and sync logs.
- Billing usage records for module meters.
- V5 feature flags, permissions, module seed entries, demo seeder, and smoke/security tests.

## Implemented Completion Pack

- Sales Pro documents, document lines, and invoice source links.
- Inventory reservations, RFQ records, and RFQ items.
- Accounting posting batches and posting entries.
- POS terminals, POS sessions, and POS sales.
- Advanced approval policies for ERP Pro records.
- ERP Pro and Integration Hub Filament list resources with tenant-scoped queries.
- Integration OAuth state placeholders, field mappings, value mappings, retry queue records, conflicts, webhook endpoints, webhook deliveries, and rate-limit buckets.
- External catalog sync preview batches and items.
- Advanced report dashboards and ERP Pro dashboard records.
- Package versions, update jobs, and signing review placeholders.
- V5 completion test suite covering all previously pending V5 task areas.

## Hard Boundaries

- Do not store raw integration secrets, passwords, API keys, OAuth tokens, or card data.
- Do not execute live two-way sync; V5 sync starts as queued/preview records.
- Do not perform automatic payouts, commissions settlement, escrow, or revenue-share transfers.
- Do not add `organization_id`, `company_id`, `site_id`, or `role` columns directly to `users`.
- Keep V5 records tenant-scoped by organization where applicable.

## Release Check

The V5 completion suite covers table availability, safe record defaults, integration credential boundaries, queue/preview defaults, tenant-clean users, and tenant-scoped Filament resource queries. Real-world finance and connector execution remains intentionally outside these record-only foundations.
