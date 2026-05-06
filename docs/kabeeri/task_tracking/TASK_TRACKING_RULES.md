# Task Tracking Rules

## القاعدة الأساسية

حالة التاسك يجب أن تعكس الحقيقة، لا التفاؤل.

## الحالات

- `pending`: لم يبدأ بعد.
- `in_progress`: بدأ فعليًا.
- `codex_done`: تم تنفيذه واختباره بواسطة Codex.
- `verified`: راجعه مالك المشروع ووافق عليه.
- `blocked`: متوقف بسبب مانع واضح.

## قواعد Codex

- لا يتم وضع `codex_done` إلا بعد تنفيذ فعلي واختبارات/تحقق مناسب.
- لا يتم وضع `verified` بواسطة Codex.
- عند وجود مانع، يجب تسجيل `blocked` مع السبب.
- أي تاسك تخطيطي جديد يبقى `pending` حتى تنفيذه.
- task_history يجب أن يطابق حالة ملفات التتبع.

## بداية أي تاسك

قبل التنفيذ:

```bash
php 24_kabeeri_task_tracking/scripts/kbr-task.php start VXX TXX
```

بعد التنفيذ والاختبار:

```bash
php 24_kabeeri_task_tracking/scripts/kbr-task.php done VXX TXX --prompt=XX --tests="..." --commit=pending --notes="..."
```

عند التعطل:

```bash
php 24_kabeeri_task_tracking/scripts/kbr-task.php block VXX TXX --notes="reason"
```
