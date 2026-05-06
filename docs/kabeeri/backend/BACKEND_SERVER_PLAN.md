# Backend and Server Plan

## القرار المعماري

Kabeeri Backend يبدأ كـ Laravel API-first Modular Monolith.

المعنى العملي:

- Laravel يملك الداتا، الصلاحيات، الـ domain logic، الـ jobs، الـ events، والـ APIs.
- Filament هو لوحة الإدارة الداخلية.
- الواجهات العامة طويلة المدى تقرأ من API ولا تعتمد على Laravel Blade كثيم رئيسي.
- OpenAPI يصبح عقدًا مهمًا عند بناء Next.js/Mobile/Desktop/Developer integrations.

## Stack

- PHP 8.3+.
- Laravel الحالي في المشروع هو المرجع التنفيذي النشط.
- MySQL 8+ كهدف إنتاجي.
- SQLite مقبول محليًا/اختباريًا حسب إعداد المشروع.
- Filament Admin.
- REST JSON APIs.
- Sanctum مبدئيًا، OAuth لاحقًا.
- Queue-ready: database queue أولًا، Redis لاحقًا.
- Cache-ready: file/database أولًا، Redis لاحقًا.
- PHPUnit/Pest + Feature/Policy tests.
- Laravel Pint، وLarastan/PHPStan لاحقًا.

## Module Structure

الهيكل المنطقي المستهدف:

- `app/Core`: Auth, Users, Organizations, Permissions, Sites, Settings, FeatureFlags, Activity, Media, Notifications, Packages, Localization.
- `app/CMS`: Content, Taxonomies, SEO, Themes, Forms, Redirects, Menus.
- `app/Rabet`: Companies, BusinessProfiles, Verification, Trust.
- `app/Commerce`: Products, Cart, Orders, Customers, Payments.
- `app/ERP`: CRM, Sales, Invoicing, Inventory, Purchasing, Accounting, HR.
- `app/Operating`: Workflows, Search, Moderation, Reviews, Disputes.
- `app/Platform`: Cloud, Billing, Marketplace, Mall, Talent, Teams, SecurityCenter, AI, Integrations, DeveloperConsole.
- `app/Enterprise`: BI, GRC, DataPlatform, IndustrySolutions.

## Feature Implementation Chain

أي Feature لازم تمر بالسلسلة دي:

1. Scope.
2. Schema.
3. Migration.
4. Model.
5. Policy/Permissions.
6. Action/Service.
7. UI/API.
8. Tests + manual check.

## قواعد مهمة

- ممنوع business logic داخل controllers أو Filament resources.
- استخدم Actions صغيرة قابلة للاختبار.
- استخدم Services للتنسيق أو منطق domain reusable فقط.
- استخدم Events/Listeners بين الموديلات بدل الاستدعاء العشوائي.
- كل فعل حساس يحتاج Policy/Permission وAudit Log.
- كل فعل عام مهم يحتاج Activity Log.
- لا تضع `organization_id` أو `company_id` أو `site_id` أو `role` مباشرة داخل `users`.

## API Direction

- APIs الخاصة يجب أن تكون scoped.
- APIs العامة يجب أن تكون visibility-aware وrate-limited.
- OpenAPI مطلوب قبل اعتماد Next.js/mobile/desktop على endpoints.
