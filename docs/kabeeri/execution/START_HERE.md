# Start Here

## نقطة البدء

نبدأ من V9 UI Foundation، وليس من إضافة Features جديدة في الباك إند.

السبب: الوثائق تؤكد أن النظام كبير جدًا، ولو بدأنا بصفحات أو موديلات جديدة بدون Information Architecture واضحة سنرجع للفوضى. لذلك البداية الصحيحة هي تثبيت الواجهة، المسارات، السياقات، والتكنولوجيا.

## سنحقق ماذا؟

في المرحلة القادمة سنحقق 6 نتائج عملية:

1. خريطة UI كاملة ومفهومة للأدمن والجمهور الخارجي.
2. فصل واضح بين Laravel Backend وFilament Admin وNext.js Public Runtime.
3. Admin Spaces واضحة: Personal, Organization, Site, Company/Rabet, Commerce, ERP, Mall, Talent, Teams, Developer, Billing, Platform.
4. Public Audience Journeys واضحة: صاحب بزنس، وكالة، مطور/Creator، مسوق/Partner، Enterprise، زائر Mall.
5. Design System عربي/RTL قابل للتوسع.
6. Task tracking مطابق للحقيقة: لا شيء يتحول إلى `codex_done` إلا بعد التنفيذ والاختبار.

## المطلوب قبل التنفيذ

- تنفيذ V9 بالكامل بالترتيب.
- كل تاسك يبدأ من task tracker.
- كل تاسك ينتهي باختبار أو توثيق سبب عدم الاختبار.
- أي صفحة جديدة يجب أن يكون لها Route/Page واضح.
- أي Admin action مهم يجب أن يحترم policy/permission.
- أي public UI جديد يجب أن يكون RTL/mobile-safe.

## ترتيب العمل المقترح

1. V9: foundation, boundaries, design tokens, route registry, spaces model.
2. V10: admin system check and internal admin spaces.
3. V11: public marketing, onboarding, audience paths, pricing.
4. V12: themes, plugins, developer economy, marketplace UX.
5. V13: Mall, customer portal, marketer/partner/agency flows.
6. V14: QA, accessibility, responsive, permission-aware navigation, release candidate.

## لا نبدأ الآن بـ

- ERP جديد.
- Mall كامل.
- Marketplace عام.
- AI Co-builder جديد.
- Next.js scaffold قبل تعريف boundary في V9.
- صفحات كثيرة بدون route registry.
