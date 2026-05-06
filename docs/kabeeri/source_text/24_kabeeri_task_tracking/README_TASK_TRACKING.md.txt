# KABEERI Task Tracking

انسخ فولدر `24_kabeeri_task_tracking` كاملًا إلى جذر مشروع Laravel `kabeeri-platform`.

هذا النظام يجعل Codex يحدّث حالة التاسك تلقائيًا بعد كل Prompt.

## الحالات

- `pending`: لم يبدأ.
- `in_progress`: Codex بدأ العمل.
- `codex_done`: Codex انتهى وشغّل الاختبارات.
- `verified`: أنت راجعت واعتمدت.
- `blocked`: التاسك متوقف.

## أهم أمر تبدأ به

```bash
php 24_kabeeri_task_tracking/scripts/kbr-task.php list V1
```

## سير العمل

```bash
php 24_kabeeri_task_tracking/scripts/kbr-task.php start V1 T07
php 24_kabeeri_task_tracking/scripts/kbr-task.php done V1 T07 --prompt=07 --tests="php artisan test" --commit=pending --notes="summary"
php 24_kabeeri_task_tracking/scripts/kbr-task.php verify V1 T07 --notes="reviewed"
```

Codex يملك صلاحية `start` و `done` و `block` فقط. أنت فقط تستخدم `verify`.
