# KABEERI V4 Demo Seed

`DatabaseSeeder` now calls `V4DemoSeeder` after V1 and V3 demo data.

Run:

```powershell
php artisan migrate --seed
```

For a clean local check without touching the normal database, use SQLite memory:

```powershell
$env:DB_CONNECTION='sqlite'
$env:DB_DATABASE=':memory:'
php artisan migrate:fresh --seed --force
```

## Demo Entry Points

- Admin: `http://127.0.0.1:8000/admin`
- Public Mall: `http://127.0.0.1:8000/mall`
- Demo admin email: `admin@kabeeri.local`
- Demo admin password: `password`

## Seeded V4 Scenario

- Published Mall business, product, and service mirrors.
- Mall publication consent for the demo business profile.
- Moderation case and flag for the demo business.
- Published review and reputation snapshot.
- Approved internal marketplace package and theme catalog records.
- Legal partner profile, agency partner profile, and agency dashboard snapshot.
- Creator profile and Work Network profile.
- Trust badge award and Academy badge award.
- Growth referral record.
- Partner storefront and draft catalog share.

## Safety Notes

- Seeders are idempotent and use fixed slugs/keys/codes.
- No secrets, API keys, card data, payout rules, or commission rules are seeded.
- Partner storefront shares remain `draft` and `private`.
- Public Mall pages show published mirrors only.
