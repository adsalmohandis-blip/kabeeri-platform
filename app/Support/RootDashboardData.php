<?php

namespace App\Support;

use App\Modules\Platform\Services\FreemiumDefaults;
use Illuminate\Support\Facades\DB;
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
            $pending = (int) ($counts['pending'] ?? 0);
            $inProgress = (int) ($counts['in_progress'] ?? 0);
            $blocked = (int) ($counts['blocked'] ?? 0);
            $verified = (int) ($counts['verified'] ?? 0);
            $codexDone = (int) ($counts['codex_done'] ?? 0);
            $key = str_replace('.tasks.json', '', basename($file));

            $versions[] = [
                'key' => $key,
                'name' => self::versionName($key),
                'kind' => self::versionKind($key),
                'total' => $total,
                'done' => $done,
                'codex_done' => $codexDone,
                'verified' => $verified,
                'pending' => $pending,
                'in_progress' => $inProgress,
                'blocked' => $blocked,
                'percent' => $total > 0 ? (int) round(($done / $total) * 100) : 0,
                'next_tasks' => array_values(array_slice(array_map(
                    fn (array $task): array => [
                        'id' => $task['task_id'] ?? '',
                        'title' => $task['title'] ?? '',
                        'status' => $task['status'] ?? 'unknown',
                    ],
                    array_values(array_filter($tasks, fn (array $task): bool => ($task['status'] ?? 'pending') !== 'codex_done' && ($task['status'] ?? '') !== 'verified')),
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

        $lines = array_slice(array_filter(file($path, FILE_IGNORE_NEW_LINES) ?: []), -8);
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
            ['label' => 'Migrations', 'value' => self::countFiles(database_path('migrations'), 'php'), 'note' => 'كل موجات قاعدة البيانات'],
            ['label' => 'Models', 'value' => self::countFiles(app_path('Models'), 'php'), 'note' => 'كيانات Laravel الحالية'],
            ['label' => 'Filament Resources', 'value' => self::countDirectories(app_path('Filament/Resources')), 'note' => 'شاشات الإدارة'],
            ['label' => 'Feature Tests', 'value' => self::countFiles(base_path('tests/Feature'), 'php'), 'note' => 'اختبارات السلوك'],
            ['label' => 'Docs', 'value' => self::countFilesRecursive(base_path('docs'), 'md'), 'note' => 'توثيق قابل للقراءة'],
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
            ['name' => 'Mall Home', 'url' => '/mall', 'count' => self::publishedMallCount(), 'label' => 'published listings'],
            ['name' => 'Products', 'url' => '/mall/products', 'count' => self::countWhere('mall_mirror_products', 'mirror_status', 'published'), 'label' => 'published'],
            ['name' => 'Services', 'url' => '/mall/services', 'count' => self::countWhere('mall_mirror_services', 'mirror_status', 'published'), 'label' => 'published'],
            ['name' => 'Courses', 'url' => '/mall/courses', 'count' => self::countWhere('mall_mirror_courses', 'mirror_status', 'published'), 'label' => 'published'],
            ['name' => 'Talent', 'url' => '/mall/talent', 'count' => self::countWhere('mall_mirror_talent', 'mirror_status', 'published'), 'label' => 'published'],
            ['name' => 'Travel', 'url' => '/mall/travel', 'count' => self::countWhere('travel_tourism_mall_listings', 'listing_status', 'published'), 'label' => 'published'],
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
            ['title' => 'أصحاب الأعمال', 'tag' => 'Business', 'value' => 'موقع + محتوى + متجر + CRM + فواتير + ظهور داخل المول.'],
            ['title' => 'المؤسسات', 'tag' => 'Enterprise', 'value' => 'حوكمة + صلاحيات + تكاملات + BI + GRC + Mobile/Desktop.'],
            ['title' => 'المطورون', 'tag' => 'Developers', 'value' => 'ثيمات وبلجنز وConnectors يمكن نشرها وبيعها في Marketplace.'],
            ['title' => 'المسوقون والشركاء', 'tag' => 'Partners', 'value' => 'إحالات، واجهات شركاء، حملات، Work Network، Academy، ونمو متكرر.'],
        ];
    }

    /**
     * @return list<array<string, string>>
     */
    private static function onboarding(): array
    {
        return [
            ['n' => '01', 'title' => 'تحديد نوع المستخدم', 'text' => 'صاحب عمل، مؤسسة، مطور، مسوق، وكالة، أو شريك. كل مسار يفتح أدوات مختلفة.'],
            ['n' => '02', 'title' => 'بناء مساحة العمل', 'text' => 'Organization، شركة، تطبيق/موقع، اللغة، العملة، الدومين، والفريق.'],
            ['n' => '03', 'title' => 'اختيار الثيم والإضافات', 'text' => 'Theme Catalog، Recipes، Plugin Bundles، وفحص Entitlements قبل المدفوع.'],
            ['n' => '04', 'title' => 'تشغيل العمل والنمو', 'text' => 'محتوى، منتجات، CRM، عمليات، تقارير، تكاملات، ثم Marketplace وPartner Network.'],
        ];
    }

    /**
     * @return list<array<string, string>>
     */
    private static function developerFlow(): array
    {
        return [
            ['title' => 'Manifest', 'text' => 'تعريف الهوية، الإصدارات، الصلاحيات، التوافق، الترخيص، والاعتمادات.'],
            ['title' => 'Review', 'text' => 'اختبارات تلقائية ومراجعة يدوية قبل النشر أو البيع.'],
            ['title' => 'Signing', 'text' => 'إصدارات موثقة وقابلة للتتبع قبل التثبيت الآمن.'],
            ['title' => 'Revenue', 'text' => 'بيع أصول رقمية أو خدمات تطوير داخل اقتصاد المنصة.'],
        ];
    }

    /**
     * @return list<array<string, string>>
     */
    private static function adminRoutes(): array
    {
        return [
            ['name' => 'Filament Admin', 'url' => '/admin', 'desc' => 'لوحة الإدارة الداخلية'],
            ['name' => 'Task Tracker', 'url' => '#task-tracker', 'desc' => 'حالة V1-V14 وFreemium وExtensions'],
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
            ['name' => 'Backend Completion', 'path' => 'docs/V2_BACKEND_COMPLETION_REPORT.md'],
            ['name' => 'Freemium Notes', 'path' => 'docs/FREEMIUM_IMPLEMENTATION_NOTES.md'],
            ['name' => 'UI Roadmap', 'path' => 'docs/UI_PAGE_INVENTORY_AND_ROADMAP.md'],
            ['name' => 'Extension Governance', 'path' => 'docs/kabeeri/extensions/README.md'],
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
}
