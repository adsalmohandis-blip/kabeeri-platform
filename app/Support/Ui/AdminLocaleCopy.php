<?php

namespace App\Support\Ui;

class AdminLocaleCopy
{
    /**
     * @var array<string, array<string, string>>
     */
    private const LABELS = [
        'ar' => [
            'Dashboard' => 'الرئيسية',
            'User settings' => 'إعدادات المستخدم',
            'Account data' => 'بيانات الحساب',
            'Interface settings' => 'إعدادات الواجهة',
            'Admin language' => 'لغة لوحة الأدمن',
            'Admin font' => 'خط لوحة الأدمن',
            'Security' => 'الأمان',
            'User settings saved' => 'تم حفظ إعدادات المستخدم',
            'Platform Admin Dashboard' => 'لوحة أدمن المنصة',
            'Development status' => 'لوحة حالة التطوير',
            'Open development status' => 'فتح لوحة حالة التطوير',
            'Compact internal view' => 'ملخص داخلي مختصر لحالة التنفيذ والبيانات وجاهزية النشر.',
            'Current admin' => 'الأدمن الحالي',
            'Signed in' => 'جلسة نشطة',
            'Progress' => 'التقدم',
            'Done' => 'المنجز',
            'Release' => 'النشر',
            'Ready' => 'جاهز',
            'Review' => 'مراجعة',
            'Pending' => 'متبقي',
            'In progress' => 'قيد التنفيذ',
            'Blocked' => 'متوقف',
            'Needs review' => 'يحتاج مراجعة',
            'needs_attention' => 'يحتاج انتباه',
            'ready' => 'جاهز',
            'partial' => 'جزئي',
            'codex_done' => 'منجز',
            'verified' => 'موثق',
            'pending' => 'متبقي',
            'in_progress' => 'قيد التنفيذ',
            'blocked' => 'متوقف',
            'Total' => 'الإجمالي',
            'Total progress' => 'إجمالي التقدم',
            'Backend' => 'الباك إند',
            'User interfaces' => 'واجهات الاستخدام',
            'Release readiness' => 'جاهزية النشر',
            'Ready for review' => 'جاهز للمراجعة',
            'Needs follow-up' => 'يحتاج متابعة',
            'Internal paths' => 'المسارات الداخلية',
            'Admin shortcuts' => 'اختصارات لوحة الأدمن',
            'Current page' => 'الصفحة الحالية',
            'Quick summary' => 'ملخص سريع',
            'System inventory' => 'مخزون النظام',
            'Real progress' => 'التقدم الحقيقي',
            'Version status' => 'حالة النسخ',
            'Remaining' => 'متبقي',
            'Latest movement' => 'آخر حركة',
            'Tracker log' => 'سجل التتبع',
            'No recent log entries.' => 'لا يوجد سجل حديث.',
            'Publish gate' => 'بوابة النشر',
            'Verification checklist' => 'قائمة التأكيد',
            'Database' => 'قاعدة البيانات',
            'Key table groups' => 'أهم مجموعات الجداول',
            'Details' => 'التفاصيل',
            'Data group' => 'مجموعة بيانات',
            'Give the platform owner a quick truth snapshot before demos, releases, or deployment.' => 'لقطة واضحة لمالك المنصة قبل العروض أو الإصدارات أو النشر.',
            'Show implementation truth across V1-V14, Freemium, and extension update tracks.' => 'عرض حالة التنفيذ الحقيقية عبر النسخ والمسارات الإضافية.',
            'Expose migration and table readiness without asking the admin to inspect code.' => 'إظهار جاهزية الهجرات والجداول بدون الرجوع للكود.',
            'Group the platform by operational domains and show health, records, and next action.' => 'تقسيم المنصة حسب المجالات التشغيلية مع الحالة والخطوة التالية.',
            'Make Go/No-Go visible with tests, docs, task tracker, build, and UX gates.' => 'توضيح قرار النشر من خلال الاختبارات والتوثيق والتتبع وتجربة الاستخدام.',
            'Show role-specific landing paths and context switching model for every admin space.' => 'عرض مسارات الدخول الخاصة بكل دور ومساحة إدارية.',

            'V10 Admin Command' => 'قيادة التطوير',
            'Core' => 'النواة',
            'Organizations' => 'المؤسسات',
            'Apps' => 'التطبيقات',
            'Content' => 'المحتوى',
            'Media' => 'الوسائط',
            'Rabet Foundation' => 'رابط',
            'CRM' => 'إدارة العملاء',
            'Sales & Invoicing' => 'المبيعات والفواتير',
            'Inventory & Purchasing' => 'المخزون والمشتريات',
            'Workflows' => 'سير العمل',
            'Reports' => 'التقارير',
            'Freemium' => 'الاشتراكات المجانية',
            'V2 CMS' => 'إدارة المحتوى',
            'V2 Commerce' => 'التجارة',
            'V2 Migration' => 'الهجرة',
            'V4 Marketplace' => 'متجر الإضافات',
            'V4 Moderation' => 'المراجعة',
            'V4 Partners' => 'الشركاء',
            'V4 Trust' => 'الثقة',
            'V5 ERP Pro' => 'تخطيط الموارد',
            'V5 Integration Hub' => 'مركز التكامل',
            'V6 Marketplace' => 'المتجر',
            'V6 Data Platform' => 'منصة البيانات',
            'V6 GRC' => 'الحوكمة والمخاطر',
            'System' => 'النظام',
            'People' => 'الأشخاص',

            'System Check' => 'فحص النظام',
            'Task Tracker' => 'تتبع التاسكات',
            'Task Tracker Status' => 'حالة تتبع التاسكات',
            'Database Status' => 'حالة قاعدة البيانات',
            'Module Health' => 'صحة الوحدات',
            'Release Readiness' => 'جاهزية الإصدار',
            'Admin Workspaces' => 'مساحات الأدمن',
            'Quick Actions' => 'إجراءات سريعة',
            'Permission-Aware Navigation' => 'تنقل حسب الصلاحيات',
            'Version Truth' => 'حقيقة النسخ',
            'Latest Tracker Events' => 'آخر أحداث التتبع',
            'Connection' => 'الاتصال',
            'Migration files' => 'ملفات الهجرة',
            'Applied' => 'المطبق',
            'Estimate pending' => 'تقدير المتبقي',
            'Go / No-Go Gates' => 'بوابات القرار',
            'Required Commands' => 'أوامر التحقق',
            'Open' => 'فتح',
            'Requires' => 'يتطلب',
            'records' => 'سجلات',
            'tables' => 'جداول',
            'missing' => 'غير موجود',
            'Open System Check' => 'فتح فحص النظام',
            'Review Task Tracker' => 'مراجعة تتبع التاسكات',
            'Inspect Database Status' => 'فحص حالة قاعدة البيانات',
            'Review Release Gate' => 'مراجعة بوابة النشر',
            'Manage Plans' => 'إدارة الخطط',
            'Review Approvals' => 'مراجعة الموافقات',
            'Hidden links are not security. Policies, permissions, confirmations, and audit logs must enforce sensitive actions.' => 'إخفاء الروابط ليس حماية. السياسات والصلاحيات والتأكيدات وسجلات النشاط هي مصدر الحماية.',
            'Manage billing or upgrade plan' => 'إدارة الفوترة أو ترقية الخطة',
            'Approve company verification' => 'اعتماد توثيق الشركة',
            'Post journal entry' => 'ترحيل قيد مالي',
            'Assign user role' => 'تعيين دور مستخدم',
            'Install plugin/package' => 'تثبيت إضافة أو حزمة',
            'Apply sensitive AI change' => 'تطبيق تغيير حساس بالذكاء الاصطناعي',
            'Billing changes affect subscription, limits, and invoices.' => 'تغييرات الفوترة تؤثر على الاشتراك والحدود والفواتير.',
            'Verification changes public trust badges and Mall reputation.' => 'التوثيق يؤثر على شارات الثقة وسمعة الظهور العام.',
            'Financial posting must be auditable and reversible only through governed flows.' => 'الترحيل المالي يجب أن يكون قابلا للمراجعة ولا يعدل إلا بمسار منظم.',
            'Role assignment changes data access across workspaces.' => 'تعيين الأدوار يغير الوصول للبيانات داخل المساحات.',
            'Packages may request data permissions and migrations.' => 'الحزم قد تطلب صلاحيات بيانات وهجرات.',
            'AI suggestions that affect finance, payroll, security, or legal data require explicit approval.' => 'اقتراحات الذكاء الاصطناعي الحساسة تحتاج موافقة صريحة.',

            'Laravel application' => 'تطبيق لارافيل',
            'V9 UI foundation' => 'أساس واجهات V9',
            'Database connection' => 'اتصال قاعدة البيانات',
            'Task tracker files' => 'ملفات تتبع التاسكات',
            'Filament resources' => 'موارد الأدمن',
            'V10 docs' => 'توثيق V10',
            'Application container is booted.' => 'حاوية التطبيق تعمل.',
            'UI boundaries and tokens are available.' => 'حدود الواجهات والتوكنز متاحة.',
            'Connection responds to a simple query.' => 'الاتصال يستجيب لاستعلام بسيط.',
            'Task files are available for admin visibility.' => 'ملفات التاسكات متاحة للعرض الإداري.',
            'Admin resources discovered in code.' => 'موارد الأدمن مكتشفة في الكود.',
            'Admin UX rules and release criteria are documented.' => 'قواعد تجربة الأدمن ومعايير الإصدار موثقة.',
            'Documented' => 'موثق',

            'Migrations' => 'الهجرات',
            'Models' => 'النماذج',
            'Filament Resources' => 'موارد الأدمن',
            'Feature Tests' => 'اختبارات السلوك',
            'UI Docs' => 'توثيق الواجهات',
            'Docs' => 'التوثيق',

            'Activity Logs' => 'سجل النشاط',
            'Activity Log' => 'سجل نشاط',
            'Agency Partners' => 'شركاء الوكالات',
            'Approval Requests' => 'طلبات الموافقة',
            'Business Projects' => 'مشروعات الأعمال',
            'Companies' => 'الشركات',
            'Company' => 'شركة',
            'Contacts' => 'جهات الاتصال',
            'Content Entries' => 'مدخلات المحتوى',
            'Content Entry' => 'مدخل محتوى',
            'Content Types' => 'أنواع المحتوى',
            'Content Type' => 'نوع محتوى',
            'Coupons' => 'الكوبونات',
            'Dashboard Widgets' => 'ودجات اللوحة',
            'Data Pipelines' => 'مسارات البيانات',
            'Departments' => 'الأقسام',
            'Developer Marketplace' => 'متجر المطورين',
            'Employee Profiles' => 'ملفات الموظفين',
            'ERP Pro Opportunities' => 'فرص تخطيط الموارد',
            'Feature Flags' => 'أعلام المميزات',
            'Feature Flag' => 'علم ميزة',
            'Forms' => 'النماذج',
            'Goods Receipts' => 'استلام البضائع',
            'GRC Risks' => 'مخاطر الحوكمة',
            'Import Jobs' => 'مهام الاستيراد',
            'Integration Connectors' => 'موصلات التكامل',
            'Inventory Items' => 'عناصر المخزون',
            'Invoices' => 'الفواتير',
            'Leads' => 'العملاء المحتملون',
            'Marketplace Catalog' => 'كتالوج المتجر',
            'Media Library' => 'مكتبة الوسائط',
            'Media Asset' => 'ملف وسائط',
            'Media Assets' => 'ملفات الوسائط',
            'CMS Menus' => 'قوائم المحتوى',
            'Moderation Cases' => 'حالات المراجعة',
            'Orders' => 'الطلبات',
            'Organization' => 'مؤسسة',
            'Partner Storefronts' => 'واجهات الشركاء',
            'Payments' => 'المدفوعات',
            'Plans & Entitlements' => 'الخطط والاستحقاقات',
            'Products' => 'المنتجات',
            'Purchase Orders' => 'أوامر الشراء',
            'Quotations' => 'عروض الأسعار',
            'Redirects' => 'إعادة التوجيه',
            'Report Definitions' => 'تعريفات التقارير',
            'Reviews' => 'المراجعات',
            'Service Requests' => 'طلبات الخدمة',
            'Settings' => 'الإعدادات',
            'App' => 'تطبيق',
            'Suppliers' => 'الموردون',
            'Taxonomies' => 'التصنيفات',
            'Warehouses' => 'المخازن',
            'Workflow Definitions' => 'تعريفات سير العمل',
            'Members' => 'الأعضاء',
            'Overrides' => 'التجاوزات',
        ],
    ];

    public static function label(?string $label): string
    {
        $label = (string) $label;
        $locale = app()->getLocale();
        $normalizedLabel = self::normalizeLabel($label);

        return self::LABELS[$locale][$label]
            ?? self::LABELS[$locale][$normalizedLabel]
            ?? self::LABELS['en'][$label]
            ?? self::LABELS['en'][$normalizedLabel]
            ?? ($locale === 'ar' ? self::humanize($normalizedLabel) : $label);
    }

    public static function visible(?string $label, string $arabicFallback = 'تفاصيل داخلية'): string
    {
        $translated = self::label($label);

        if (app()->getLocale() !== 'ar') {
            return $translated;
        }

        return preg_match('/[A-Za-z]{3,}/', $translated) ? $arabicFallback : $translated;
    }

    public static function currentBrand(): string
    {
        return app()->getLocale() === 'ar' ? 'كبيري' : 'KABEERI';
    }

    private static function humanize(string $label): string
    {
        return str_replace(['_', '-'], ' ', $label);
    }

    private static function normalizeLabel(string $label): string
    {
        $label = trim(str_replace(['_', '-'], ' ', $label));

        if ($label === '') {
            return $label;
        }

        $label = preg_replace('/\s+/', ' ', $label) ?? $label;
        $label = ucwords($label);

        return trim(str_replace(
            [' Cms ', ' Erp ', ' Grc ', ' Ai ', ' Ui ', ' Crm ', ' V '],
            [' CMS ', ' ERP ', ' GRC ', ' AI ', ' UI ', ' CRM ', ' V'],
            " {$label} ",
        ));
    }
}
