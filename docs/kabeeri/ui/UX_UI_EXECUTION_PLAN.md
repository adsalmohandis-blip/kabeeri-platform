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
