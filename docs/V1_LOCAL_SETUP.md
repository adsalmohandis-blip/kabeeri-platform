# V1 Local Setup

## Requirements

- PHP `^8.3`
- Composer `^2`
- SQLite or MySQL

## Standard Setup

```powershell
composer install
Copy-Item .env.example .env -Force
php artisan key:generate
if (!(Test-Path .\database\database.sqlite)) { New-Item .\database\database.sqlite -ItemType File | Out-Null }
php artisan migrate --seed
php artisan serve
```

Open:
- `http://127.0.0.1:8000`
- `http://127.0.0.1:8000/admin`

## If PHP Path Is Old on Windows

Use explicit PHP path (example):

```powershell
$php83 = "C:\Users\arshw\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe"
$php83 artisan test
```

## Test & Quality Commands

```powershell
php artisan test
vendor/bin/pint
vendor/bin/pint --test
```
