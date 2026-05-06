# KABEERI V3 Implementation Notes

Updated on 2026-05-06.

## Scope Implemented

KABEERI V3 adds a Business Operations layer on top of the stable V1/V2 foundation. Organizations remain the tenant root, and V3 records are scoped by `organization_id` with optional company/site links where the existing model supports them.

Implemented V3 areas:

- CRM: contacts, CRM-expanded leads, lead sources, lead scoring, sales pipelines/stages, CRM activities, customer timeline, and service requests.
- Sales: quotations, quotation lifecycle, invoices, quotation-to-invoice conversion, manual payments, and receipts.
- Inventory and purchasing: inventory items, warehouses, stock movements, suppliers, supplier contacts, purchase orders, and goods receipts.
- Accounting starter: chart of accounts and balanced journal entries.
- People operations: employee profiles, departments, positions, and employee evaluations.
- Projects and workflow: business projects/tasks, workflow definitions, workflow runs, approval requests, and workflow hook dispatch.
- Reporting: report definitions, report snapshots, and dashboard widgets.
- Admin foundation: Filament resources for the implemented V3 operational areas.
- Demo data: idempotent V3 demo seed records included from `DatabaseSeeder`.
- Hardening: smoke tests, tenant-isolation/security tests, and composite performance indexes.

## V3 Exclusions

V3 intentionally does not implement:

- Full ERP accounting, tax engines, payroll, inventory costing, POS, shipping, or fulfillment.
- Real payment gateway integrations or card-data storage.
- Live external accounting, CRM, supplier, or inventory sync.
- Advanced AI scoring, forecasting, or automation.
- Public marketplace, affiliate payouts, Academy, Work Network, or partner operations.
- Arbitrary package code execution or third-party package submissions.

## Common Commands

Run the full test suite:

```powershell
php artisan test
```

Check formatting:

```powershell
vendor/bin/pint --test
```

Fresh local setup with demo data:

```powershell
php artisan migrate:fresh --seed
```

The PHPUnit config sets a higher `memory_limit` for the expanded V3 suite.

## Demo Data

`DatabaseSeeder` runs V1, V2, and V3 demo seeders. V3 demo records include:

- Demo contact, lead, service request, and quotation.
- Demo warehouse, inventory item, supplier, and starter chart of accounts.
- Demo department, employee profile, project, workflow definition, report definition, and dashboard widget.

No real secrets or external credentials are seeded.

## Admin Areas

V3 Filament resources are grouped into:

- CRM
- Sales
- Inventory
- People
- Workflow
- Reports

Resources use tenant-scoped queries so users only see records from organizations they can access.

## Security and Performance

See:

- [V3_SECURITY_PASS_REPORT.md](V3_SECURITY_PASS_REPORT.md)
- [V3_INDEX_PERFORMANCE_REPORT.md](V3_INDEX_PERFORMANCE_REPORT.md)

## Known Limitations

- V3 services are backend/data-layer foundations. Public customer-facing portals are not included.
- Payment records are manual placeholders only.
- Workflow hooks start matching active workflows but do not execute arbitrary package code.
- Reporting stores definitions and snapshots; it does not include a full BI/query builder.
