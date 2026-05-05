# KABEERI V1 Module Conventions

This project follows a modular skeleton under `app/Modules` to keep V1 clean and extensible.

## Current Skeleton

- `app/Modules/Core`
  - `Models`
  - `Actions`
  - `Services`
  - `Policies`
  - `Events`
  - `Listeners`
  - `Jobs`
  - `Filament`
  - `Database`
- `app/Modules/CMS`
- `app/Modules/Media`
- `app/Modules/Rabet`
- `app/Modules/Platform`

## Conventions

- Keep controllers thin; prefer small `Actions` and `Services`.
- Keep module boundaries explicit; avoid cross-module tight coupling.
- Add tests with every feature.
- Do not place V2+ logic in V1 unless explicitly requested.
- Do not add tenant columns directly to `users`; memberships remain the relationship boundary.

## Scope Note

This skeleton is structural only. No domain logic, migrations, or business resources are implemented here.
