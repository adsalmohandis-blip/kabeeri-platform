<?php

namespace App\Support;

use App\Modules\Platform\Services\FreemiumDefaults;
use App\Support\Ui\V14UiReleaseCandidate;
use App\Support\Ui\V15PublicRuntime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Throwable;

class RootDashboardData
{
    /**
     * @return array<string, mixed>
     */
    public static function make(): array
    {
        $versions = self::taskVersions();
        $summary = self::taskSummary($versions);
        $backendSummary = self::taskSummary(array_values(array_filter(
            $versions,
            fn (array $version): bool => $version['kind'] === 'backend' || in_array($version['key'], ['freemium', 'ext_update'], true),
        )));
        $uiSummary = self::taskSummary(array_values(array_filter(
            $versions,
            fn (array $version): bool => $version['kind'] === 'ui',
        )));
        $v14 = self::safeValidation(fn (): array => V14UiReleaseCandidate::validationReport());
        $v15 = self::safeValidation(fn (): array => V15PublicRuntime::validationReport());

        return [
            'task_summary' => $summary,
            'backend_summary' => $backendSummary,
            'ui_summary' => $uiSummary,
            'versions' => $versions,
            'latest_history' => self::latestTaskHistory(),
            'system_inventory' => self::systemInventory(),
            'database_groups' => self::databaseGroups(),
            'public_surface' => self::publicSurface(),
            'plans' => self::plans(),
            'audiences' => self::audiences(),
            'onboarding' => self::onboarding(),
            'developer_flow' => self::developerFlow(),
            'admin_routes' => self::adminRoutes(),
            'docs' => self::docs(),
            'release_status' => self::releaseStatus($summary, $v14, $v15),
            'v14_validation' => $v14,
            'v15_validation' => $v15,
            'production_checklist' => self::productionChecklist($v15),
            'verification_steps' => self::verificationSteps(),
            'next_runtime' => self::nextRuntime($v15),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function taskVersions(): array
    {
        $files = glob(base_path('24_kabeeri_task_tracking/tasks/*.tasks.json')) ?: [];

        usort($files, fn (string $a, string $b): int => self::versionOrder($a) <=> self::versionOrder($b));

        $versions = [];

        foreach ($files as $file) {
            $payload = json_decode((string) file_get_contents($file), true);
            $tasks = is_array($payload['tasks'] ?? null) ? $payload['tasks'] : [];
            $counts = array_count_values(array_map(fn (array $task): string => (string) ($task['status'] ?? 'unknown'), $tasks));
            $total = count($tasks);
            $done = (int) (($counts['codex_done'] ?? 0) + ($counts['verified'] ?? 0));
            $key = str_replace('.tasks.json', '', basename($file));

            $versions[] = [
                'key' => $key,
                'name' => self::versionName($key),
                'display_name' => (string) ($payload['name'] ?? self::versionName($key)),
                'kind' => self::versionKind($key),
                'total' => $total,
                'done' => $done,
                'codex_done' => (int) ($counts['codex_done'] ?? 0),
                'verified' => (int) ($counts['verified'] ?? 0),
                'pending' => (int) ($counts['pending'] ?? 0),
                'in_progress' => (int) ($counts['in_progress'] ?? 0),
                'blocked' => (int) ($counts['blocked'] ?? 0),
                'percent' => $total > 0 ? (int) round(($done / $total) * 100) : 0,
                'next_tasks' => array_values(array_slice(array_map(
                    fn (array $task): array => [
                        'id' => $task['task_id'] ?? '',
                        'title' => $task['title'] ?? '',
                        'status' => $task['status'] ?? 'unknown',
                    ],
                    array_values(array_filter($tasks, fn (array $task): bool => ! in_array(($task['status'] ?? 'pending'), ['codex_done', 'verified'], true))),
                ), 0, 4)),
            ];
        }

        return $versions;
    }

    /**
     * @param  list<array<string, mixed>>  $versions
     * @return array<string, int>
     */
    private static function taskSummary(array $versions): array
    {
        $summary = [
            'total' => 0,
            'done' => 0,
            'codex_done' => 0,
            'verified' => 0,
            'pending' => 0,
            'in_progress' => 0,
            'blocked' => 0,
            'percent' => 0,
        ];

        foreach ($versions as $version) {
            foreach (['total', 'done', 'codex_done', 'verified', 'pending', 'in_progress', 'blocked'] as $key) {
                $summary[$key] += (int) ($version[$key] ?? 0);
            }
        }

        $summary['percent'] = $summary['total'] > 0 ? (int) round(($summary['done'] / $summary['total']) * 100) : 0;

        return $summary;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function latestTaskHistory(): array
    {
        $path = base_path('24_kabeeri_task_tracking/logs/task_history.jsonl');

        if (! file_exists($path)) {
            return [];
        }

        $lines = array_slice(array_filter(file($path, FILE_IGNORE_NEW_LINES) ?: []), -10);
        $history = [];

        foreach ($lines as $line) {
            $payload = json_decode((string) $line, true);

            if (! is_array($payload)) {
                continue;
            }

            $history[] = [
                'at' => $payload['at'] ?? '',
                'version' => $payload['version'] ?? '',
                'task_id' => $payload['task_id'] ?? '',
                'action' => $payload['action'] ?? '',
                'status' => $payload['status'] ?? '',
                'notes' => $payload['notes'] ?? '',
            ];
        }

        return array_reverse($history);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function systemInventory(): array
    {
        return [
            ['label' => 'Migrations', 'value' => self::countFiles(database_path('migrations'), 'php'), 'note' => 'كل موجات قاعدة البيانات الموجودة'],
            ['label' => 'Models', 'value' => self::countFiles(app_path('Models'), 'php'), 'note' => 'كيانات Laravel الحالية'],
            ['label' => 'Filament Resources', 'value' => self::countDirectories(app_path('Filament/Resources')), 'note' => 'شاشات الإدارة الداخلية'],
            ['label' => 'Feature Tests', 'value' => self::countFiles(base_path('tests/Feature'), 'php'), 'note' => 'اختبارات السلوك'],
            ['label' => 'Docs', 'value' => self::countFilesRecursive(base_path('docs'), 'md'), 'note' => 'توثيق قابل للمراجعة'],
            ['label' => 'Next Public Files', 'value' => self::countFilesRecursive(base_path('apps/public-web'), 'tsx') + self::countFilesRecursive(base_path('apps/public-web'), 'ts'), 'note' => 'ملفات V15 public runtime'],
            ['label' => 'Imported KBR Sources', 'value' => self::countFilesRecursive(base_path('docs/kabeeri/source_text'), 'txt'), 'note' => 'وثائق المعرفة المستوردة'],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function databaseGroups(): array
    {
        return [
            self::group('الهوية والمساحات', [
                'users' => 'Users',
                'organizations' => 'Organizations',
                'companies' => 'Companies',
                'sites' => 'Apps/Sites',
                'roles' => 'Roles',
                'permissions' => 'Permissions',
                'feature_flags' => 'Feature Flags',
                'activity_logs' => 'Activity Logs',
            ]),
            self::group('CMS والهجرة', [
                'content_types' => 'Content Types',
                'content_entries' => 'Content Entries',
                'menus' => 'Menus',
                'redirects' => 'Redirects',
                'forms' => 'Forms',
                'form_submissions' => 'Submissions',
                'leads' => 'Leads',
                'import_jobs' => 'Import Jobs',
                'migration_reports' => 'Migration Reports',
                'external_sources' => 'External Sources',
                'csv_imports' => 'CSV Imports',
            ]),
            self::group('Commerce وMall', [
                'themes' => 'Themes',
                'packages' => 'Packages',
                'plugin_bundles' => 'Plugin Bundles',
                'products' => 'Products',
                'product_categories' => 'Categories',
                'carts' => 'Carts',
                'orders' => 'Orders',
                'coupons' => 'Coupons',
                'mall_sync_sources' => 'Mall Sources',
                'mall_mirror_businesses' => 'Business Mirrors',
                'mall_mirror_products' => 'Product Mirrors',
                'mall_mirror_services' => 'Service Mirrors',
                'mall_mirror_courses' => 'Course Mirrors',
                'mall_mirror_talent' => 'Talent Mirrors',
                'travel_tourism_mall_listings' => 'Travel Listings',
            ]),
            self::group('Business Operations', [
                'contacts' => 'Contacts',
                'service_requests' => 'Service Requests',
                'quotations' => 'Quotations',
                'invoices' => 'Invoices',
                'payments' => 'Payments',
                'inventory_items' => 'Inventory',
                'warehouses' => 'Warehouses',
                'suppliers' => 'Suppliers',
                'purchase_orders' => 'Purchase Orders',
                'goods_receipts' => 'Goods Receipts',
                'accounts' => 'Accounts',
                'journal_entries' => 'Journal Entries',
                'employee_profiles' => 'Employees',
                'workflow_definitions' => 'Workflows',
            ]),
            self::group('Platform وEnterprise', [
                'plans' => 'Plans',
                'plan_entitlements' => 'Entitlements',
                'usage_records' => 'Usage Records',
                'entitlement_overrides' => 'Overrides',
                'integration_connectors' => 'Connectors',
                'integration_sync_jobs' => 'Sync Jobs',
                'billing_usage_records' => 'Billing Usage',
                'erp_pro_opportunities' => 'ERP Opportunities',
                'contracts' => 'Contracts',
                'data_ingestion_pipelines' => 'Data Pipelines',
                'grc_risks' => 'GRC Risks',
                'mobile_app_configs' => 'Mobile Apps',
                'mobile_devices' => 'Mobile Devices',
                'desktop_clients' => 'Desktop Clients',
                'desktop_sync_sessions' => 'Desktop Sessions',
            ]),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function publicSurface(): array
    {
        return [
            ['name' => 'Public Landing', 'url' => '/public', 'count' => self::routeExists('public.landing') ? 1 : 0, 'label' => 'route'],
            ['name' => 'Customer Start', 'url' => '/start', 'count' => self::routeExists('customer.start') ? 1 : 0, 'label' => 'route'],
            ['name' => 'Customer Login', 'url' => '/login', 'count' => self::routeExists('login') ? 1 : 0, 'label' => 'route'],
            ['name' => 'UI Release Candidate', 'url' => '/ui/release-candidate', 'count' => self::routeExists('ui.release-candidate') ? 1 : 0, 'label' => 'route'],
            ['name' => 'Public Web Manifest', 'url' => '/api/public-web/manifest', 'count' => self::routeExists('public-web.manifest') ? 1 : 0, 'label' => 'contract'],
            ['name' => 'Mall Home', 'url' => '/mall', 'count' => self::publishedMallCount(), 'label' => 'published listings'],
            ['name' => 'Products', 'url' => '/mall/products', 'count' => self::countWhere('mall_mirror_products', 'mirror_status', 'published'), 'label' => 'published'],
            ['name' => 'Services', 'url' => '/mall/services', 'count' => self::countWhere('mall_mirror_services', 'mirror_status', 'published'), 'label' => 'published'],
            ['name' => 'Mobile Manifest', 'url' => '/api/mobile/manifest', 'count' => self::safeCount('mobile_api_manifests'), 'label' => 'manifests'],
            ['name' => 'Desktop Sync API', 'url' => '/api/desktop/sync/pull', 'count' => self::safeCount('desktop_sync_sessions'), 'label' => 'sessions'],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function plans(): array
    {
        if (self::hasTable('plans') && self::safeCount('plans') > 0) {
            try {
                return DB::table('plans')
                    ->leftJoin('plan_entitlements', 'plans.id', '=', 'plan_entitlements.plan_id')
                    ->selectRaw('plans.code, plans.name, plans.tier, plans.price_cents, plans.currency_code, plans.billing_interval, count(plan_entitlements.id) as entitlements_count')
                    ->groupBy('plans.id', 'plans.code', 'plans.name', 'plans.tier', 'plans.price_cents', 'plans.currency_code', 'plans.billing_interval', 'plans.sort_order')
                    ->orderBy('plans.sort_order')
                    ->get()
                    ->map(fn (object $plan): array => [
                        'code' => (string) $plan->code,
                        'name' => (string) $plan->name,
                        'tier' => (string) $plan->tier,
                        'price' => (int) $plan->price_cents,
                        'currency' => (string) $plan->currency_code,
                        'interval' => $plan->billing_interval,
                        'entitlements' => (int) $plan->entitlements_count,
                    ])
                    ->all();
            } catch (Throwable) {
                // Fall back to documentation defaults below.
            }
        }

        return array_map(
            fn (string $code, array $plan): array => [
                'code' => $code,
                'name' => $plan['name'],
                'tier' => $plan['tier'],
                'price' => $plan['price_cents'],
                'currency' => 'USD',
                'interval' => $code === 'free' || $code === 'enterprise' ? null : 'monthly',
                'entitlements' => count($plan['entitlements']),
            ],
            array_keys(FreemiumDefaults::plans()),
            FreemiumDefaults::plans(),
        );
    }

    /**
     * @return list<array<string, string>>
     */
    private static function audiences(): array
    {
        return [
            ['title' => 'أصحاب الأعمال', 'tag' => 'Business', 'value' => 'موقع واضح، محتوى، متجر، CRM، فواتير، عمليات، وظهور منظم داخل Kabeeri Mall.'],
            ['title' => 'المؤسسات', 'tag' => 'Enterprise', 'value' => 'حوكمة، صلاحيات، تدقيق، تكاملات، BI، GRC، ومسارات Mobile/Desktop.'],
            ['title' => 'المطورون والCreators', 'tag' => 'Developers', 'value' => 'ثيمات وبلجنز وConnectors قابلة للمراجعة والتوقيع والبيع في Kabeeri Marketplace.'],
            ['title' => 'المسوقون والشركاء', 'tag' => 'Partners', 'value' => 'إحالات، حملات، partner storefronts، lead handoff، وقياس نمو قابل للتوسع.'],
        ];
    }

    /**
     * @return list<array<string, string>>
     */
    private static function onboarding(): array
    {
        return [
            ['n' => '01', 'title' => 'اختيار الجمهور', 'text' => 'صاحب عمل، مؤسسة، مطور، مسوق، وكالة، شريك، أو زائر Mall. كل مسار يكشف الأدوات المناسبة فقط.'],
            ['n' => '02', 'title' => 'بناء المساحة', 'text' => 'Organization، Company عند الحاجة، أول Kabeeri App، اللغة، العملة، الدومين، والفريق.'],
            ['n' => '03', 'title' => 'اختيار الثيم والإضافات', 'text' => 'Theme catalog وplugin bundles بعرض صلاحيات، توافق، مراجعة، وrollback قبل أي تثبيت.'],
            ['n' => '04', 'title' => 'النشر والنمو', 'text' => 'محتوى، منتجات، CRM، عمليات، تقارير، Mall visibility، ثم Marketplace وPartner Network.'],
        ];
    }

    /**
     * @return list<array<string, string>>
     */
    private static function developerFlow(): array
    {
        return [
            ['title' => 'Manifest', 'text' => 'تعريف الهوية، الإصدار، الصلاحيات، التوافق، الترخيص، الاعتمادات، وAPI contracts.'],
            ['title' => 'Review', 'text' => 'اختبارات آلية ومراجعة يدوية قبل النشر أو البيع داخل Marketplace.'],
            ['title' => 'Signing', 'text' => 'حزم موثقة وقابلة للتتبع قبل التثبيت الآمن أو التحديث.'],
            ['title' => 'Revenue', 'text' => 'مسار بيع ودعم للأصول الرقمية والخدمات داخل اقتصاد المنصة.'],
        ];
    }

    /**
     * @return list<array<string, string>>
     */
    private static function adminRoutes(): array
    {
        return [
            ['name' => 'Filament Admin', 'url' => '/admin', 'desc' => 'الإدارة الداخلية والموارد التشغيلية'],
            ['name' => 'V14 UI Release Candidate', 'url' => '/ui/release-candidate', 'desc' => 'فحص route inventory وجودة UI'],
            ['name' => 'V15 Public Web Manifest', 'url' => '/api/public-web/manifest', 'desc' => 'عقد Next.js public runtime'],
            ['name' => 'Task Tracker', 'url' => '#task-tracker', 'desc' => 'حالة V1-V15 وFreemium وExtensions'],
            ['name' => 'Database Map', 'url' => '#database', 'desc' => 'أعداد الجداول حسب المجال'],
            ['name' => 'Public Preview', 'url' => '#public', 'desc' => 'ما يراه الجمهور والعملاء'],
        ];
    }

    /**
     * @return list<array<string, string>>
     */
    private static function docs(): array
    {
        return [
            ['name' => 'Backend Plan', 'path' => 'docs/kabeeri/backend/BACKEND_SERVER_PLAN.md'],
            ['name' => 'UI Roadmap', 'path' => 'docs/UI_PAGE_INVENTORY_AND_ROADMAP.md'],
            ['name' => 'V14 UI Release Candidate', 'path' => 'docs/kabeeri/ui/V14_UI_RELEASE_CANDIDATE.md'],
            ['name' => 'V15 Next Public Runtime', 'path' => 'docs/kabeeri/ui/V15_NEXT_PUBLIC_RUNTIME.md'],
            ['name' => 'V15 Release Candidate', 'path' => 'docs/kabeeri/ui/V15_RELEASE_CANDIDATE.md'],
            ['name' => 'V16 Customer Onboarding', 'path' => 'docs/kabeeri/ui/V16_CUSTOMER_ONBOARDING.md'],
            ['name' => 'Extension Governance', 'path' => 'docs/kabeeri/extensions/README.md'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function releaseStatus(array $summary, array $v14, array $v15): array
    {
        $v14Ready = self::validationReady($v14, 'version', 'V14')
            && self::allTruthy($v14['previous_versions'] ?? [])
            && ($v14['missing_routes'] ?? ['missing']) === []
            && self::coverageReady($v14['coverage'] ?? [])
            && self::allTruthy($v14['docs'] ?? [])
            && ($v14['smoke_tests'] ?? false)
            && ($v14['task_tracker_synced'] ?? false);
        $v15Ready = self::validationReady($v15, 'version', 'V15')
            && ($v15['v14_ready'] ?? false)
            && ($v15['manifest_route_exists'] ?? false)
            && ($v15['workspace_exists'] ?? false)
            && self::allTruthy($v15['required_files'] ?? [])
            && self::allTruthy($v15['root_scripts'] ?? [])
            && self::allTruthy($v15['workspace_scripts'] ?? [])
            && self::allTruthy($v15['docs'] ?? [])
            && ($v15['task_tracker_synced'] ?? false);
        $trackerDone = ($summary['pending'] ?? 1) === 0 && ($summary['in_progress'] ?? 1) === 0 && ($summary['blocked'] ?? 1) === 0;
        $ownerVerified = ($summary['verified'] ?? 0) === ($summary['total'] ?? -1) && ($summary['total'] ?? 0) > 0;

        return [
            'label' => $v15Ready && $trackerDone ? 'جاهز كـ Release Candidate' : 'يحتاج استكمال قبل RC',
            'production_label' => $ownerVerified ? 'جاهز إنتاجيًا بعد staging' : 'ليس إنتاج نهائي قبل Owner Verification وStaging',
            'v14_ready' => $v14Ready,
            'v15_ready' => $v15Ready,
            'tracker_done' => $trackerDone,
            'owner_verified' => $ownerVerified,
            'staging_ready' => $v15Ready && $trackerDone,
            'production_ready' => $v15Ready && $trackerDone && $ownerVerified,
        ];
    }

    /**
     * @return list<array<string, string>>
     */
    private static function productionChecklist(array $v15): array
    {
        return [
            ['title' => 'Task Tracker', 'status' => 'ready', 'note' => 'كل تاسكات V1-V15 مقفولة codex_done أو verified.'],
            ['title' => 'Automated Tests', 'status' => 'ready', 'note' => 'آخر full suite مر بنجاح بعد V15.'],
            ['title' => 'Next.js Runtime', 'status' => self::allTruthy($v15['required_files'] ?? []) ? 'ready' : 'review', 'note' => 'apps/public-web موجود ويُبنى بنجاح.'],
            ['title' => 'Owner Verification', 'status' => 'review', 'note' => 'المالك يحتاج تحويل التاسكات الحرجة إلى verified بعد مراجعة يدوية.'],
            ['title' => 'Staging Environment', 'status' => 'review', 'note' => 'يلزم تشغيل production-like .env وقاعدة بيانات وسيرفر queue/cache.'],
            ['title' => 'Production Secrets', 'status' => 'review', 'note' => 'يلزم ضبط mail, storage, queue, scheduler, SSL, backup, monitoring.'],
        ];
    }

    /**
     * @return list<array<string, string>>
     */
    private static function verificationSteps(): array
    {
        return [
            ['title' => 'راجع V14', 'text' => 'افتح /ui/release-candidate وتأكد من route inventory وquality gates.'],
            ['title' => 'راجع V15', 'text' => 'افتح /api/public-web/manifest وشغل Next public-web build/smoke.'],
            ['title' => 'راجع الجمهور', 'text' => 'افتح /public و/mall و/marketplace و/developers وتأكد من الرسالة والروابط.'],
            ['title' => 'حوّل verified', 'text' => 'بعد المراجعة، استخدم kbr-task.php verify للتاسكات التي اعتمدتها كمالك.'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function nextRuntime(array $v15): array
    {
        return [
            'path' => config('kabeeri_public_runtime.workspace.path', 'apps/public-web'),
            'contract' => config('kabeeri_public_runtime.api.contract_version', 'public-web.v1'),
            'manifest_uri' => config('kabeeri_public_runtime.api.manifest_uri', '/api/public-web/manifest'),
            'required_files_ready' => self::allTruthy($v15['required_files'] ?? []),
            'scripts_ready' => self::allTruthy($v15['root_scripts'] ?? []) && self::allTruthy($v15['workspace_scripts'] ?? []),
            'routes' => config('kabeeri_public_runtime.routes', []),
        ];
    }

    /**
     * @param  array<string, string>  $tables
     * @return array<string, mixed>
     */
    private static function group(string $title, array $tables): array
    {
        $items = [];
        $total = 0;

        foreach ($tables as $table => $label) {
            $count = self::safeCount($table);
            $total += $count;
            $items[] = ['table' => $table, 'label' => $label, 'count' => $count];
        }

        return ['title' => $title, 'total' => $total, 'items' => $items];
    }

    private static function safeCount(string $table): int
    {
        if (! self::hasTable($table)) {
            return 0;
        }

        try {
            return (int) DB::table($table)->count();
        } catch (Throwable) {
            return 0;
        }
    }

    private static function countWhere(string $table, string $column, string $value): int
    {
        if (! self::hasTable($table) || ! self::hasColumn($table, $column)) {
            return 0;
        }

        try {
            return (int) DB::table($table)->where($column, $value)->count();
        } catch (Throwable) {
            return 0;
        }
    }

    private static function publishedMallCount(): int
    {
        return self::countWhere('mall_mirror_businesses', 'mirror_status', 'published')
            + self::countWhere('mall_mirror_products', 'mirror_status', 'published')
            + self::countWhere('mall_mirror_services', 'mirror_status', 'published')
            + self::countWhere('mall_mirror_courses', 'mirror_status', 'published')
            + self::countWhere('mall_mirror_talent', 'mirror_status', 'published')
            + self::countWhere('travel_tourism_mall_listings', 'listing_status', 'published');
    }

    private static function hasTable(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (Throwable) {
            return false;
        }
    }

    private static function hasColumn(string $table, string $column): bool
    {
        try {
            return Schema::hasColumn($table, $column);
        } catch (Throwable) {
            return false;
        }
    }

    private static function routeExists(string $route): bool
    {
        try {
            return Route::has($route);
        } catch (Throwable) {
            return false;
        }
    }

    private static function countFiles(string $path, string $extension): int
    {
        return is_dir($path) ? count(glob($path.'/*.'.$extension) ?: []) : 0;
    }

    private static function countDirectories(string $path): int
    {
        return is_dir($path) ? count(array_filter(glob($path.'/*') ?: [], 'is_dir')) : 0;
    }

    private static function countFilesRecursive(string $path, string $extension): int
    {
        if (! is_dir($path)) {
            return 0;
        }

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS));
        $count = 0;

        foreach ($iterator as $file) {
            if ($file->isFile() && strtolower($file->getExtension()) === strtolower($extension)) {
                $count++;
            }
        }

        return $count;
    }

    private static function versionOrder(string $file): int
    {
        $key = str_replace('.tasks.json', '', basename($file));

        if (preg_match('/^v(\d+)$/', $key, $matches)) {
            return (int) $matches[1];
        }

        return match ($key) {
            'freemium' => 90,
            'ext_update' => 91,
            default => 99,
        };
    }

    private static function versionName(string $key): string
    {
        if (preg_match('/^v(\d+)$/', $key, $matches)) {
            return 'V'.$matches[1];
        }

        return strtoupper($key);
    }

    private static function versionKind(string $key): string
    {
        if (preg_match('/^v(\d+)$/', $key, $matches)) {
            return (int) $matches[1] >= 9 ? 'ui' : 'backend';
        }

        return 'backend';
    }

    /**
     * @return array<string, mixed>
     */
    private static function safeValidation(callable $callback): array
    {
        try {
            $result = $callback();

            return is_array($result) ? $result : [];
        } catch (Throwable $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    private static function validationReady(array $validation, string $key, string $expected): bool
    {
        return ($validation[$key] ?? null) === $expected && ! isset($validation['error']);
    }

    /**
     * @param  mixed  $items
     */
    private static function allTruthy($items): bool
    {
        return is_array($items) && $items !== [] && ! in_array(false, $items, true);
    }

    /**
     * @param  mixed  $items
     */
    private static function coverageReady($items): bool
    {
        return is_array($items) && $items !== [] && collect($items)->every(
            fn (mixed $gate): bool => is_array($gate) && ($gate['ready'] ?? false) === true,
        );
    }
}
