# V3 Demo Seed

V3 demo data is included in `DatabaseSeeder` and is safe to run repeatedly.

## Run

```powershell
php artisan migrate:fresh --seed
```

## Seeded V3 Scenario

- CRM contact and lead.
- Service request linked to the CRM records.
- Draft quotation.
- Warehouse, inventory item, and supplier.
- Starter chart of accounts.
- Department and employee profile.
- Business project.
- Workflow definition.
- Report definition and dashboard widget.

## Notes

- Demo data is tenant-scoped to the first seeded organization.
- No external API credentials or secrets are seeded.
- Manual payments remain placeholders; no payment gateway is configured.
