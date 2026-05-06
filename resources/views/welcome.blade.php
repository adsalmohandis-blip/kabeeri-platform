@php
    $versions = [];
    $totalTasks = 0;
    $totalDone = 0;
    $totalPending = 0;

    foreach (range(1, 8) as $versionNumber) {
        $path = base_path("24_kabeeri_task_tracking/tasks/v{$versionNumber}.tasks.json");
        if (! file_exists($path)) {
            continue;
        }

        $payload = json_decode(file_get_contents($path), true);
        $tasks = $payload['tasks'] ?? [];
        $counts = array_count_values(array_map(fn ($task) => $task['status'] ?? 'unknown', $tasks));
        $done = ($counts['codex_done'] ?? 0) + ($counts['verified'] ?? 0);
        $pending = $counts['pending'] ?? 0;
        $total = count($tasks);

        $totalTasks += $total;
        $totalDone += $done;
        $totalPending += $pending;

        $versions[] = [
            'name' => 'V'.$versionNumber,
            'pending' => $pending,
            'percent' => $total > 0 ? round(($done / $total) * 100) : 0,
        ];
    }

    $overallPercent = $totalTasks > 0 ? round(($totalDone / $totalTasks) * 100) : 0;

    $audiences = [
        ['title' => 'أصحاب الأعمال', 'tag' => 'Business', 'desc' => 'موقع، محتوى، متجر، CRM، فواتير، منتجات وخدمات، وظهور داخل المول.', 'path' => 'اختيار النشاط، ضبط الهوية، اختيار الثيم، إضافة العروض، دعوة الفريق، ثم إطلاق التشغيل.'],
        ['title' => 'المؤسسات', 'tag' => 'Enterprise', 'desc' => 'صلاحيات، حوكمة، تكاملات، بيانات، تقارير، GRC، وتجارب Mobile/Desktop.', 'path' => 'تهيئة المؤسسة، توزيع الصلاحيات، ربط الأنظمة، تشغيل الوحدات، ثم متابعة الأداء.'],
        ['title' => 'المطورون', 'tag' => 'Developers', 'desc' => 'بناء ثيمات وبلجنز وConnectors ونشرها وبيعها داخل متجر المنصة.', 'path' => 'بناء الحزمة، اختبار manifest، تقديمها للمراجعة، نشرها، ثم تحقيق عائد من البيع أو الخدمات.'],
        ['title' => 'المسوقون والشركاء', 'tag' => 'Partners', 'desc' => 'واجهات خدمات، إحالات، حملات، Work Network، Academy، وفرص شراكة متكررة.', 'path' => 'إنشاء واجهة شريك، عرض الخدمات، جذب العملاء، متابعة النتائج، وتنمية السمعة.'],
    ];

    $onboarding = [
        ['n' => '01', 'title' => 'تحديد نوع المستخدم', 'text' => 'صاحب عمل، مؤسسة، مطور، مسوق، وكالة، أو شريك. كل مسار يفتح أدوات مختلفة.'],
        ['n' => '02', 'title' => 'إعداد الهوية', 'text' => 'الشعار، المجال، الدولة، العملة، النطاق، الفريق، والصلاحيات الأساسية.'],
        ['n' => '03', 'title' => 'اختيار الثيم والإضافات', 'text' => 'ثيم مناسب للمجال، demo pages، recipe، وحزمة plugins حسب الاحتياج.'],
        ['n' => '04', 'title' => 'إطلاق التشغيل', 'text' => 'إضافة المنتجات والخدمات، تشغيل CRM والفواتير، وربط التقارير ومسارات النمو.'],
    ];

    $capabilities = [
        ['title' => 'CMS وSEO', 'text' => 'صفحات، محتوى، قوائم، نماذج، redirects، sitemap، وبيانات بحث.'],
        ['title' => 'Commerce وMall', 'text' => 'منتجات، خدمات، كورسات، مواهب، سياحة، وواجهات عرض عامة.'],
        ['title' => 'ERP وOperations', 'text' => 'CRM، فرص، عروض، فواتير، مخزون، مشتريات، محاسبة، مشاريع.'],
        ['title' => 'Integrations', 'text' => 'Connectors، credentials references، sync preview، conflicts، وAPI gateway.'],
        ['title' => 'Mobile وDesktop', 'text' => 'Mobile APIs، devices، push tokens hashed، وdesktop sync dry-run.'],
        ['title' => 'Network وPartners', 'text' => 'شركاء، وكالات، إحالات، Academy، Work Network، وواجهات شركاء.'],
    ];

    $themeFlow = [
        ['title' => 'Theme Catalog', 'text' => 'قوالب رسمية حسب المجال، اللغة، ونوع التجربة المطلوبة.'],
        ['title' => 'Theme Recipes', 'text' => 'وصفات جاهزة تضبط الصفحات والمحتوى والهيكل الأولي.'],
        ['title' => 'Plugin Bundles', 'text' => 'حزم إضافات تضيف وظائف مثل متجر، نماذج، CRM، أو تكاملات.'],
        ['title' => 'Marketplace Review', 'text' => 'مراجعة manifest والحزمة قبل النشر والبيع داخل المتجر.'],
    ];

    $plans = [
        ['name' => 'Start', 'for' => 'مشروع صغير', 'items' => ['موقع وCMS', 'ثيم جاهز', 'SEO ونماذج', 'ظهور مبدئي']],
        ['name' => 'Business', 'for' => 'شركة نامية', 'items' => ['CRM وفواتير', 'منتجات وخدمات', 'فريق وصلاحيات', 'تقارير']],
        ['name' => 'Enterprise', 'for' => 'مؤسسة', 'items' => ['حوكمة', 'تكاملات', 'BI وGRC', 'Mobile/Desktop']],
        ['name' => 'Partner', 'for' => 'مطور أو مسوق', 'items' => ['Marketplace', 'ثيمات وبلجنز', 'إحالات', 'واجهات شركاء']],
    ];
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KABEERI | منصة تشغيل ونمو للأعمال</title>
    <meta name="description" content="KABEERI منصة موحدة لبناء المواقع، إدارة الأعمال، التجارة، التكاملات، الثيمات، البلجنز، وشبكة الشركاء والمطورين.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=almarai:400,700,800|ibm-plex-sans-arabic:400,500,600,700" rel="stylesheet" />
    <style>
        :root{--ink:#15110c;--ink2:#34291f;--muted:#746654;--paper:#fff9ed;--cream:rgba(255,249,237,.78);--gold:#c98a2e;--gold2:#f5c96b;--olive:#41533c;--clay:#985536;--night:#1f1811;--line:rgba(21,17,12,.13);--shadow:0 28px 90px rgba(62,39,13,.17);--soft:0 16px 46px rgba(62,39,13,.09)}
        *{box-sizing:border-box}html{scroll-behavior:smooth}body{margin:0;min-height:100vh;color:var(--ink);font-family:"IBM Plex Sans Arabic","Almarai",sans-serif;background:radial-gradient(circle at 8% 6%,rgba(223,234,194,.9),transparent 26rem),radial-gradient(circle at 88% 10%,rgba(245,201,107,.42),transparent 25rem),linear-gradient(135deg,#fff9ed 0%,#f1d8a7 54%,#dfb572 100%);background-attachment:fixed}
        body:before{content:"";position:fixed;inset:0;z-index:-1;opacity:.22;background-image:linear-gradient(rgba(21,17,12,.06) 1px,transparent 1px),linear-gradient(90deg,rgba(21,17,12,.05) 1px,transparent 1px);background-size:44px 44px;mask-image:linear-gradient(to bottom,#000,transparent 82%)}a{color:inherit;text-decoration:none}.shell{width:min(1280px,calc(100% - 34px));margin:0 auto;padding:22px 0 56px}
        .topbar{position:sticky;top:12px;z-index:20;display:flex;align-items:center;justify-content:space-between;gap:18px;padding:12px;border:1px solid rgba(21,17,12,.1);border-radius:28px;background:rgba(255,249,237,.78);backdrop-filter:blur(18px);box-shadow:0 12px 34px rgba(62,39,13,.08)}.brand{display:flex;align-items:center;gap:12px}.mark{width:54px;height:54px;display:grid;place-items:center;border-radius:20px;color:var(--paper);background:conic-gradient(from 210deg,#1f1811,#89532d,#dba24b,#41533c,#1f1811);font-family:Almarai,sans-serif;font-weight:800}.brand strong{display:block;font-family:Almarai,sans-serif;font-size:18px}.brand span{color:var(--muted);font-size:13px}.topnav{display:flex;flex-wrap:wrap;justify-content:flex-end;gap:8px}.nav-link{display:inline-flex;align-items:center;justify-content:center;min-height:40px;padding:0 13px;border:1px solid rgba(21,17,12,.11);border-radius:999px;background:rgba(255,255,255,.44);font-size:13px;font-weight:800}.nav-link:hover{background:var(--paper);transform:translateY(-1px)}
        .hero{position:relative;overflow:hidden;margin-top:20px;border:1px solid rgba(21,17,12,.12);border-radius:44px;background:linear-gradient(145deg,rgba(255,249,237,.9),rgba(255,239,203,.74));box-shadow:var(--shadow)}.hero:before{content:"";position:absolute;width:380px;height:380px;inset-inline-start:-130px;top:-160px;border-radius:999px;background:repeating-linear-gradient(45deg,rgba(65,83,60,.15) 0 10px,transparent 10px 24px);animation:floatA 20s ease-in-out infinite alternate}.hero-grid{position:relative;display:grid;grid-template-columns:minmax(0,1.16fr) minmax(360px,.84fr);gap:26px;padding:clamp(28px,5vw,64px)}.kicker{display:inline-flex;align-items:center;gap:10px;width:fit-content;padding:10px 14px;border:1px solid rgba(21,17,12,.12);border-radius:999px;background:rgba(255,255,255,.48);color:#6f3524;font-weight:800;font-size:13px}.dot{width:9px;height:9px;border-radius:999px;background:#4b9149;box-shadow:0 0 0 7px rgba(75,145,73,.13)}
        h1{max-width:880px;margin:22px 0 0;font-family:Almarai,sans-serif;font-size:clamp(42px,7vw,92px);line-height:.99;letter-spacing:-2.3px}.lead{max-width:800px;margin:24px 0 0;color:var(--muted);font-size:clamp(17px,1.8vw,22px);line-height:1.95}.actions{display:flex;flex-wrap:wrap;gap:12px;margin-top:32px}.button{display:inline-flex;align-items:center;justify-content:center;min-height:52px;padding:0 22px;border:1px solid rgba(21,17,12,.15);border-radius:17px;font-weight:800;transition:transform .18s ease,box-shadow .18s ease}.button:hover{transform:translateY(-2px);box-shadow:0 16px 36px rgba(62,39,13,.14)}.primary{color:var(--paper);background:var(--night)}.secondary{background:rgba(255,255,255,.58)}.ghost{background:rgba(65,83,60,.11);color:var(--olive)}
        .value-card{padding:24px;border-radius:30px;color:var(--paper);background:linear-gradient(160deg,rgba(31,24,17,.96),rgba(105,63,35,.96));box-shadow:0 22px 56px rgba(31,24,17,.22)}.value-card h2{margin:0;font-family:Almarai,sans-serif;font-size:28px}.value-card p{margin:10px 0 18px;color:rgba(255,249,237,.75);line-height:1.8}.value-list{display:grid;gap:9px}.value-list div{display:flex;justify-content:space-between;gap:12px;padding:11px 12px;border-radius:15px;background:rgba(255,249,237,.08);color:rgba(255,249,237,.88)}.metrics{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:14px}.metric{padding:17px;border:1px solid var(--line);border-radius:22px;background:rgba(255,249,237,.72);box-shadow:var(--soft)}.metric span{color:var(--muted);font-size:12px;font-weight:800;text-transform:uppercase}.metric strong{display:block;margin-top:8px;font-family:Almarai,sans-serif;font-size:29px}
        .section{margin-top:38px}.section-title{display:flex;justify-content:space-between;align-items:end;gap:18px;margin-bottom:16px}.section-title h2{margin:0;font-family:Almarai,sans-serif;font-size:clamp(29px,3.2vw,46px);letter-spacing:-.8px}.section-title p{max-width:650px;margin:0;color:var(--muted);line-height:1.85}.grid-2{display:grid;grid-template-columns:repeat(2,1fr);gap:16px}.grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}.grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}.card{border:1px solid var(--line);border-radius:30px;background:var(--cream);box-shadow:var(--soft);padding:24px}.card h3{margin:0 0 10px;font-family:Almarai,sans-serif;font-size:26px}.card p{margin:0;color:var(--muted);line-height:1.8}.tag{display:inline-flex;margin-bottom:14px;padding:8px 11px;border-radius:999px;background:rgba(65,83,60,.12);color:var(--olive);font-size:12px;font-weight:800}.chips{display:flex;flex-wrap:wrap;gap:8px;margin:16px 0}.chip{padding:8px 10px;border-radius:999px;background:rgba(255,255,255,.56);font-size:12px;font-weight:800}.path{padding:15px;border-radius:18px;background:rgba(255,255,255,.44)}
        .step{position:relative;overflow:hidden;min-height:245px}.step:after{content:attr(data-step);position:absolute;inset-inline-end:18px;bottom:8px;color:rgba(21,17,12,.06);font-family:Almarai,sans-serif;font-weight:800;font-size:92px}.step small{color:var(--clay);font-weight:800}.feature{color:var(--paper);background:linear-gradient(145deg,#1f1811,#74442a 64%,#9b6432)}.feature p{color:rgba(255,249,237,.76)}
        .plan.featured{background:linear-gradient(160deg,rgba(31,24,17,.95),rgba(65,83,60,.95));color:var(--paper)}.plan ul{margin:16px 0 0;padding:0;list-style:none;display:grid;gap:10px}.plan li{color:var(--muted)}.plan.featured li,.plan.featured p{color:rgba(255,249,237,.76)}.version-list{display:grid;gap:9px;margin-top:16px}.version-row{display:grid;grid-template-columns:48px 1fr 80px;gap:10px;align-items:center;padding:11px 12px;border-radius:16px;background:rgba(255,255,255,.46)}.bar{height:8px;border-radius:999px;background:rgba(21,17,12,.09);overflow:hidden}.bar span{display:block;height:100%;border-radius:inherit;background:linear-gradient(90deg,var(--olive),var(--gold2))}.version-row em{font-style:normal;color:var(--muted);font-size:12px;text-align:left}.warn{color:#6f3524!important;font-weight:800}
        .final{margin-top:38px;padding:clamp(26px,5vw,48px);border-radius:38px;border:1px solid rgba(21,17,12,.12);background:linear-gradient(145deg,rgba(255,249,237,.88),rgba(255,239,203,.76));box-shadow:var(--shadow);text-align:center}.final h2{margin:0;font-family:Almarai,sans-serif;font-size:clamp(32px,5vw,62px);line-height:1.12}.final p{max-width:780px;margin:18px auto 26px;color:var(--muted);line-height:1.9;font-size:18px}
        @keyframes floatA{from{transform:translate3d(0,0,0) rotate(0)}to{transform:translate3d(38px,30px,0) rotate(9deg)}}@media(max-width:1100px){.hero-grid,.grid-2{grid-template-columns:1fr}.grid-3,.grid-4{grid-template-columns:repeat(2,1fr)}}@media(max-width:760px){.shell{width:min(100% - 22px,1280px);padding-top:12px}.topbar{position:static;flex-direction:column;align-items:stretch;border-radius:22px}.topnav{justify-content:stretch}.nav-link{flex:1}.hero{border-radius:30px}.hero-grid{padding:24px}h1{letter-spacing:-1.2px}.grid-3,.grid-4,.metrics{grid-template-columns:1fr}.section-title{flex-direction:column;align-items:start}.version-row{grid-template-columns:44px 1fr 72px}}
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <a class="brand" href="/">
                <div class="mark">K</div>
                <div>
                    <strong>KABEERI Platform</strong>
                    <span>منصة تشغيل ونمو للأعمال والمطورين والشركاء</span>
                </div>
            </a>
            <nav class="topnav" aria-label="روابط الصفحة">
                <a class="nav-link" href="#audiences">لمن؟</a>
                <a class="nav-link" href="#onboarding">Onboarding</a>
                <a class="nav-link" href="#themes">الثيمات والبلجنز</a>
                <a class="nav-link" href="#developers">المطورين</a>
                <a class="nav-link" href="#plans">الاشتراكات</a>
                <a class="nav-link" href="/admin">Admin</a>
            </nav>
        </header>

        <main class="hero">
            <div class="hero-grid">
                <section>
                    <div class="kicker"><span class="dot"></span> منصة واحدة لبناء وتشغيل وتسويق ونمو الأعمال</div>
                    <h1>كابيري تجمع الموقع، الإدارة، المتجر، الشركاء، والثيمات في نظام واحد مفهوم.</h1>
                    <p class="lead">KABEERI ليست مجرد CMS أو متجر. هي منصة تشغيل كاملة تساعد العميل يبدأ من اختيار نوع نشاطه، يبني واجهته، يضيف خدماته ومنتجاته، يدير فريقه وعملاءه، ثم يتوسع عبر الثيمات والبلجنز والمطورين والمسوقين والشركاء.</p>
                    <div class="actions">
                        <a class="button primary" href="#onboarding">افهم مسار البداية</a>
                        <a class="button secondary" href="#audiences">اختر مسارك</a>
                        <a class="button ghost" href="{{ route('mall.index') }}">شاهد المول العام</a>
                    </div>
                </section>
                <aside>
                    <div class="value-card">
                        <h2>ماذا يحصل عليه العميل؟</h2>
                        <p>منصة جاهزة للتوسع: واجهة عامة، إدارة داخلية، متجر، CRM، عمليات، تكاملات، وثيمات وإضافات قابلة للنمو.</p>
                        <div class="value-list">
                            <div><span>Website + CMS</span><strong>موقع ومحتوى</strong></div>
                            <div><span>Business Suite</span><strong>تشغيل ومبيعات</strong></div>
                            <div><span>Marketplace</span><strong>ثيمات وبلجنز</strong></div>
                            <div><span>Partner Network</span><strong>مطورين ومسوقين</strong></div>
                        </div>
                    </div>
                    <div class="metrics">
                        <div class="metric"><span>Build</span><strong>{{ $overallPercent }}%</strong></div>
                        <div class="metric"><span>Tasks</span><strong>{{ $totalTasks }}</strong></div>
                        <div class="metric"><span>Pending</span><strong>{{ $totalPending }}</strong></div>
                    </div>
                </aside>
            </div>
        </main>

        <section class="section" id="audiences">
            <div class="section-title">
                <h2>كل جمهور له مسار واضح</h2>
                <p>المنصة تخاطب أصحاب الأعمال، المؤسسات، المطورين، والمسوقين. كل فئة تدخل لسبب مختلف وتحصل على قيمة مختلفة.</p>
            </div>
            <div class="grid-2">
                @foreach ($audiences as $audience)
                    <article class="card">
                        <span class="tag">{{ $audience['tag'] }}</span>
                        <h3>{{ $audience['title'] }}</h3>
                        <p>{{ $audience['desc'] }}</p>
                        <p class="path" style="margin-top:16px;"><strong>المسار:</strong> {{ $audience['path'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section" id="onboarding">
            <div class="section-title">
                <h2>Onboarding من أول زيارة لأول تشغيل</h2>
                <p>العميل لا يحتاج فهم كل شيء مرة واحدة. كابيري تقوده تدريجيًا حتى يصبح لديه نظام يعمل.</p>
            </div>
            <div class="grid-4">
                @foreach ($onboarding as $step)
                    <article class="card step" data-step="{{ $step['n'] }}">
                        <small>خطوة {{ $step['n'] }}</small>
                        <h3>{{ $step['title'] }}</h3>
                        <p>{{ $step['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section">
            <div class="section-title">
                <h2>الإمكانيات الأساسية</h2>
                <p>منصة وحدات: يبدأ العميل بما يحتاجه، ثم يضيف قدرات جديدة مع نمو النشاط.</p>
            </div>
            <div class="grid-3">
                @foreach ($capabilities as $capability)
                    <article class="card">
                        <h3>{{ $capability['title'] }}</h3>
                        <p>{{ $capability['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section grid-2" id="themes">
            <article class="card feature">
                <h3 style="font-size:38px;">الثيمات والبلجنز ليست زينة. هي طريقة توسع المنصة.</h3>
                <p>العميل يختار ثيم مناسب لمجاله، يطبق وصفة جاهزة للصفحات والمحتوى، ثم يضيف بلجنز حسب احتياجه. المطورون يستطيعون بناء وبيع هذه الثيمات والإضافات عبر متجر المنصة بعد مراجعة آمنة.</p>
                <div class="actions">
                    <a class="button secondary" href="/admin">إدارة الثيمات</a>
                    <a class="button ghost" href="#developers">مسار المطورين</a>
                </div>
            </article>
            <div class="grid-2">
                @foreach ($themeFlow as $flow)
                    <article class="card">
                        <h3>{{ $flow['title'] }}</h3>
                        <p>{{ $flow['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section grid-2" id="developers">
            <article class="card">
                <span class="tag">Developers</span>
                <h3>كيف يطوّر المطورون كابيري؟</h3>
                <p>يبنون ثيمات وبلجنز وConnectors وفق manifest واضح، يختبرونها، يقدّمونها للمراجعة، ثم يبيعونها داخل متجر المنصة أو يعرضون خدمات تطوير حولها.</p>
                <div class="chips">
                    <span class="chip">Theme Builder</span><span class="chip">Plugin Bundles</span><span class="chip">Connector SDK</span><span class="chip">Marketplace</span>
                </div>
            </article>
            <article class="card" id="marketers">
                <span class="tag">Marketers & Partners</span>
                <h3>كيف يستفيد المسوقون والشركاء؟</h3>
                <p>يبنون واجهات خدمات، يجذبون عملاء، يديرون الإحالات، يتابعون النتائج عبر CRM والتقارير، ويكبرون داخل Work Network وAcademy.</p>
                <div class="chips">
                    <span class="chip">Referrals</span><span class="chip">Partner Storefronts</span><span class="chip">Agency Profiles</span><span class="chip">Growth</span>
                </div>
            </article>
        </section>

        <section class="section" id="plans">
            <div class="section-title">
                <h2>مسارات الاشتراك والاستفادة</h2>
                <p>تصور واضح لمسارات القيمة. التسعير النهائي يمكن ضبطه لاحقًا حسب السوق والخدمات.</p>
            </div>
            <div class="grid-4">
                @foreach ($plans as $plan)
                    <article class="card plan {{ $plan['name'] === 'Business' ? 'featured' : '' }}">
                        <span class="tag">{{ $plan['for'] }}</span>
                        <h3>{{ $plan['name'] }}</h3>
                        <ul>
                            @foreach ($plan['items'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section grid-2">
            <article class="card">
                <div class="section-title" style="margin-bottom:0;">
                    <h2>حالة البناء</h2>
                    <p>مؤشرات داخلية يمكن نقلها لاحقًا للوحة الإدارة أو إخفاؤها من النسخة العامة.</p>
                </div>
                <div class="version-list">
                    @foreach ($versions as $version)
                        <div class="version-row">
                            <strong>{{ $version['name'] }}</strong>
                            <div class="bar"><span style="width: {{ $version['percent'] }}%"></span></div>
                            <em class="{{ $version['pending'] ? 'warn' : '' }}">{{ $version['pending'] ? $version['pending'].' pending' : 'ready' }}</em>
                        </div>
                    @endforeach
                </div>
            </article>
            <article class="card feature">
                <h3>جاهزية النشر</h3>
                <p>الصفحة الآن تشرح قيمة المنصة ومسارات الجمهور. قبل الإطلاق الرسمي نحتاج حسم UX tasks النهائية، إغلاق V2 pending، وتحديد نصوص التسعير والدعوات حسب استراتيجية البيع.</p>
                <a class="button secondary" href="/admin" style="margin-top:18px;">افتح لوحة الإدارة</a>
            </article>
        </section>

        <section class="final">
            <h2>كابيري منصة تنمو مع العميل ومع مجتمع المطورين والشركاء.</h2>
            <p>العميل يبدأ بمنصة جاهزة، المطور يضيف قيمة من خلال الثيمات والبلجنز، المسوق يجلب النمو، والشركاء يبنون خدمات حول المنصة. هذا هو جوهر كابيري: نظام أعمال قابل للتوسع، لا مجرد موقع.</p>
            <div class="actions" style="justify-content:center;">
                <a class="button primary" href="#onboarding">ابدأ مسار العميل</a>
                <a class="button secondary" href="#developers">انضم كمطور</a>
                <a class="button ghost" href="#marketers">مسار المسوقين</a>
            </div>
        </section>
    </div>
</body>
</html>
