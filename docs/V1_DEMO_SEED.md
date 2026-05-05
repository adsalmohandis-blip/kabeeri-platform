# V1 Demo Seed

`DatabaseSeeder` includes `V1DemoSeeder` and creates local demo data for smoke validation.

## Seed Command

```powershell
php artisan migrate:fresh --seed
```

## Demo Admin Login

- Email: `admin@kabeeri.local`
- Password: `password`

## Demo Records

- Organization: `kabeeri-demo-org`
- Company: `kabeeri-demo-company`
- App/Site: `kabeeri-demo-app`
- Theme: `kabeeri-starter`
- Content types: `page`, `post`
- Published pages: `home`, `about`, `services`, `contact`
- Business profile draft: `kabeeri-demo-business`

## Public Rendering Check

Open:

`/app/kabeeri-demo-app/home`

Expected:
- Published page is visible publicly
- Starter theme layout is applied
- Arabic/RTL direction works when app language is Arabic
