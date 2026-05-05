# KABEERI V1

KABEERI V1 is the core Laravel foundation for building the first controlled release of the KABEERI platform.
This stage focuses on a stable project base only and does not include V2+ business features.

## V1 Scope Only

This repository is currently in **V1 foundation mode**.
At this stage we only prepare core project setup and safe development workflow.

Not included in this step:
- V2/V3/V4/V5/V6 features
- Mall, advanced commerce, ERP Pro, advanced AI, external sync
- Production integrations

## Local Development Setup

### Requirements
- PHP 8.3+
- Composer 2+
- SQLite (default) or MySQL-compatible database

### Notes for this machine
If `php` in your terminal points to an older version, use the PHP 8.3 path directly:

```powershell
$php83 = "C:\Users\arshw\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe"
```

### Install dependencies
```powershell
$php83 .\composer.phar install
```

### Environment
```powershell
Copy-Item .env.example .env -Force
$php83 artisan key:generate
```

### Database (default sqlite)
```powershell
if (!(Test-Path .\database\database.sqlite)) { New-Item .\database\database.sqlite -ItemType File | Out-Null }
$php83 artisan migrate
```

### Run locally
```powershell
$php83 artisan serve
```

Then open [http://127.0.0.1:8000](http://127.0.0.1:8000).

## Admin Panel (Filament)

Filament is installed for the V1 admin foundation only (no business resources yet).

- Admin URL: [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin)
- Panel provider: `app/Providers/Filament/AdminPanelProvider.php`

Create an admin user:

```powershell
$php83 artisan make:filament-user
```

If `php` points to a different version, use the explicit PHP 8.3 path.

## Module Skeleton

Module structure and conventions are documented in [MODULES.md](MODULES.md).

## Running Tests

```powershell
$php83 artisan test
```

## Quality Commands

Run the formatter:

```powershell
$php83 .\vendor\bin\pint
```

Check formatting without changing files:

```powershell
$php83 .\vendor\bin\pint --test
```

Run the test suite:

```powershell
$php83 artisan test
```

## CI-Friendly Command List

Use these commands in CI or a clean validation terminal:

```powershell
composer install --no-interaction --prefer-dist
php artisan migrate --force
php artisan test
vendor/bin/pint --test
```

On this machine (where `php` may point to an older version), use:

```powershell
$php83 .\composer.phar install --no-interaction --prefer-dist
$php83 artisan migrate --force
$php83 artisan test
$php83 .\vendor\bin\pint --test
```

## Project Status

Prompts implemented: **V1 Prompt 01 (Initial Foundation)** and **V1 Prompt 02 (Basic Quality Tools)**.
Foundation progress includes Filament admin setup and a module structure skeleton, without domain business features.
