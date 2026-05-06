# Prompt block to paste before any Codex task

You must use the KABEERI task tracker.

Before starting the current task, run:

```bash
php 24_kabeeri_task_tracking/scripts/kbr-task.php start <VERSION> <TASK_ID>
```

After implementation and tests pass, run:

```bash
php 24_kabeeri_task_tracking/scripts/kbr-task.php done <VERSION> <TASK_ID> --prompt=<PROMPT_ID> --tests="php artisan test" --commit=pending --notes="short summary"
```

Do not mark tasks as verified. Only the project owner can run `verify`.

If blocked, run:

```bash
php 24_kabeeri_task_tracking/scripts/kbr-task.php block <VERSION> <TASK_ID> --notes="reason"
```
