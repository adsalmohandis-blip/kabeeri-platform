# KABEERI V1 - Codex VS Code Handoff

## Session Context
- Date: 2026-05-05
- Goal: Start KABEERI implementation using V1 Prompt Pack
- Current step completed: V1 Prompt 01

## Workspace
- Project root:
  `C:\Users\arshw\OneDrive\Documents\New project`

## Source Docs (analyzed)
- Prompt packs folder:
  `D:\My Project Ideas\Kabeeri\kabeeri_professional_knowledge_system_v1.3.0_final_coding_ready\KABEERI_Professional_Knowledge_System\16_CODE_PROPMPTS_TO_CREATE_BY_CODEX`
- V1 prompt file:
  `...\kabeeri_v1_codex_prompt_pack_ar.docx`

## What Was Done (V1 Prompt 01)
1. Verified workspace and prerequisites.
2. Found default PHP in PATH was old (`8.1.4` from XAMPP).
3. Installed PHP 8.3 (user-scope) via winget.
4. Downloaded local Composer as `composer.phar` in project root.
5. Created Laravel project and moved it to workspace root.
6. Updated `README.md` with:
   - project purpose
   - V1-only scope
   - local run instructions
   - test instructions
7. Updated `.env` `APP_NAME` to `KABEERI_V1`.
8. Verified app routes and tests.

## Current Stack State
- Laravel version: `13.7.0`
- PHP used for build/tests: `8.3.30`
- Local PHP 8.3 path:
  `C:\Users\arshw\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe`
- Local Composer file:
  `C:\Users\arshw\OneDrive\Documents\New project\composer.phar`

## Important Environment Note
Default `php` command in terminal may still resolve to XAMPP PHP 8.1.
Use the explicit PHP 8.3 path for all commands until PATH is fixed.

Example:
```powershell
$php83 = "C:\Users\arshw\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe"
$php83 artisan test
```

## Validation Results
- Home route exists.
- Tests passed: `2/2`.

## Files Updated by Prompt 01
- `README.md`
- `.env`
- Full Laravel scaffold created in root (`app`, `bootstrap`, `config`, `database`, `public`, `resources`, `routes`, `storage`, `tests`, etc.)

## Output ZIP
- Ready zip for VS Code import:
  `C:\Users\arshw\OneDrive\Documents\New project\kabeeri_v1_prompt01_output.zip`

## Hard Rules to Keep
- Implement V1 only.
- No V2+ features.
- No Mall/ERP Pro/Advanced AI/External Sync unless explicitly requested.
- Do not add `organization_id/company_id/site_id/role` directly to `users`.
- Keep modular architecture.
- Add tests with each feature.
- Avoid destructive commands.

## Next Step
Proceed with **V1 Prompt 02 — أدوات الجودة الأساسية**.

Expected in Prompt 02:
- Ensure tests run reliably.
- Configure Laravel Pint.
- Add CI-friendly command list in README.
- Add basic health test confirming app boots.
- No business features yet.

## Suggested Kickoff Prompt for Codex in VS Code
```text
Read this file first:
C:\Users\arshw\OneDrive\Documents\New project\CODEX_VSCODE_HANDOFF_V1.md

Now continue with KABEERI V1 Prompt 02 only.
Follow V1 boundaries strictly.
Use PHP 8.3 from the explicit path in the handoff.
After implementation:
- summarize changes
- list files changed
- list commands run
- list tests added
- report test results
- mention risks/TODOs
```
