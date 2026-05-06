@php
    $versions = [];
    foreach (range(1, 8) as $versionNumber) {
        $path = base_path("24_kabeeri_task_tracking/tasks/v{$versionNumber}.tasks.json");
        if (! file_exists($path)) {
            continue;
        }

        $payload = json_decode(file_get_contents($path), true);
        $tasks = $payload['tasks'] ?? [];
        $counts = array_count_values(array_map(fn ($task) => $task['status'] ?? 'unknown', $tasks));
        $versions[] = [
            'name' => 'V'.$versionNumber,
            'total' => count($tasks),
            'pending' => $counts['pending'] ?? 0,
            'done' => ($counts['codex_done'] ?? 0) + ($counts['verified'] ?? 0),
            'verified' => $counts['verified'] ?? 0,
        ];
    }

    $quickLinks = [
        ['title' => 'ابدأ من لوحة الإدارة', 'href' => '/admin', 'label' => 'إدارة النظام', 'note' => 'المكان الطبيعي لإدارة الموارد والبيانات.'],
        ['title' => 'افتح المول العام', 'href' => route('mall.index'), 'label' => 'Public Mall', 'note' => 'واجهة العرض العامة للمنتجات والخدمات والمواهب.'],
        ['title' => 'Mobile config API', 'href' => route('mobile.config'), 'label' => 'V7 Mobile', 'note' => 'تأكد من إعدادات تطبيق الموبايل.'],
        ['title' => 'Mobile manifest API', 'href' => route('mobile.manifest'), 'label' => 'API Manifest', 'note' => 'خريطة endpoints التي يقرأها تطبيق الموبايل.'],
    ];

    $workAreas = [
        ['name' => 'Core', 'desc' => 'المستخدمين، الصلاحيات، المنظمات، المواقع، الإعدادات.'],
        ['name' => 'CMS', 'desc' => 'المحتوى، القوائم، SEO، النماذج، واستيراد WordPress.'],
        ['name' => 'Mall', 'desc' => 'عرض الأعمال والمنتجات والخدمات والكورسات والمواهب والسياحة.'],
        ['name' => 'ERP / Operations', 'desc' => 'CRM، مبيعات، فواتير، مخزون، مشتريات، محاسبة، مشاريع.'],
        ['name' => 'Integrations', 'desc' => 'Connectors، credentials references، sync preview، conflicts.'],
        ['name' => 'Mobile / Desktop', 'desc' => 'APIs للموبايل وسجل أجهزة وسيناريو desktop sync dry-run.'],
    ];
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KABEERI Workspace</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=almarai:400,700,800|ibm-plex-sans-arabic:400,500,700" rel="stylesheet" />
    <style>
        :root {
            --ink: #201710;
            --muted: #726354;
            --paper: #fff8ea;
            --paper-strong: #fff2cf;
            --date: #7a3f21;
            --olive: #3f4c2f;
            --mint: #dce8bd;
            --line: rgba(32, 23, 16, .14);
            --shadow: 0 24px 70px rgba(54, 35, 12, .16);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            color: var(--ink);
            font-family: "IBM Plex Sans Arabic", "Almarai", sans-serif;
            background:
                radial-gradient(circle at 12% 10%, rgba(220, 232, 189, .95), transparent 28rem),
                radial-gradient(circle at 86% 2%, rgba(196, 111, 58, .35), transparent 25rem),
                linear-gradient(135deg, #fff8ea 0%, #f6e4bd 48%, #e7c891 100%);
        }

        a { color: inherit; text-decoration: none; }

        .shell {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
            padding: 28px 0 44px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: center;
            margin-bottom: 24px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
        }

        .mark {
            width: 48px;
            height: 48px;
            display: grid;
            place-items: center;
            border-radius: 18px;
            color: #fff8ea;
            background: linear-gradient(145deg, #2d2419, #88512d);
            box-shadow: var(--shadow);
            font-family: Almarai, sans-serif;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border: 1px solid var(--line);
            border-radius: 999px;
            background: rgba(255, 248, 234, .62);
            backdrop-filter: blur(10px);
            color: var(--muted);
            font-size: 14px;
        }

        .hero {
            position: relative;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 34px;
            background: rgba(255, 248, 234, .78);
            box-shadow: var(--shadow);
            padding: clamp(26px, 5vw, 58px);
        }

        .hero:before {
            content: "";
            position: absolute;
            inset-inline-start: -80px;
            top: -110px;
            width: 310px;
            height: 310px;
            border-radius: 999px;
            background: repeating-linear-gradient(45deg, rgba(63, 76, 47, .12) 0 12px, transparent 12px 24px);
            animation: drift 18s ease-in-out infinite alternate;
        }

        .hero-grid {
            position: relative;
            display: grid;
            grid-template-columns: 1.18fr .82fr;
            gap: 28px;
            align-items: stretch;
        }

        h1 {
            margin: 0;
            max-width: 820px;
            font-family: Almarai, sans-serif;
            font-size: clamp(38px, 7vw, 82px);
            line-height: 1.02;
            letter-spacing: -2px;
        }

        .lead {
            max-width: 720px;
            margin: 22px 0 0;
            color: var(--muted);
            font-size: clamp(17px, 2vw, 22px);
            line-height: 1.85;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 30px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            padding: 0 20px;
            border-radius: 16px;
            border: 1px solid rgba(32, 23, 16, .18);
            font-weight: 800;
            transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
        }

        .button:hover { transform: translateY(-2px); box-shadow: 0 14px 30px rgba(54, 35, 12, .13); }
        .primary { background: #2c2419; color: #fff8ea; }
        .secondary { background: rgba(255, 255, 255, .54); }

        .status-card {
            border-radius: 26px;
            padding: 24px;
            background: linear-gradient(160deg, #2c2419, #694225);
            color: #fff8ea;
            min-height: 100%;
        }

        .status-card h2 { margin: 0 0 14px; font-family: Almarai, sans-serif; font-size: 26px; }
        .status-card p { margin: 0 0 18px; color: rgba(255, 248, 234, .74); line-height: 1.7; }

        .version-list { display: grid; gap: 9px; }
        .version-row {
            display: grid;
            grid-template-columns: 48px 1fr auto;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 14px;
            background: rgba(255, 248, 234, .09);
        }

        .bar { height: 7px; overflow: hidden; border-radius: 999px; background: rgba(255, 248, 234, .18); }
        .bar span { display: block; height: 100%; background: var(--mint); border-radius: inherit; }
        .pending { color: #ffd99a; font-size: 12px; }

        .section-title {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: end;
            margin: 34px 0 16px;
        }

        .section-title h2 { margin: 0; font-family: Almarai, sans-serif; font-size: clamp(25px, 3vw, 38px); }
        .section-title p { margin: 0; color: var(--muted); max-width: 520px; line-height: 1.7; }

        .quick-grid, .area-grid, .steps-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
        }

        .card {
            min-height: 172px;
            padding: 20px;
            border: 1px solid var(--line);
            border-radius: 24px;
            background: rgba(255, 248, 234, .74);
            box-shadow: 0 12px 34px rgba(54, 35, 12, .08);
            transition: transform .18s ease, background .18s ease;
        }

        .card:hover { transform: translateY(-3px); background: rgba(255, 255, 255, .8); }
        .card small { color: var(--date); font-weight: 800; }
        .card h3 { margin: 14px 0 8px; font-size: 20px; }
        .card p { margin: 0; color: var(--muted); line-height: 1.7; }

        .area-grid { grid-template-columns: repeat(3, 1fr); }
        .area-card { background: rgba(63, 76, 47, .09); }

        .steps-grid { grid-template-columns: 1.2fr .9fr .9fr; }
        .step-card { min-height: 210px; }
        .step-card strong { display: block; margin-bottom: 12px; font-size: 22px; }
        .step-card ul { margin: 0; padding: 0; list-style: none; display: grid; gap: 10px; color: var(--muted); }
        .step-card li { padding: 10px 12px; border-radius: 12px; background: rgba(255, 255, 255, .45); }

        .footer-note {
            margin-top: 28px;
            padding: 18px 20px;
            border: 1px dashed rgba(32, 23, 16, .24);
            border-radius: 20px;
            color: var(--muted);
            background: rgba(255, 248, 234, .52);
            line-height: 1.8;
        }

        @keyframes drift {
            from { transform: translate3d(0, 0, 0) rotate(0deg); }
            to { transform: translate3d(34px, 28px, 0) rotate(8deg); }
        }

        @media (max-width: 920px) {
            .hero-grid, .quick-grid, .area-grid, .steps-grid { grid-template-columns: 1fr; }
            .section-title { align-items: start; flex-direction: column; }
            .topbar { align-items: start; flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <div class="brand">
                <div class="mark">K</div>
                <div>
                    <div>KABEERI Workspace</div>
                    <span style="color: var(--muted); font-size: 14px;">نقطة تشغيل واضحة للمشروع</span>
                </div>
            </div>
            <div class="pill">Laravel شغال · DB محدثة لحد V8 · آخر فحص أخضر</div>
        </header>

        <main class="hero">
            <div class="hero-grid">
                <section>
                    <div class="pill">لو الواجهة كانت تايهة، دي بوصلة التشغيل الجديدة</div>
                    <h1>اشتغل على كابيري من هنا، مش من صفحة Laravel الافتراضية.</h1>
                    <p class="lead">
                        المشروع كبير: CMS، Mall، ERP، Integrations، Mobile، Desktop. الصفحة دي بتجمع لك مداخل العمل المهمة وتقول لك كل جزء موجود ليه، عشان ما تفضلش تدور في routes أو ملفات الكود.
                    </p>
                    <div class="hero-actions">
                        <a class="button primary" href="/admin">افتح لوحة الإدارة</a>
                        <a class="button secondary" href="{{ route('mall.index') }}">شاهد المول العام</a>
                        <a class="button secondary" href="{{ route('mobile.config') }}">اختبر Mobile API</a>
                    </div>
                </section>

                <aside class="status-card">
                    <h2>حالة الإصدارات</h2>
                    <p>ده ملخص سريع من ملفات task tracker. أي pending ظاهر هنا يبقى محتاج حسم قبل النشر الرسمي.</p>
                    <div class="version-list">
                        @foreach ($versions as $version)
                            @php $percent = $version['total'] > 0 ? round(($version['done'] / $version['total']) * 100) : 0; @endphp
                            <div class="version-row">
                                <strong>{{ $version['name'] }}</strong>
                                <div class="bar"><span style="width: {{ $percent }}%"></span></div>
                                <span class="{{ $version['pending'] ? 'pending' : '' }}">{{ $version['pending'] ? $version['pending'].' pending' : 'ready' }}</span>
                            </div>
                        @endforeach
                    </div>
                </aside>
            </div>
        </main>

        <section>
            <div class="section-title">
                <h2>ابدأ منين؟</h2>
                <p>دي أهم مداخل التشغيل اليومية. لو أنت بتختبر، ابدأ بالمول وAPIs. لو بتدير بيانات، ادخل لوحة الإدارة.</p>
            </div>
            <div class="quick-grid">
                @foreach ($quickLinks as $link)
                    <a class="card" href="{{ $link['href'] }}">
                        <small>{{ $link['label'] }}</small>
                        <h3>{{ $link['title'] }}</h3>
                        <p>{{ $link['note'] }}</p>
                    </a>
                @endforeach
            </div>
        </section>

        <section>
            <div class="section-title">
                <h2>خريطة النظام</h2>
                <p>بدل أسماء modules مبعثرة، دي خريطة بسيطة لما تم بناؤه حتى الآن.</p>
            </div>
            <div class="area-grid">
                @foreach ($workAreas as $area)
                    <article class="card area-card">
                        <small>مساحة عمل</small>
                        <h3>{{ $area['name'] }}</h3>
                        <p>{{ $area['desc'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section>
            <div class="section-title">
                <h2>قبل ما نكمل UI</h2>
                <p>دي الخطوات اللي هتخلي المشروع قابل للاستخدام بجد، مش مجرد backend ضخم.</p>
            </div>
            <div class="steps-grid">
                <article class="card step-card">
                    <strong>1. نقفل V2 المتبقي</strong>
                    <ul>
                        <li>WooCommerce feed prototype</li>
                        <li>Filament Resources V2</li>
                        <li>Demo data + smoke/security/docs</li>
                    </ul>
                </article>
                <article class="card step-card">
                    <strong>2. نبني لوحة تشغيل</strong>
                    <ul>
                        <li>Dashboard للـ modules</li>
                        <li>صفحات إدارة للثيمات</li>
                        <li>إرشاد واضح لكل route</li>
                    </ul>
                </article>
                <article class="card step-card">
                    <strong>3. نعمل ثيمات مفهومة</strong>
                    <ul>
                        <li>Theme gallery</li>
                        <li>Preview قبل التطبيق</li>
                        <li>Demo pages جاهزة</li>
                    </ul>
                </article>
            </div>
        </section>

        <div class="footer-note">
            ملاحظة مهمة: دي ليست بديل نهائي للوحة Admin، لكنها نقطة دخول مفهومة للمشروع. الخطوة التالية الأفضل هي بناء Dashboard داخل Filament تربط الثيمات، المول، الموديولات، وحالة التراكر في مكان واحد.
        </div>
    </div>
</body>
</html>
