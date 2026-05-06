# UX/UI Execution Plan

## القرار

V9-V14 هي مسار UX/UI فوق الباك إند الموجود، وليست بديلًا عن خطة المنتج V1-V6.

## V9

هدف V9: تثبيت الأساس قبل بناء الصفحات.

يشمل:

- UI rules and boundaries.
- Laravel vs Next.js boundary.
- Design tokens.
- Admin spaces model.
- Context switcher concept.
- Route/page registry.
- RTL/accessibility/responsive standards.
- UI smoke strategy.

مخرجات التنفيذ داخل المشروع:

- `config/kabeeri_ui.php`: مصدر truth للـ runtime boundaries والـ design tokens والـ admin spaces والـ route registry.
- `App\Support\Ui\V9UiFoundation`: تقرير readiness قابل للاختبار.
- `resources/css/app.css`: بداية tokens قابلة للاستخدام في Vite/Tailwind.
- `docs/kabeeri/ui/V9_UI_FOUNDATION.md`: قواعد V9 وrelease gate.
- `docs/kabeeri/ui/ROUTE_PAGE_REGISTRY.md`: سجل الصفحات والمسارات.
- `docs/kabeeri/ui/UI_SMOKE_TEST_STRATEGY.md`: استراتيجية smoke testing.
- `docs/kabeeri/ui/NEXT_PUBLIC_RUNTIME_PLAN.md`: خطة Next.js public runtime.

## V10

هدف V10: جعل الأدمن مفهوم وقابل للتشغيل.

يشمل:

- System check dashboard.
- Task tracker dashboard.
- Migration/database status.
- Module health.
- Release readiness.
- Role-specific dashboards.
- Permission-aware navigation.

مخرجات التنفيذ داخل المشروع:

- `config/kabeeri_admin.php`: سجل صفحات V10، module health، workspace homes، quick actions، permission-aware rules.
- `App\Support\Ui\V10AdminExperience`: service يجمع system/task/database/module/release/workspaces data.
- Filament pages: System Check, Task Tracker Status, Database Status, Module Health, Release Readiness, Admin Workspaces.
- `resources/views/filament/pages/v10-admin-page.blade.php`: واجهة مشتركة منظمة لصفحات V10.
- `docs/kabeeri/ui/V10_ADMIN_UX.md`: قواعد وتجربة V10.
- `docs/kabeeri/ui/V10_RELEASE_CANDIDATE.md`: release gate.
- `tests/Feature/V10AdminExperienceTest.php`: اختبارات smoke وpermission-aware navigation.

## V11

هدف V11: جعل الجمهور يفهم المنصة ومسار onboarding.

يشمل:

- Public landing information architecture.
- Audience selector.
- Business owner path.
- Enterprise path.
- Developer/Creator path.
- Marketer/Partner path.
- WordPress alternative page.
- Pricing/subscription architecture.
- Early access/demo request.

## V12

هدف V12: شرح وتشغيل الثيمات والبلجنز والمطورين.

يشمل:

- Developer portal.
- Creator/publisher onboarding.
- Kabeeri Design Market.
- Package Store.
- Package lifecycle.
- Manifest, permissions, signing, compatibility.
- Licensing/revenue share.

## V13

هدف V13: تشغيل المسارات الخارجية حول Mall والعملاء والشركاء.

يشمل:

- Kabeeri Mall UX.
- Business/product/service/talent/travel listings.
- Customer portal.
- Agency/partner/marketer flows.
- Trust, moderation, reports, listing claim.

## V14

هدف V14: release candidate للـ UI.

يشمل:

- Accessibility pass.
- RTL/LTR pass.
- Responsive pass.
- Permission-aware navigation QA.
- Marketplace vs Mall separation QA.
- Design system coverage QA.
- Docs and release report.

## قواعد UI

- لا تعرض كل المنصة دفعة واحدة للجمهور.
- افصل الأدمن الداخلي عن العرض العام.
- UI الأدمن context-first وpermission-aware.
- public themes طويلة المدى Next.js/React/Tailwind.
- Blade الحالي fallback/bridge فقط.
- Filament هو مكان الإدارة الداخلية.
