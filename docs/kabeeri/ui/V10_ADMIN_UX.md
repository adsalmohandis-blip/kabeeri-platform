# KABEERI V10 Internal Admin UX

Created on 2026-05-07.

## Goal

V10 makes the internal admin understandable and operational. It sits on top of the V9 foundation and turns the admin area into a command layer for system check, task truth, database readiness, module health, release gates, role-specific workspaces, quick actions, and permission-aware navigation.

## Runtime

- Runtime: Filament inside Laravel.
- Public marketing and Next.js public runtime are outside V10.
- Blade public pages remain bridge/fallback pages only.

## Implemented Pages

The V10 page registry lives in `config/kabeeri_admin.php`.

- System Check: `/admin/system-check`.
- Task Tracker Status: `/admin/task-tracker-status`.
- Database Status: `/admin/database-status`.
- Module Health: `/admin/module-health`.
- Release Readiness: `/admin/release-readiness`.
- Admin Workspaces: `/admin/admin-workspaces`.

## Admin UX Principles

Every admin surface must answer:

- What context am I in?
- What needs action?
- What am I allowed to do?
- What is blocked and why?
- What is the safest next step?

## Admin Spaces

V10 exposes workspace homes for:

- Personal Space.
- Organization Workspace.
- Kabeeri App / Site Admin.
- Company Admin / Rabet OS.
- Commerce Admin.
- ERP Admin.
- Kabeeri Mall Console.
- Talent Console.
- Kabeeri Teams.
- Developer Console.
- Billing Console.
- Platform Admin.

## Permission-Aware Navigation

V10 documents and displays blocked sensitive actions. Hidden UI is not security; backend policies, permissions, confirmations, and audit logs remain required.

Sensitive examples:

- billing.manage.
- company.verification.approve.
- finance.journal.post.
- users.role.assign.
- plugin.install.
- ai.apply_sensitive_change.

## Module Health

Module health groups are intentionally operational, not database-only:

- Platform Admin.
- Content and Apps.
- Company / Rabet OS.
- Commerce Admin.
- ERP Admin.
- Kabeeri Mall Console.
- Developer Console.
- Billing Console.
- Security and Compliance.
- Mobile and Desktop.

Each module shows table readiness, record counts, route availability, and a next action.

## Release Gate

V10 is complete when:

- V9 foundation is still ready.
- Six V10 Filament pages are registered.
- V10 task tracker is synced.
- V10 docs exist.
- V10 smoke tests exist and pass.
- Pint passes.
- Build passes.

## Implementation Artifacts

- `config/kabeeri_admin.php`.
- `App\Support\Ui\V10AdminExperience`.
- `App\Filament\Pages\SystemCheck`.
- `App\Filament\Pages\TaskTrackerStatus`.
- `App\Filament\Pages\DatabaseStatus`.
- `App\Filament\Pages\ModuleHealth`.
- `App\Filament\Pages\ReleaseReadiness`.
- `App\Filament\Pages\AdminWorkspaces`.
- `resources/views/filament/pages/v10-admin-page.blade.php`.
- `tests/Feature/V10AdminExperienceTest.php`.