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

مخرجات التنفيذ داخل المشروع:

- `config/kabeeri_public.php`: مصدر truth للصفحات العامة، الجمهور المستهدف، رحلة onboarding، التسعير، FAQ، وخطة Next.js.
- `App\Support\Ui\V11PublicExperience`: service يجهز بيانات V11، يفحص release readiness، ويلتقط طلبات الديمو كـ CRM leads.
- `App\Http\Controllers\Web\PublicMarketingController`: controller لصفحات العرض العام ومسار contact sales.
- Public routes: `/public`, `/for`, `/for/business-owners`, `/for/enterprise`, `/for/developers-creators`, `/for/marketers-partners`, `/wordpress-alternative`, `/use-cases/service-business`, `/templates`, `/onboarding`, `/onboarding/workspace-setup`, `/pricing`, `/trust`, `/contact-sales`.
- `resources/views/public/v11-page.blade.php`: واجهة Blade bridge احترافية تفصل الجمهور عن الأدمن وتعرض المسارات حسب الصفحة.
- `docs/kabeeri/ui/V11_PUBLIC_UX.md`: شرح تجربة الجمهور وحدودها.
- `docs/kabeeri/ui/V11_RELEASE_CANDIDATE.md`: release gate واختبارات V11.
- `tests/Feature/V11PublicExperienceTest.php`: اختبارات config/routes/render/contact/release readiness.

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

مخرجات التنفيذ داخل المشروع:

- `config/kabeeri_marketplace.php`: مصدر truth لصفحات V12، الكتالوج، التصنيفات، lifecycle، manifest fields، permissions، QA، component library، licensing، وrelease gates.
- `App\Support\Ui\V12MarketplaceExperience`: service يجهز بيانات Marketplace Studio ويفحص routes/admin routes/database/docs/task tracker readiness.
- `App\Http\Controllers\Web\MarketplaceDeveloperController`: controller لصفحات Marketplace وDeveloper Portal.
- Marketplace routes: `/marketplace`, `/marketplace/themes`, `/marketplace/themes/{theme}`, `/marketplace/theme-recipes`, `/marketplace/plugins`, `/marketplace/plugins/{package}`, `/marketplace/licensing`, `/marketplace/governance`, `/marketplace/review-status`.
- Developer routes: `/developers`, `/developers/onboarding`, `/developers/docs/themes`, `/developers/docs/plugin-manifest`, `/developers/docs/connectors`, `/developers/submission-checklist`, `/developers/listings`, `/developers/sales`, `/developers/profile`, `/developers/qa`.
- `resources/views/marketplace/v12-page.blade.php`: Blade bridge لواجهة Marketplace Studio وDeveloper Portal.
- `docs/kabeeri/ui/V12_MARKETPLACE_DEVELOPER_UX.md`: شرح تجربة الثيمات والبلجنز والمطورين وحدودها.
- `docs/kabeeri/ui/V12_RELEASE_CANDIDATE.md`: release gate واختبارات V12.
- `tests/Feature/V12MarketplaceExperienceTest.php`: اختبارات config/routes/render/detail/governance/release readiness.

## V13

هدف V13: تشغيل المسارات الخارجية حول Mall والعملاء والشركاء.

يشمل:

- Kabeeri Mall UX.
- Business/product/service/talent/travel listings.
- Customer portal.
- Agency/partner/marketer flows.
- Trust, moderation, reports, listing claim.

مخرجات التنفيذ داخل المشروع:

- `config/kabeeri_external.php`: مصدر truth لصفحات V13، Mall sections، trust badges، search filters، customer steps، partner paths، referrals، campaign resources، network، legal verification، وrelease gates.
- `App\Support\Ui\V13ExternalExperience`: service يجهز بيانات Mall/customer/partner/network ويفحص routes/admin routes/database/docs/task tracker readiness.
- `App\Http\Controllers\Web\ExternalPortalController`: controller لصفحات Mall trust/search/claim، customer portal، partners، network، legal verification.
- Mall routes الجديدة: `/mall/search`, `/mall/trust`, `/mall/claim-report` بجانب مسارات Mall الحالية.
- Customer routes: `/customer`, `/customer/theme-plugins`, `/customer/quick-setup`.
- Partner routes: `/partners`, `/partners/agency-profile`, `/partners/storefront`, `/partners/referrals`, `/partners/campaigns`, `/partners/legal-verification`.
- Network routes: `/network`, `/network/talent-path`.
- `resources/views/external/v13-page.blade.php`: واجهة Blade bridge للبوابات الخارجية.
- `resources/views/mall/layout.blade.php` وتحديث كل قوالب Mall الحالية: home, business, products, services, courses, talent, travel.
- `docs/kabeeri/ui/V13_MALL_CUSTOMER_PARTNER_UX.md`: شرح تجربة V13 وحدودها.
- `docs/kabeeri/ui/V13_RELEASE_CANDIDATE.md`: release gate واختبارات V13.
- `tests/Feature/V13ExternalExperienceTest.php`: اختبارات config/routes/render/search/trust/release readiness.

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

مخرجات التنفيذ داخل المشروع:

- `config/kabeeri_ui_quality.php`: مصدر truth لقواعد V14، route groups، accessibility، responsive، navigation، states، security/permission، performance، manual QA، Next.js separation، وquality gates.
- `App\Support\Ui\V14UiReleaseCandidate`: service يجمع route inventory وcoverage report وrelease readiness فوق V9-V13.
- `App\Http\Controllers\Web\UiReleaseCandidateController`: controller لتقرير الجاهزية النهائي.
- Route: `/ui/release-candidate`.
- `resources/views/ui/v14-release-candidate.blade.php`: واجهة release candidate center.
- `docs/kabeeri/ui/V14_UI_QA_HANDOFF.md`: handoff checklist لمالك المنصة وفريق التطوير.
- `docs/kabeeri/ui/V14_UI_RELEASE_CANDIDATE.md`: release candidate report وGo/No-Go criteria.
- `tests/Feature/V14UiReleaseCandidateTest.php`: اختبارات route inventory، coverage، docs، rendering، وrelease readiness.

## V15

هدف V15: تحويل قرار Next.js public runtime من خطة موثقة إلى scaffold حقيقي قابل للبناء والاختبار.

يشمل:

- Laravel public-web manifest contract.
- Next.js App Router workspace under `apps/public-web`.
- Arabic/RTL-first design tokens.
- Public runtime route/navigation manifest.
- Theme manifest resolver foundation.
- API client boundary for Laravel JSON contracts.
- Smoke/typecheck/build commands.

مخرجات التنفيذ داخل المشروع:

- `config/kabeeri_public_runtime.php`: مصدر truth لقواعد V15، workspace، API contract، route manifest، audience paths، theme manifest، required files، وquality gates.
- `App\Support\Ui\V15PublicRuntime`: service يعرض manifest ويفحص release readiness.
- `App\Http\Controllers\Public\PublicWebRuntimeController`: controller لعقد Next.js public runtime.
- Route: `/api/public-web/manifest`.
- `apps/public-web`: Next.js/React/TypeScript/Tailwind scaffold.
- `docs/kabeeri/ui/V15_NEXT_PUBLIC_RUNTIME.md`: شرح runtime boundary وملفات scaffold.
- `docs/kabeeri/ui/V15_RELEASE_CANDIDATE.md`: Go/No-Go gates وأوامر التحقق.
- `tests/Feature/V15PublicRuntimeTest.php`: اختبارات manifest contract، route registry، files/scripts، وrelease readiness.

## قواعد UI

- لا تعرض كل المنصة دفعة واحدة للجمهور.
- افصل الأدمن الداخلي عن العرض العام.
- UI الأدمن context-first وpermission-aware.
- public themes طويلة المدى Next.js/React/Tailwind.
- Blade الحالي fallback/bridge فقط.
- Filament هو مكان الإدارة الداخلية.
