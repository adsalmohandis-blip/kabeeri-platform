# Kabeeri Knowledge Import

تم استيراد المعرفة التشغيلية من حزمة KBR v1.6.6 داخل هذا المجلد حتى يصبح المشروع مكتفيًا ذاتيًا أثناء التطوير.

## ماذا يوجد هنا؟

- `source_text/`: نسخة نصية قابلة للبحث من الوثائق الأصلية، محافظة على نفس تقسيم المجلدات.
- `source_manifest.json`: فهرس كل ملف مستورد ومساره وعدد كلماته.
- `source_summary_by_dir.json`: إحصاء سريع حسب مجلدات الوثائق الأصلية.
- `execution/START_HERE.md`: نقطة البداية العملية وما الذي سنحققه.
- `backend/BACKEND_SERVER_PLAN.md`: خطة الباك إند والسيرفر في Laravel.
- `backend/DATABASE_AND_MIGRATION_WAVES.md`: موجات بناء قاعدة البيانات وترتيب التنفيذ.
- `ui/UX_UI_EXECUTION_PLAN.md`: خطة UX/UI وفق الوثائق.
- `task_tracking/TASK_TRACKING_RULES.md`: قواعد حالة التاسكات والتحقق.
- `extensions/`: حزمة قواعد ومعمارية تحديثات الثيمات والبلجنز والإضافات.

## الوثائق التحليلية المرتبطة

- `docs/KBR_V166_FULL_BACKEND_SERVER_AND_UI_ANALYSIS.md`
- `docs/KBR_V166_DEEP_UI_UX_REVIEW.md`
- `docs/UI_PAGE_INVENTORY_AND_ROADMAP.md`

## قاعدة العمل

نقطة البداية الآن ليست بناء صفحات عشوائية. نقطة البداية هي V9: تثبيت أساس الـ UI/UX والحدود بين Laravel/Filament/Next.js، ثم تنفيذ V10-V14 بالترتيب.
