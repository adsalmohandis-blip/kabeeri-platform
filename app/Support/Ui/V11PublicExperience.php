<?php

namespace App\Support\Ui;

use App\Models\Lead;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class V11PublicExperience
{
    /**
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        $config = config('kabeeri_public', []);

        if (app()->getLocale() === 'ar') {
            $config = array_replace_recursive($config, self::arabicCopy());
        }

        return [
            'config' => $config,
            'pages' => $config['pages'] ?? [],
            'story_layers' => $config['story_layers'] ?? [],
            'audiences' => $config['audiences'] ?? [],
            'journeys' => $config['journeys'] ?? [],
            'onboarding_steps' => $config['onboarding_steps'] ?? [],
            'wizard' => $config['wizard'] ?? [],
            'pricing_layers' => $config['pricing_layers'] ?? [],
            'plans' => $config['plans'] ?? [],
            'templates' => $config['templates'] ?? [],
            'faq' => $config['faq'] ?? [],
            'next_runtime' => $config['next_runtime'] ?? [],
            'release' => self::releaseReadiness(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function arabicCopy(): array
    {
        return [
            'name' => 'واجهة كبيري العامة ومسارات الجمهور والبداية',
            'pages' => [
                'landing' => ['label' => 'الواجهة العامة', 'intent' => 'شرح كبيري تدريجيًا بدون إرباك الزائر.'],
                'audiences' => ['label' => 'اختيار المسار', 'intent' => 'مساعدة الزائر على اختيار المسار المناسب.'],
                'business' => ['label' => 'مسار صاحب العمل', 'intent' => 'موقع، تجارة، إدارة عملاء، عمليات، ونمو عبر الظهور العام.'],
                'enterprise' => ['label' => 'مسار المؤسسات', 'intent' => 'حوكمة، أمان، تكاملات، ذكاء أعمال، وإدارة مخاطر.'],
                'developers' => ['label' => 'مسار المطورين والمبدعين', 'intent' => 'ثيمات، إضافات، حزم، مراجعة، توقيع، وأرباح.'],
                'partners' => ['label' => 'مسار المسوقين والشركاء', 'intent' => 'إحالات، حملات، تسليم عملاء، وعوائد شراكة.'],
                'wordpress' => ['label' => 'بديل ووردبريس', 'intent' => 'تقديم كبيري كمسار أبسط للموقع والمحتوى والتجارة.'],
                'service_business' => ['label' => 'حالة استخدام لشركات الخدمات', 'intent' => 'عرض الحجز، الخدمات، إدارة العملاء، عروض الأسعار، الفواتير، والظهور العام.'],
                'templates' => ['label' => 'حالات الاستخدام والقوالب', 'intent' => 'قوالب قطاعات بدون إرباك الزائر.'],
                'onboarding' => ['label' => 'نظرة على مسار البداية', 'intent' => 'شرح البداية من الجمهور إلى مساحة العمل ثم أول إطلاق.'],
                'workspace_setup' => ['label' => 'معالج إعداد مساحة العمل', 'intent' => 'معاينة بنية الإعداد والإظهار التدريجي للحقول.'],
                'pricing' => ['label' => 'الأسعار والخطط', 'intent' => 'شرح الاشتراكات، الموديولات، التحقق، الظهور العام، رصيد الذكاء الاصطناعي، وتقاسم الأرباح.'],
                'trust' => ['label' => 'الأسئلة والثقة', 'intent' => 'الإجابة عن المخاطر، الملكية، الخصوصية، المتجر، الظهور العام، والهجرة.'],
                'contact' => ['label' => 'طلب ديمو', 'intent' => 'التقاط الطلبات المؤهلة داخل إدارة العملاء.'],
            ],
            'story_layers' => [
                ['title' => 'موقع أوضح من فوضى ووردبريس', 'text' => 'ابدأ بتطبيق كبيري: صفحات، محتوى، تحسين ظهور، وسائط، نماذج، وقوائم بدون ازدحام إضافات.'],
                ['title' => 'تجارة بدون ثقل الأنظمة المتفرقة', 'text' => 'أضف المنتجات والخدمات والطلبات والكوبونات وطرق الدفع وعروض الأسعار عندما يصبح نشاطك جاهزًا.'],
                ['title' => 'هوية شركة موثوقة عبر رابط', 'text' => 'اربط ملف الشركة، التحقق، جهات الاتصال، وإشارات الثقة العامة.'],
                ['title' => 'تشغيل عندما يكبر الفريق', 'text' => 'انتقل تدريجيًا إلى إدارة العملاء، طلبات الخدمة، عروض الأسعار، الفواتير، المخزون، المشتريات، سير العمل، التقارير، والموافقات.'],
                ['title' => 'ظهور عام عبر مول كبيري', 'text' => 'انشر الشركات والمنتجات والخدمات والدورات والمواهب والسفر بعد المراجعة والموافقة.'],
                ['title' => 'اقتصاد المنصة', 'text' => 'المطورون والوكالات والمبدعون والمسوقون والشركاء يستطيعون البناء والبيع والإحالة والدعم داخل المنظومة.'],
            ],
            'audiences' => [
                'business' => [
                    'label' => 'صاحب عمل',
                    'headline' => 'أطلق حضورًا جادًا لنشاطك ثم توسع إلى التشغيل.',
                    'pain' => 'تحتاج موقعًا ومحتوى وعملاء محتملين وتجارة وفواتير وظهورًا بدون إدارة إضافات منفصلة.',
                    'outcome' => 'ابدأ بتطبيق كبيري، أضف التجارة وإدارة العملاء، ثم انتقل إلى نظام الشركة والظهور العام.',
                ],
                'enterprise' => [
                    'label' => 'مؤسسة',
                    'headline' => 'حكم الفرق والبيانات والتكاملات والمخاطر من منصة واحدة.',
                    'pain' => 'تحتاج صلاحيات وسجلات تدقيق وتحكمًا في التكاملات وجاهزية للذكاء التشغيلي والحوكمة.',
                    'outcome' => 'استخدم لوحة الإدارة، السياسات، سجلات النشاط، بوابات الإصدار، الواجهات البرمجية، الجوال، سطح المكتب، والحوكمة.',
                ],
                'developers' => [
                    'label' => 'مطور أو مبدع',
                    'headline' => 'ابن ثيمات وإضافات وموصلات وحزم أعمال لاقتصاد كبيري.',
                    'pain' => 'تحتاج مسار متجر واضحًا بملفات تعريف وصلاحيات وتوافق وتوقيع ودعم وأرباح.',
                    'outcome' => 'أنشئ الحزم، اجتز المراجعة، انشر داخل المتجر، وبع أو ادعم العملاء.',
                ],
                'partners' => [
                    'label' => 'مسوق أو شريك',
                    'headline' => 'حوّل الحملات والإحالات وشركاء التنفيذ إلى نمو قابل للقياس.',
                    'pain' => 'تحتاج تسليم عملاء محتملين ومواد حملات ووضوح إحالات وواجهات شركاء وقواعد عوائد.',
                    'outcome' => 'استخدم مسارات الشركاء، تتبع الإحالات، تسليم إدارة العملاء، الأكاديمية وشبكة العمل، ثم لوحات العوائد.',
                ],
                'mall' => [
                    'label' => 'زائر المول',
                    'headline' => 'اكتشف شركات ومنتجات وخدمات ودورات ومواهب وسفر موثوق.',
                    'pain' => 'تحتاج اكتشافًا عامًا بثقة ومراجعة وتحقق وأنواع قوائم واضحة.',
                    'outcome' => 'تصفح مول كبيري وتواصل مع القوائم المنشورة أو الموثقة.',
                ],
            ],
            'journeys' => [
                'business' => ['اختر مسار نشاطك', 'أنشئ تطبيق كبيري', 'أضف المحتوى والخدمات', 'التقط العملاء المحتملين', 'أضف التجارة والفواتير', 'انشر في المول عندما تصبح موثوقًا'],
                'enterprise' => ['حدد نطاق الحوكمة', 'عرّف المؤسسات والشركات', 'راجع الصلاحيات والتدقيق', 'اربط الواجهات والتكاملات', 'خطط للذكاء التشغيلي والحوكمة', 'شغّل بوابات الإصدار'],
                'developers' => ['أنشئ ملف ناشر', 'ابن ملف التعريف', 'شغّل الاختبارات محليًا', 'أرسل للمراجعة', 'اجتز فحص الصلاحيات والأمان', 'وقّع وانشر'],
                'partners' => ['اختر مسار الشراكة', 'أنشئ حملة أو واجهة شريك', 'التقط إحالة أو عميلًا محتملًا', 'سلّم إلى إدارة العملاء', 'قس التقدم', 'جهز تقاسم العوائد'],
            ],
            'onboarding_steps' => [
                ['title' => 'الجمهور والنية', 'text' => 'اختر صاحب عمل، مؤسسة، مطور أو مبدع، مسوق أو شريك، وكالة، أو زائر مول.'],
                ['title' => 'مساحة العمل', 'text' => 'أنشئ المؤسسة، والشركة عند الحاجة، وأول تطبيق كبيري.'],
                ['title' => 'وصفة التطبيق', 'text' => 'اختر موقعًا، نشاط خدمات، متجرًا، صفحة هبوط، بوابة، أو مركز محتوى.'],
                ['title' => 'الثيم والموديولات', 'text' => 'اختر الثيم الحالي، ثم لاحقًا ثيم نكست، وفعل فقط الموديولات المطلوبة.'],
                ['title' => 'الفريق والصلاحيات', 'text' => 'ادع المستخدمين، حدد الأدوار، واشرح الإجراءات الممنوعة بوضوح.'],
                ['title' => 'الإطلاق والنمو', 'text' => 'انشر المحتوى، التقط العملاء، أضف التجارة والتشغيل، ثم انتقل لمسارات المول والمتجر.'],
            ],
            'wizard' => [
                'fields' => ['الجمهور', 'نوع النشاط', 'اسم المؤسسة', 'هل توجد شركة؟', 'نوع أول تطبيق', 'اللغة', 'العملة', 'الموديولات', 'حجم الفريق', 'الخطة المتوقعة'],
                'progressive_rules' => ['اسأل فقط عما يحتاجه الجمهور المختار الآن.', 'أخف نظام الموارد حتى توجد شركة واضحة.', 'أخف تثبيت المتجر حتى يوجد مالك أو مطور مناسب.', 'أخف نشر المول حتى يتم شرح الثقة والمراجعة.'],
            ],
            'pricing_layers' => [
                ['label' => 'الاشتراك', 'text' => 'خطط مجانية ومبدئية وتجارية ووكالات ومؤسسات مع الاستحقاقات.'],
                ['label' => 'الموديولات', 'text' => 'التجارة، إدارة العملاء، الموارد، المتجر، الذكاء التشغيلي والحوكمة، الجوال وسطح المكتب.'],
                ['label' => 'التحقق', 'text' => 'تحقق رابط والشركة وترقيات الثقة العامة.'],
                ['label' => 'الظهور في المول', 'text' => 'قوائم مميزة، ظهور ممول، توليد عملاء، وترقيات اكتشاف.'],
                ['label' => 'الذكاء والأتمتة', 'text' => 'رصيد ذكاء اصطناعي، موافقة على التغييرات الحساسة، وحدود الأتمتة.'],
                ['label' => 'عوائد المتجر', 'text' => 'قواعد تقاسم عوائد المطورين والمبدعين والوكالات والشركاء.'],
            ],
            'plans' => [
                ['name' => 'مجاني / مجتمع', 'price' => 'مجاني', 'best_for' => 'استكشاف مبكر وتطبيق بسيط واحد.', 'highlight' => 'ابدأ بدون مخاطرة.'],
                ['name' => 'مبدئي / احترافي', 'price' => '١٩ دولار شهريًا', 'best_for' => 'نشاط فردي بموقع عام والتقاط عملاء.', 'highlight' => 'مسار إطلاق مركز.'],
                ['name' => 'أعمال', 'price' => '٤٩ دولار شهريًا', 'best_for' => 'شركة نامية تحتاج تجارة وإدارة عملاء وتشغيل.', 'highlight' => 'أفضل أساس للنمو.'],
                ['name' => 'وكالة', 'price' => '٩٩ دولار شهريًا', 'best_for' => 'وكالات تدير عدة عملاء وحزم قابلة لإعادة الاستخدام.', 'highlight' => 'توسيع تقديم الخدمات.'],
                ['name' => 'مؤسسات', 'price' => 'حسب الاتفاق', 'best_for' => 'حوكمة وأمان وتكاملات وذكاء تشغيلي وعقود.', 'highlight' => 'اعتماد مضبوط للمنصة.'],
            ],
            'templates' => [
                ['title' => 'شركة خدمات', 'includes' => ['خدمات', 'طلب حجز', 'إدارة العملاء', 'عروض أسعار', 'فواتير', 'قائمة في المول']],
                ['title' => 'شركة احترافية', 'includes' => ['موقع', 'ملف رابط', 'نماذج', 'فريق', 'تقارير']],
                ['title' => 'بداية التجارة', 'includes' => ['منتجات', 'طلبات', 'كوبونات', 'مدفوعات', 'منتجات في المول']],
                ['title' => 'حزمة عميل للوكالات', 'includes' => ['ثيمات', 'قوالب', 'مساحات عمل للعملاء', 'مسار شراكة']],
                ['title' => 'حوكمة المؤسسات', 'includes' => ['صلاحيات', 'تدقيق', 'تكاملات', 'ذكاء تشغيلي', 'حوكمة']],
            ],
            'faq' => [
                ['q' => 'هل كبيري مجرد منشئ مواقع؟', 'a' => 'لا. يبدأ كمسار أوضح للموقع والمحتوى، ثم يتوسع إلى التجارة وإدارة العملاء والتشغيل والمول والمتجر والجوال وسطح المكتب وحوكمة المؤسسات.'],
                ['q' => 'لماذا لا أستخدم ووردبريس فقط؟', 'a' => 'ووردبريس قوي، لكن كثيرًا من الأنشطة تنتهي بفوضى إضافات. كبيري يستهدف موديولات مضبوطة، حوكمة إدارية، وتشغيل أعمال في مسار واحد.'],
                ['q' => 'هل الثيمات الحالية أم ثيمات نكست؟', 'a' => 'صفحات لارافيل الحالية جسر مؤقت. الثيمات التجارية النهائية تستهدف نكست وريأكت وتايب سكريبت وتيلويند عبر عقود واجهات برمجية.'],
                ['q' => 'ما الفرق بين المتجر والمول؟', 'a' => 'المتجر للإضافات الداخلية مثل الثيمات والإضافات والموصلات ومهارات الذكاء. المول للاكتشاف العام مثل الشركات والمنتجات والخدمات والمواهب والدورات والسفر.'],
                ['q' => 'هل يستطيع المطورون البيع على المنصة؟', 'a' => 'نعم، اقتصاد المطورين المخطط يشمل ملفات تعريف الحزم، المراجعة، الصلاحيات، التوقيع، النشر، التزامات الدعم، وتقاسم العوائد.'],
                ['q' => 'كيف تعمل الثقة؟', 'a' => 'الثقة تبنى عبر رابط وملف الشركة والمراجعة وموافقة النشر وقابلية التدقيق ومسارات مراجعة واضحة.'],
            ],
            'next_runtime' => [
                'decision' => 'لا ننشئ واجهة نكست قبل توثيق العقود ومسار الهجرة. هذه النسخة توثق خطة الإعداد وتبقي قوالب لارافيل كجسر مؤقت.',
                'contract_needs' => ['الخطط والأسعار', 'محتوى التطبيق العام', 'رحلات الجمهور', 'التقاط العملاء', 'قوائم المول', 'ملف الثيم', 'التنقل والقوائم'],
                'migration_steps' => ['الإبقاء على صفحات الجسر الحالية', 'تأكيد عقود الواجهات البرمجية', 'إنشاء تطبيق الواجهة العامة', 'نقل التسويق والبداية أولًا', 'نقل المتجر والمول لاحقًا', 'تشغيل مراجعة التكافؤ وإمكانية الوصول'],
            ],
            'release_gates' => [
                ['label' => 'أساس الواجهة جاهز'],
                ['label' => 'لوحة قيادة الأدمن جاهزة'],
                ['label' => 'مسارات الواجهة العامة مسجلة'],
                ['label' => 'طلب الديمو ينشئ عميلًا محتملًا'],
                ['label' => 'توثيق النسخة جاهز'],
                ['label' => 'اختبارات النسخة جاهزة'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function pageData(string $page): array
    {
        $all = self::all();

        return [
            ...$all,
            'page' => $page,
            'page_config' => $all['pages'][$page] ?? $all['pages']['landing'],
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function captureInquiry(array $payload): Lead
    {
        $organization = self::publicInquiryOrganization();
        $companyName = $payload['company_name'] ?? null;

        return Lead::query()->create([
            'organization_id' => $organization->id,
            'title' => 'Public inquiry: '.($companyName ?: $payload['name']),
            'company_name' => $companyName ?: null,
            'name' => $payload['name'],
            'email' => $payload['email'],
            'phone' => $payload['phone'] ?? null,
            'source' => 'public_v11_inquiry',
            'status' => 'new',
            'priority' => match ($payload['audience'] ?? null) {
                'enterprise' => 'high',
                'agency', 'developers', 'developer_creator' => 'normal',
                default => 'normal',
            },
            'message' => $payload['message'],
            'metadata' => [
                'audience' => $payload['audience'] ?? 'unknown',
                'plan_interest' => $payload['plan_interest'] ?? null,
                'source_page' => 'V11 Contact Sales',
                'next_step' => 'Review inquiry in CRM leads.',
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function validationReport(): array
    {
        $config = config('kabeeri_public', []);
        $routes = array_column($config['pages'] ?? [], 'route');
        $missingRoutes = array_values(array_filter(
            $routes,
            fn (string $route): bool => ! Route::has($route),
        ));

        return [
            'version' => $config['version'] ?? null,
            'v9_ready' => V9UiFoundation::isReleaseCandidateReady(),
            'v10_ready' => V10AdminExperience::isReleaseCandidateReady(),
            'page_count' => count($config['pages'] ?? []),
            'audience_count' => count($config['audiences'] ?? []),
            'story_layer_count' => count($config['story_layers'] ?? []),
            'pricing_layer_count' => count($config['pricing_layers'] ?? []),
            'missing_routes' => $missingRoutes,
            'task_tracker_synced' => self::v11TaskTrackerSynced(),
            'docs' => [
                'public_ux' => file_exists(base_path('docs/kabeeri/ui/V11_PUBLIC_UX.md')),
                'release_report' => file_exists(base_path('docs/kabeeri/ui/V11_RELEASE_CANDIDATE.md')),
            ],
        ];
    }

    public static function isReleaseCandidateReady(): bool
    {
        $report = self::validationReport();

        return $report['version'] === 'V11'
            && $report['v9_ready']
            && $report['v10_ready']
            && $report['page_count'] >= 14
            && $report['audience_count'] >= 5
            && $report['story_layer_count'] >= 6
            && $report['pricing_layer_count'] >= 6
            && $report['missing_routes'] === []
            && $report['task_tracker_synced']
            && ! in_array(false, $report['docs'], true);
    }

    /**
     * @return array<string, mixed>
     */
    private static function releaseReadiness(): array
    {
        $releaseGates = config('kabeeri_public.release_gates', []);

        if (app()->getLocale() === 'ar') {
            $releaseGates = array_replace_recursive($releaseGates, self::arabicCopy()['release_gates'] ?? []);
        }

        $gates = array_map(function (array $gate): array {
            $ready = match ($gate['key']) {
                'v9_ready' => V9UiFoundation::isReleaseCandidateReady(),
                'v10_ready' => V10AdminExperience::isReleaseCandidateReady(),
                'routes_ready' => self::routesReady(),
                'inquiry_flow_ready' => class_exists(Lead::class) && class_exists(Organization::class),
                'docs_ready' => file_exists(base_path('docs/kabeeri/ui/V11_PUBLIC_UX.md')),
                'smoke_tests_ready' => file_exists(base_path('tests/Feature/V11PublicExperienceTest.php')),
                default => false,
            };

            return $gate + ['ready' => $ready];
        }, $releaseGates);

        return [
            'ready' => collect($gates)->every(fn (array $gate): bool => $gate['ready']),
            'gates' => $gates,
            'commands' => [
                'php artisan test --filter=V11PublicExperienceTest',
                'php artisan test --filter=RootDashboardPageTest',
                'vendor/bin/pint --test',
                'npm run build',
            ],
        ];
    }

    private static function routesReady(): bool
    {
        return collect(config('kabeeri_public.pages', []))
            ->every(fn (array $page): bool => Route::has($page['route']));
    }

    private static function v11TaskTrackerSynced(): bool
    {
        $path = base_path('24_kabeeri_task_tracking/tasks/v11.tasks.json');
        $payload = json_decode((string) file_get_contents($path), true);
        $tasks = is_array($payload['tasks'] ?? null) ? $payload['tasks'] : [];

        return count($tasks) >= 27 && collect($tasks)->every(
            fn (array $task): bool => in_array(($task['status'] ?? null), ['codex_done', 'verified'], true),
        );
    }

    private static function publicInquiryOrganization(): Organization
    {
        $contact = config('kabeeri_public.contact');
        $owner = User::query()->firstOrCreate(
            ['email' => $contact['owner_email']],
            [
                'name' => 'kabeeri Public Inquiry Owner',
                'password' => Str::password(40),
            ],
        );

        return Organization::query()->firstOrCreate(
            ['slug' => $contact['organization_slug']],
            [
                'name' => $contact['organization_name'],
                'owner_user_id' => $owner->id,
                'account_type' => 'platform_internal',
                'status' => 'active',
                'plan_code' => 'enterprise',
                'locale' => 'ar',
                'timezone' => 'Africa/Cairo',
                'metadata' => ['source' => 'v11_public_inquiries'],
            ],
        );
    }
}
