# KABEERI V3 Index and Performance Pass

Date: 2026-05-06

## Scope

This pass reviewed V3 operational tables that are commonly filtered by organization, status, ownership, workflow trigger, or transaction relationship.

## Added Index Coverage

- CRM: contacts and leads.
- Sales: quotations, invoices, and payments.
- Inventory and purchasing: inventory items, stock movements, purchase orders, and goods receipts.
- People and projects: employee profiles and business tasks.
- Workflow and approvals: workflow definitions and approval requests.
- Reporting: report definitions and dashboard widgets.

## Notes

- The migration only adds indexes. It does not alter business data or table shape.
- Indexes are scoped toward V3 admin list queries, service lookups, and tenant-first filtering.
- A regression test confirms the composite indexes exist after a fresh migration.
