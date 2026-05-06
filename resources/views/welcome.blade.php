@php
    $versions = [];
    $totalTasks = 0;
    $totalDone = 0;
    $totalPending = 0;
    $totalVerified = 0;

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
        $verified = $counts['verified'] ?? 0;
        $total = count($tasks);

        $totalTasks += $total;
        $totalDone += $done;
        $totalPending += $pending;
        $totalVerified += $verified;

        $versions[] = [
            'name' => 'V'.$versionNumber,
            'total' => $total,
            'pending' => $pending,
            'done' => $done,
            'verified' => $verified,
            'percent' => $total > 0 ? round(($done / $total) * 100) : 0,
        ];
    }

    $overallPercent = $totalTasks > 0 ? round(($totalDone / $totalTasks) * 100) : 0;

    $primaryActions = [
        ['title' => 'لوحة الإدارة', 'href' => '/admin', 'eyebrow' => 'Control Room', 'desc' => 'إدارة البيانات والموارد والصلاحيات من Filament.', 'tone' => 'dark'],
        ['title' => 'المول العام', 'href' => route('mall.index'), 'eyebrow' => 'Public Experience', 'desc' => 'واجهة العرض العامة للأعمال والمنتجات والخدمات.', 'tone' => 'gold'],
        ['title' => 'Mobile API', 'href' => route('mobile.config'), 'eyebrow' => 'V7 Foundation', 'desc' => 'اختبار config والmanifest الخاصين بتطبيق الموبايل.', 'tone' => 'olive'],
        ['title' => 'Sitemap', 'href' => route('sitemap'), 'eyebrow' => 'SEO Health', 'desc' => 'مراجعة خريطة الروابط العامة القابلة للأرشفة.', 'tone' => 'clay'],
    ];

    $operatingLanes = [
        ['name' => 'Core & Identity', 'tag' => 'V1', 'desc' => 'المستخدمين، الصلاحيات، المنظمات، المواقع، الإعدادات، feature flags.', 'items' => ['Users', 'Roles', 'Organizations']],
        ['name' => 'CMS & Themes', 'tag' => 'V2', 'desc' => 'المحتوى، القوائم، SEO، النماذج، الثيمات، demo importer، واستيراد WordPress.', 'items' => ['Content', 'Themes', 'Forms']],
        ['name' => 'Operations Suite', 'tag' => 'V3/V5', 'desc' => 'CRM، مبيعات، فواتير، مخزون، مشتريات، محاسبة، عقود، helpdesk.', 'items' => ['CRM', 'Finance', 'ERP']],
        ['name' => 'Mall & Network', 'tag' => 'V4/V6', 'desc' => 'المول العام، المراجعات، الثقة، الشركاء، Work Network، Academy، الصناعة.', 'items' => ['Mall', 'Trust', 'Academy']],
        ['name' => 'Integration Hub', 'tag' => 'V5/V6', 'desc' => 'Connectors، credentials references، sync preview، webhooks، conflicts، API gateway.', 'items' => ['Connectors', 'Sync', 'Gateway']],
        ['name' => 'Mobile & Desktop', 'tag' => 'V7/V8', 'desc' => 'إعدادات الموبايل، الأجهزة، push tokens، desktop sync dry-run، file queue.', 'items' => ['Mobile', 'Desktop', 'Devices']],
    ];

    $apiLinks = [
        ['method' => 'GET', 'path' => '/api/mobile/config', 'href' => route('mobile.config'), 'desc' => 'إعدادات تطبيق الموبايل الحالية.'],
        ['method' => 'GET', 'path' => '/api/mobile/manifest', 'href' => route('mobile.manifest'), 'desc' => 'خريطة endpoints وقدرات التطبيق.'],
        ['method' => 'GET', 'path' => '/api/mobile/theme', 'href' => route('mobile.theme'), 'desc' => 'بروفايل الثيم النشط للموبايل.'],
        ['method' => 'POST', 'path' => '/api/desktop/sync/push-dry-run', 'href' => '#desktop-note', 'desc' => 'تسجيل عمليات desktop بدون تطبيق تغييرات.'],
    ];

    $nextMoves = [
        ['number' => '01', 'title' => 'إغلاق V2 المتبقي', 'desc' => 'WooCommerce prototype، Filament resources، demo data، smoke/security/docs/RC.'],
        ['number' => '02', 'title' => 'UX Tasks Pack', 'desc' => 'تحويل كل مساحة عمل إلى flows واضحة: start, manage, preview, publish.'],
        ['number' => '03', 'title' => 'Theme Studio', 'desc' => 'Gallery، preview، apply، demo pages، وربط الثيمات بالمواقع.'],
        ['number' => '04', 'title' => 'Production Hardening', 'desc' => 'Auth middleware، rate limits، queues، backups، monitoring، وdeployment checklist.'],
    ];
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KABEERI Command Center</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=almarai:400,700,800|ibm-plex-sans-arabic:400,500,600,700" rel="stylesheet" />
    <style>
        :root {
            --ink: #1b1711;
            --ink-soft: #3d3327;
            --muted: #756654;
            --paper: #fff9ec;
            --paper-2: #f8e9c7;
            --gold: #c88a32;
            --gold-2: #f4c96e;
            --olive: #43513a;
            --olive-2: #dfe8bf;
            --clay: #9b5434;
            --night: #211a13;
            --line: rgba(27, 23, 17, .13);
            --glass: rgba(255, 249, 236, .72);
            --shadow: 0 28px 90px rgba(70, 44, 16, .17);
            --soft-shadow: 0 16px 45px rgba(70, 44, 16, .09);
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }

        body {
            margin: 0;
            min-height: 100vh;
            color: var(--ink);
            font-family: "IBM Plex Sans Arabic", "Almarai", sans-serif;
            background:
                radial-gradient(circle at 8% 8%, rgba(223, 232, 191, .9), transparent 25rem),
                radial-gradient(circle at 90% 12%, rgba(244, 201, 110, .42), transparent 22rem),
                radial-gradient(circle at 52% 78%, rgba(155, 84, 52, .14), transparent 32rem),
                linear-gradient(135deg, #fff9ec 0%, #f3dfb7 52%, #e3bd7a 100%);
            background-attachment: fixed;
        }

        body:before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            opacity: .28;
            background-image:
                linear-gradient(rgba(27, 23, 17, .055) 1px, transparent 1px),
                linear-gradient(90deg, rgba(27, 23, 17, .05) 1px, transparent 1px);
            background-size: 42px 42px;
            mask-image: linear-gradient(to bottom, #000, transparent 78%);
        }

        a { color: inherit; text-decoration: none; }
        .shell { width: min(1280px, calc(100% - 34px)); margin: 0 auto; padding: 26px 0 54px; }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            position: sticky;
            top: 14px;
            z-index: 20;
            padding: 12px;
            border: 1px solid rgba(27, 23, 17, .1);
            border-radius: 28px;
            background: rgba(255, 249, 236, .72);
            backdrop-filter: blur(18px);
            box-shadow: 0 12px 36px rgba(70, 44, 16, .08);
        }

        .brand { display: flex; align-items: center; gap: 12px; }
        .mark {
            width: 54px;
            height: 54px;
            display: grid;
            place-items: center;
            border-radius: 20px;
            color: #fff9ec;
            background: conic-gradient(from 210deg, #211a13, #8e5831, #dba349, #43513a, #211a13);
            box-shadow: 0 16px 38px rgba(70, 44, 16, .24);
            font-family: Almarai, sans-serif;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .brand strong { display: block; font-family: Almarai, sans-serif; font-size: 18px; }
        .brand span { color: var(--muted); font-size: 13px; }

        .topnav { display: flex; flex-wrap: wrap; justify-content: flex-end; gap: 8px; }
        .nav-link, .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 40px;
            padding: 0 13px;
            border: 1px solid rgba(27, 23, 17, .12);
            border-radius: 999px;
            background: rgba(255, 255, 255, .42);
            color: var(--ink-soft);
            font-size: 13px;
            font-weight: 700;
        }

        .nav-link:hover { background: #fff9ec; transform: translateY(-1px); }

        .hero {
            position: relative;
            overflow: hidden;
            margin-top: 20px;
            border: 1px solid rgba(27, 23, 17, .12);
            border-radius: 42px;
            background:
                linear-gradient(145deg, rgba(255, 249, 236, .87), rgba(255, 239, 199, .7)),
                radial-gradient(circle at 20% 20%, rgba(223, 232, 191, .8), transparent 22rem);
            box-shadow: var(--shadow);
        }

        .hero:before,
        .hero:after {
            content: "";
            position: absolute;
            border-radius: 999px;
            pointer-events: none;
        }

        .hero:before {
            width: 360px;
            height: 360px;
            inset-inline-start: -120px;
            top: -150px;
            background: repeating-linear-gradient(45deg, rgba(67, 81, 58, .15) 0 10px, transparent 10px 23px);
            animation: floatA 20s ease-in-out infinite alternate;
        }

        .hero:after {
            width: 280px;
            height: 280px;
            inset-inline-end: -70px;
            bottom: -120px;
            background: radial-gradient(circle, rgba(200, 138, 50, .28), transparent 68%);
            animation: floatB 16s ease-in-out infinite alternate;
        }

        .hero-grid {
            position: relative;
            display: grid;
            grid-template-columns: minmax(0, 1.18fr) minmax(360px, .82fr);
            gap: 24px;
            padding: clamp(26px, 5vw, 58px);
        }

        .kicker {
            display: inline-flex;
            width: fit-content;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border: 1px solid rgba(27, 23, 17, .12);
            border-radius: 999px;
            background: rgba(255, 255, 255, .46);
            color: var(--clay);
            font-weight: 800;
            font-size: 13px;
        }

        .dot { width: 9px; height: 9px; border-radius: 50%; background: #4f8f45; box-shadow: 0 0 0 7px rgba(79, 143, 69, .13); }

        h1 {
            margin: 22px 0 0;
            max-width: 820px;
            font-family: Almarai, sans-serif;
            font-size: clamp(42px, 7vw, 92px);
            line-height: .98;
            letter-spacing: -2.6px;
        }

        .lead {
            max-width: 760px;
            margin: 24px 0 0;
            color: var(--muted);
            font-size: clamp(17px, 1.8vw, 22px);
            line-height: 1.95;
        }

        .hero-actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 30px; }
        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 52px;
            padding: 0 21px;
            border-radius: 17px;
            border: 1px solid rgba(27, 23, 17, .16);
            font-weight: 800;
            transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
        }
        .button:hover { transform: translateY(-2px); box-shadow: 0 16px 34px rgba(70, 44, 16, .14); }
        .button.primary { color: #fff9ec; background: var(--night); }
        .button.secondary { background: rgba(255, 255, 255, .54); }

        .metric-strip {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            margin-top: 32px;
        }

        .metric {
            padding: 16px;
            border: 1px solid rgba(27, 23, 17, .1);
            border-radius: 22px;
            background: rgba(255, 255, 255, .42);
        }
        .metric span { color: var(--muted); font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: .4px; }
        .metric strong { display: block; margin-top: 7px; font-family: Almarai, sans-serif; font-size: 30px; }

        .command-panel {
            display: grid;
            gap: 14px;
        }

        .panel-card {
            border-radius: 30px;
            padding: 24px;
            color: #fff9ec;
            background:
                linear-gradient(160deg, rgba(33, 26, 19, .96), rgba(95, 60, 34, .96)),
                radial-gradient(circle at 10% 10%, rgba(244, 201, 110, .3), transparent 18rem);
            box-shadow: 0 22px 55px rgba(33, 26, 19, .22);
        }

        .panel-head { display: flex; justify-content: space-between; gap: 14px; align-items: start; margin-bottom: 16px; }
        .panel-head h2 { margin: 0; font-family: Almarai, sans-serif; font-size: 28px; }
        .panel-head p { margin: 8px 0 0; color: rgba(255, 249, 236, .7); line-height: 1.7; }
        .score {
            min-width: 80px;
            height: 80px;
            display: grid;
            place-items: center;
            border-radius: 24px;
            background: rgba(255, 249, 236, .1);
            border: 1px solid rgba(255, 249, 236, .17);
            font-family: Almarai, sans-serif;
            font-weight: 800;
            font-size: 24px;
        }

        .version-list { display: grid; gap: 9px; }
        .version-row {
            display: grid;
            grid-template-columns: 48px 1fr 78px;
            align-items: center;
            gap: 10px;
            padding: 11px 12px;
            border-radius: 16px;
            background: rgba(255, 249, 236, .08);
        }
        .version-row strong { font-family: Almarai, sans-serif; }
        .bar { height: 8px; overflow: hidden; border-radius: 999px; background: rgba(255, 249, 236, .17); }
        .bar span { display: block; height: 100%; border-radius: inherit; background: linear-gradient(90deg, var(--olive-2), var(--gold-2)); }
        .version-row em { font-style: normal; font-size: 12px; color: rgba(255, 249, 236, .78); text-align: left; }
        .version-row em.warn { color: #ffd99a; }

        .mini-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .mini-card {
            min-height: 122px;
            padding: 18px;
            border: 1px solid var(--line);
            border-radius: 24px;
            background: rgba(255, 249, 236, .72);
            box-shadow: var(--soft-shadow);
        }
        .mini-card b { display: block; font-family: Almarai, sans-serif; font-size: 18px; margin-bottom: 8px; }
        .mini-card p { margin: 0; color: var(--muted); line-height: 1.7; font-size: 14px; }

        .section { margin-top: 34px; }
        .section-title {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 18px;
            margin-bottom: 16px;
        }
        .section-title h2 { margin: 0; font-family: Almarai, sans-serif; font-size: clamp(28px, 3vw, 42px); letter-spacing: -.8px; }
        .section-title p { max-width: 580px; margin: 0; color: var(--muted); line-height: 1.8; }

        .action-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
        .action-card {
            position: relative;
            overflow: hidden;
            min-height: 210px;
            padding: 22px;
            border: 1px solid var(--line);
            border-radius: 28px;
            background: var(--glass);
            box-shadow: var(--soft-shadow);
            transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
        }
        .action-card:hover { transform: translateY(-4px); box-shadow: 0 24px 60px rgba(70, 44, 16, .15); background: rgba(255, 255, 255, .72); }
        .action-card:after {
            content: "";
            position: absolute;
            width: 120px;
            height: 120px;
            border-radius: 999px;
            inset-inline-end: -44px;
            bottom: -44px;
            opacity: .34;
            background: var(--gold-2);
        }
        .action-card.dark:after { background: #211a13; }
        .action-card.olive:after { background: var(--olive); }
        .action-card.clay:after { background: var(--clay); }
        .action-card small { position: relative; z-index: 1; color: var(--clay); font-weight: 800; }
        .action-card h3 { position: relative; z-index: 1; margin: 18px 0 10px; font-family: Almarai, sans-serif; font-size: 24px; }
        .action-card p { position: relative; z-index: 1; margin: 0; color: var(--muted); line-height: 1.75; }
        .action-card .arrow { position: absolute; inset-inline-start: 22px; bottom: 20px; font-weight: 800; color: var(--ink-soft); }

        .lane-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
        .lane-card {
            padding: 22px;
            border: 1px solid var(--line);
            border-radius: 28px;
            background: rgba(255, 249, 236, .64);
            box-shadow: var(--soft-shadow);
        }
        .lane-top { display: flex; justify-content: space-between; gap: 12px; align-items: center; margin-bottom: 12px; }
        .tag { padding: 7px 10px; border-radius: 999px; background: rgba(67, 81, 58, .12); color: var(--olive); font-size: 12px; font-weight: 800; }
        .lane-card h3 { margin: 0; font-family: Almarai, sans-serif; font-size: 23px; }
        .lane-card p { margin: 0 0 16px; color: var(--muted); line-height: 1.75; }
        .chips { display: flex; flex-wrap: wrap; gap: 8px; }
        .chip { padding: 8px 10px; border-radius: 999px; background: rgba(255, 255, 255, .55); color: var(--ink-soft); font-size: 12px; font-weight: 800; }

        .split { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .api-panel, .roadmap-panel {
            border: 1px solid var(--line);
            border-radius: 32px;
            background: rgba(255, 249, 236, .72);
            box-shadow: var(--soft-shadow);
            padding: 22px;
        }
        .api-list, .roadmap-list { display: grid; gap: 10px; margin-top: 16px; }
        .api-row, .roadmap-row {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 12px;
            align-items: start;
            padding: 14px;
            border-radius: 18px;
            background: rgba(255, 255, 255, .47);
        }
        .method, .number {
            min-width: 54px;
            height: 34px;
            display: inline-grid;
            place-items: center;
            border-radius: 12px;
            background: var(--night);
            color: #fff9ec;
            font-size: 12px;
            font-weight: 800;
            direction: ltr;
        }
        .api-row code { display: block; direction: ltr; text-align: left; font-family: Consolas, monospace; color: var(--ink-soft); margin-bottom: 4px; }
        .api-row p, .roadmap-row p { margin: 0; color: var(--muted); line-height: 1.65; font-size: 14px; }
        .roadmap-row h3 { margin: 0 0 5px; font-size: 17px; }

        .notice {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 18px;
            align-items: center;
            margin-top: 34px;
            padding: 20px;
            border: 1px dashed rgba(27, 23, 17, .22);
            border-radius: 28px;
            background: rgba(255, 249, 236, .62);
            color: var(--muted);
            line-height: 1.75;
        }
        .notice b { color: var(--ink); }
        .seal { width: 52px; height: 52px; display: grid; place-items: center; border-radius: 18px; background: rgba(67, 81, 58, .13); color: var(--olive); font-weight: 800; }

        @keyframes floatA { from { transform: translate3d(0, 0, 0) rotate(0deg); } to { transform: translate3d(38px, 30px, 0) rotate(9deg); } }
        @keyframes floatB { from { transform: translate3d(0, 0, 0) scale(1); } to { transform: translate3d(-26px, -22px, 0) scale(1.08); } }

        @media (max-width: 1080px) {
            .hero-grid, .split { grid-template-columns: 1fr; }
            .action-grid, .lane-grid { grid-template-columns: repeat(2, 1fr); }
            .command-panel { grid-template-columns: 1fr; }
        }

        @media (max-width: 720px) {
            .shell { width: min(100% - 22px, 1280px); padding-top: 12px; }
            .topbar { position: static; align-items: stretch; flex-direction: column; border-radius: 22px; }
            .topnav { justify-content: stretch; }
            .nav-link { flex: 1; justify-content: center; }
            .hero { border-radius: 30px; }
            .hero-grid { padding: 24px; }
            h1 { letter-spacing: -1.2px; }
            .metric-strip, .action-grid, .lane-grid, .mini-grid { grid-template-columns: 1fr; }
            .section-title { align-items: start; flex-direction: column; }
            .notice { grid-template-columns: 1fr; }
            .version-row { grid-template-columns: 44px 1fr 70px; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <a class="brand" href="/">
                <div class="mark">K</div>
                <div>
                    <strong>KABEERI Command Center</strong>
                    <span>واجهة تشغيل منظمة للمشروع بالكامل</span>
                </div>
            </a>
            <nav class="topnav" aria-label="روابط سريعة">
                <a class="nav-link" href="/admin">Admin</a>
                <a class="nav-link" href="{{ route('mall.index') }}">Mall</a>
                <a class="nav-link" href="#system-map">الخريطة</a>
                <a class="nav-link" href="#next">التالي</a>
            </nav>
        </header>

        <main class="hero">
            <div class="hero-grid">
                <section>
                    <div class="kicker"><span class="dot"></span> النظام يعمل · قاعدة البيانات محدثة · مركز التحكم جاهز</div>
                    <h1>منصة كبيرة تحتاج واجهة تفكير، مش مجرد صفحة ترحيب.</h1>
                    <p class="lead">
                        دي نقطة دخول احترافية تجمع اتجاهات العمل في كابيري: أين تدير البيانات، أين ترى الواجهات العامة، أين تختبر APIs، وما الذي يحتاج قرار قبل النشر. الهدف إنك تفتح الصفحة وتعرف تتحرك فورًا.
                    </p>
                    <div class="hero-actions">
                        <a class="button primary" href="/admin">افتح لوحة الإدارة</a>
                        <a class="button secondary" href="{{ route('mall.index') }}">استعرض تجربة المول</a>
                        <a class="button secondary" href="#api">راجع واجهات API</a>
                    </div>

                    <div class="metric-strip" aria-label="مؤشرات سريعة">
                        <div class="metric"><span>Progress</span><strong>{{ $overallPercent }}%</strong></div>
                        <div class="metric"><span>Total Tasks</span><strong>{{ $totalTasks }}</strong></div>
                        <div class="metric"><span>Pending</span><strong>{{ $totalPending }}</strong></div>
                        <div class="metric"><span>Verified</span><strong>{{ $totalVerified }}</strong></div>
                    </div>
                </section>

                <aside class="command-panel" aria-label="حالة الإصدارات">
                    <div class="panel-card">
                        <div class="panel-head">
                            <div>
                                <h2>حالة البناء</h2>
                                <p>ملخص من ملفات task tracker. الهدف هنا كشف الحقيقة بسرعة: ماذا اكتمل، وماذا ما زال pending.</p>
                            </div>
                            <div class="score">{{ $overallPercent }}%</div>
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
                    </div>
                    <div class="mini-grid">
                        <div class="mini-card">
                            <b>التركيز الآن</b>
                            <p>حسم V2 المتبقي ثم بناء UX/UI tasks pack بدل تطوير واجهات عشوائية.</p>
                        </div>
                        <div class="mini-card">
                            <b>حدود آمنة</b>
                            <p>Mobile/Desktop foundations لا تخزن raw tokens ولا تطبق sync payloads مباشرة.</p>
                        </div>
                    </div>
                </aside>
            </div>
        </main>

        <section class="section" id="start">
            <div class="section-title">
                <h2>مداخل العمل الأساسية</h2>
                <p>أربع بوابات واضحة بدل التوهان بين routes. كل بطاقة تخبرك لماذا تستخدمها ومتى تبدأ منها.</p>
            </div>
            <div class="action-grid">
                @foreach ($primaryActions as $action)
                    <a class="action-card {{ $action['tone'] }}" href="{{ $action['href'] }}">
                        <small>{{ $action['eyebrow'] }}</small>
                        <h3>{{ $action['title'] }}</h3>
                        <p>{{ $action['desc'] }}</p>
                        <span class="arrow">اذهب الآن</span>
                    </a>
                @endforeach
            </div>
        </section>

        <section class="section" id="system-map">
            <div class="section-title">
                <h2>خريطة النظام</h2>
                <p>النظام مقسوم لمسارات تشغيل. كل مسار له وظيفة واضحة، وده يساعدنا لاحقًا نبني Dashboard حقيقية لكل مساحة.</p>
            </div>
            <div class="lane-grid">
                @foreach ($operatingLanes as $lane)
                    <article class="lane-card">
                        <div class="lane-top">
                            <h3>{{ $lane['name'] }}</h3>
                            <span class="tag">{{ $lane['tag'] }}</span>
                        </div>
                        <p>{{ $lane['desc'] }}</p>
                        <div class="chips">
                            @foreach ($lane['items'] as $item)
                                <span class="chip">{{ $item }}</span>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section split" id="api">
            <div class="api-panel">
                <div class="section-title" style="margin-bottom: 0;">
                    <h2>واجهات API السريعة</h2>
                    <p>روابط مفيدة للاختبار والفهم. POST endpoints معروضة كعقد تشغيل وليست روابط تصفح مباشرة.</p>
                </div>
                <div class="api-list">
                    @foreach ($apiLinks as $api)
                        <a class="api-row" href="{{ $api['href'] }}">
                            <span class="method">{{ $api['method'] }}</span>
                            <div>
                                <code>{{ $api['path'] }}</code>
                                <p>{{ $api['desc'] }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="roadmap-panel" id="next">
                <div class="section-title" style="margin-bottom: 0;">
                    <h2>خطة الحركة التالية</h2>
                    <p>الواجهة دي تنظم الموجود. بناء UX كامل للنظام يحتاج تاسكات واضحة ومسارات قبول.</p>
                </div>
                <div class="roadmap-list">
                    @foreach ($nextMoves as $move)
                        <div class="roadmap-row">
                            <span class="number">{{ $move['number'] }}</span>
                            <div>
                                <h3>{{ $move['title'] }}</h3>
                                <p>{{ $move['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <div class="notice" id="desktop-note">
            <div class="seal">UX</div>
            <div>
                <b>ملاحظة تشغيل:</b>
                هذه الصفحة أصبحت Command Center للمشروع، لكنها ليست بديلًا عن UX tasks pack القادم. أفضل خطوة بعدها هي تحويل كل مساحة عمل إلى شاشة Admin/Frontend مفهومة: قائمة، تفاصيل، إنشاء، معاينة، نشر، وتقارير حالة.
            </div>
            <a class="button secondary" href="/admin">ابدأ من Admin</a>
        </div>
    </div>
</body>
</html>
