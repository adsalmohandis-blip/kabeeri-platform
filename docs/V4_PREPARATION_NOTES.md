# KABEERI V4 Preparation Notes

Date: 2026-05-06

## V3 Review Summary

V3 is complete as an ERP-lite Business Operations foundation. It now includes CRM, sales, manual payments, inventory lite, purchasing, accounting starter, people operations, projects, workflow, approvals, reporting, demo data, admin resource foundations, smoke coverage, security coverage, and performance indexes.

The implementation stayed within V3 boundaries:

- No payment gateway integration.
- No live external sync.
- No arbitrary package code execution.
- No payroll, POS, shipping fulfillment, tax engine, or advanced inventory costing.
- No V4+ public marketplace, Academy, Work Network, affiliate payouts, or advanced AI automation.

## Recommended V4 Themes

V4 should build on V3 without turning the platform into an unsafe full ERP in one jump.

Recommended V4 candidates:

- Workflow UI polish and safer approval configuration.
- Sales documents polish: quote/invoice templates, status history, void/cancel flows, and exports.
- Reporting dashboards: saved views, date filters, CSV export, and scheduled snapshots.
- Inventory operations: stock adjustment approvals, reorder suggestions, and low-stock reports.
- Customer/vendor portals as opt-in authenticated views.
- Accounting improvements: posting rules from invoices/payments/purchases and reconciliation reports.
- Import/export hardening for CRM, products, and inventory through preview-first flows.

## V4 Guardrails

- Keep Organizations as tenant root.
- Keep users free of direct tenant/role columns.
- Keep external integrations preview-first or explicit-confirmation-first.
- Do not store API secrets in plain text.
- Do not add third-party package execution without a sandbox and approval model.
- Keep payment providers out unless PCI and provider boundaries are explicitly designed.

## Technical Debt To Watch

- Filament resources can gain more policy-level tests as permissions become more granular.
- Reports need a bounded query model before user-authored reporting is exposed.
- Workflow execution should remain declarative and non-code-executing until a sandbox exists.
- Financial records need stricter void/cancel flows before any real accounting close process.
- Large test suites now require the raised PHPUnit memory limit committed in V3.

## Suggested First V4 Sprint

1. V4 scope freeze and exclusions document.
2. Workflow approval UI and policy tests.
3. Quote/invoice status history and cancel/void foundations.
4. Dashboard saved filters and CSV exports.
5. Import/export preview hardening for CRM and inventory.
