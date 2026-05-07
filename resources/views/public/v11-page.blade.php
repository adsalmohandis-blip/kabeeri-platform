@php
    $page = $data['page'];
    $pageConfig = $data['page_config'];
    $pages = $data['pages'];
    $storyLayers = $data['story_layers'];
    $audiences = $data['audiences'];
    $journeys = $data['journeys'];
    $onboardingSteps = $data['onboarding_steps'];
    $wizard = $data['wizard'];
    $pricingLayers = $data['pricing_layers'];
    $plans = $data['plans'];
    $templates = $data['templates'];
    $faq = $data['faq'];
    $release = $data['release'];
    $nextRuntime = $data['next_runtime'];

    $audienceKey = match ($page) {
        'business' => 'business',
        'enterprise' => 'enterprise',
        'developers' => 'developers',
        'partners' => 'partners',
        default => null,
    };
    $selectedAudience = $audienceKey ? $audiences[$audienceKey] : null;

    $publicNav = [
        ['label' => 'الرئيسية العامة', 'route' => 'public.landing'],
        ['label' => 'اختر مسارك', 'route' => 'public.audiences'],
        ['label' => 'الأسعار', 'route' => 'public.pricing'],
        ['label' => 'Onboarding', 'route' => 'public.onboarding'],
        ['label' => 'الثقة', 'route' => 'public.trust'],
        ['label' => 'طلب ديمو', 'route' => 'public.contact'],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Localization\KabeeriLocale::direction() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageConfig['label'] }} | KABEERI Public Bridge</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=almarai:400,700,800|ibm-plex-sans-arabic:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --ink: #17130d;
            --muted: #17130d;
            --paper: #fffaf0;
            --cream: #fffaf0;
            --olive: #17130d;
            --olive-dark: #17130d;
            --copper: #c98a2e;
            --gold: #c98a2e;
            --mint: #17130d;
            --line: rgba(23,19,13, .14);
            --shadow: 0 24px 90px rgba(23,19,13, .14);
            --radius-xl: 34px;
            --radius-lg: 24px;
            --radius-md: 16px;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            color: var(--ink);
            background:
                radial-gradient(circle at top right, rgba(201,138,46, .34), transparent 28rem),
                radial-gradient(circle at 10% 18%, rgba(23,19,13, .22), transparent 24rem),
                linear-gradient(135deg, #fffaf0 0%, #fffaf0 46%, #fffaf0 100%);
            font-family: "IBM Plex Sans Arabic", "Almarai", "Tajawal", sans-serif;
            min-height: 100vh;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background-image:
                linear-gradient(rgba(23,19,13, .035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(23,19,13, .035) 1px, transparent 1px);
            background-size: 42px 42px;
            mask-image: linear-gradient(to bottom, rgba(0,0,0, .85), transparent);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .shell {
            width: min(100% - 32px, 1320px);
            margin: 0 auto;
            padding: 18px 0 70px;
        }

        .topbar {
            position: sticky;
            top: 14px;
            z-index: 20;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 14px;
            border: 1px solid rgba(255,250,240, .62);
            border-radius: 999px;
            background: rgba(255,250,240, .74);
            box-shadow: 0 18px 60px rgba(23,19,13, .12);
            backdrop-filter: blur(22px);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 210px;
        }

        .mark {
            display: grid;
            place-items: center;
            width: 46px;
            height: 46px;
            border-radius: 17px;
            color: #fffaf0;
            background:
                radial-gradient(circle at 35% 28%, rgba(255,250,240, .42), transparent 32%),
                linear-gradient(135deg, var(--olive-dark), var(--olive));
            font-weight: 800;
            letter-spacing: -.08em;
            box-shadow: inset 0 0 0 1px rgba(255,250,240, .24), 0 14px 30px rgba(23,19,13, .22);
        }

        .brand strong,
        .card h3,
        .section-title h2,
        .hero h1 {
            letter-spacing: -.04em;
        }

        .brand small {
            display: block;
            color: var(--muted);
            font-size: 12px;
            margin-top: 2px;
        }

        .nav {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 6px;
        }

        .nav a {
            padding: 10px 13px;
            border-radius: 999px;
            color: rgba(23,19,13, .74);
            font-weight: 700;
            font-size: 13px;
            transition: .2s ease;
        }

        .nav a:hover,
        .nav a.active {
            color: var(--olive-dark);
            background: rgba(23,19,13, .12);
        }

        .top-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 44px;
            padding: 11px 18px;
            border: 1px solid rgba(23,19,13, .15);
            border-radius: 999px;
            background: rgba(255,250,240, .62);
            color: var(--olive-dark);
            font-weight: 800;
            transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 34px rgba(23,19,13, .13);
        }

        .button.primary {
            color: #fffaf0;
            border-color: transparent;
            background: linear-gradient(135deg, var(--olive-dark), var(--olive));
        }

        .button.copper {
            color: #fffaf0;
            border-color: transparent;
            background: linear-gradient(135deg, #17130d, var(--copper));
        }

        .hero {
            position: relative;
            overflow: hidden;
            margin-top: 22px;
            border: 1px solid rgba(255,250,240, .7);
            border-radius: 46px;
            background:
                linear-gradient(145deg, rgba(255,250,240, .88), rgba(255,250,240, .52)),
                radial-gradient(circle at 10% 20%, rgba(201,138,46, .24), transparent 26rem);
            box-shadow: var(--shadow);
        }

        .hero::after {
            content: "";
            position: absolute;
            inset-inline-start: -7rem;
            bottom: -11rem;
            width: 28rem;
            height: 28rem;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(23,19,13, .26), transparent 68%);
            animation: drift 9s ease-in-out infinite alternate;
        }

        .hero-grid {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: minmax(0, 1.08fr) minmax(340px, .92fr);
            gap: 28px;
            padding: 54px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border: 1px solid rgba(23,19,13, .18);
            border-radius: 999px;
            color: var(--olive-dark);
            background: rgba(23,19,13, .52);
            font-size: 13px;
            font-weight: 800;
        }

        .eyebrow::before {
            content: "";
            width: 9px;
            height: 9px;
            border-radius: 999px;
            background: var(--gold);
            box-shadow: 0 0 0 7px rgba(201,138,46, .16);
        }

        .hero h1 {
            max-width: 820px;
            margin: 22px 0 16px;
            color: #17130d;
            font-size: clamp(42px, 7vw, 88px);
            line-height: .98;
        }

        .hero p {
            max-width: 760px;
            margin: 0;
            color: rgba(23,19,13, .72);
            font-size: clamp(17px, 2vw, 22px);
            line-height: 1.85;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 26px;
        }

        .signal-board {
            display: grid;
            gap: 12px;
        }

        .signal {
            position: relative;
            overflow: hidden;
            padding: 20px;
            border: 1px solid rgba(23,19,13, .12);
            border-radius: var(--radius-lg);
            background: rgba(255,250,240, .7);
            box-shadow: 0 18px 44px rgba(23,19,13, .09);
        }

        .signal.dark {
            color: #fffaf0;
            background:
                radial-gradient(circle at 15% 20%, rgba(201,138,46, .2), transparent 18rem),
                linear-gradient(135deg, var(--olive-dark), #17130d);
        }

        .signal strong {
            display: block;
            font-size: 15px;
        }

        .signal span {
            display: block;
            margin-top: 7px;
            color: inherit;
            opacity: .72;
            line-height: 1.7;
        }

        .section {
            margin-top: 22px;
            padding: 30px;
            border: 1px solid rgba(255,250,240, .62);
            border-radius: var(--radius-xl);
            background: rgba(255,250,240, .66);
            box-shadow: 0 18px 54px rgba(23,19,13, .08);
        }

        .section.dark {
            color: #fffaf0;
            background:
                radial-gradient(circle at top left, rgba(201,138,46, .16), transparent 26rem),
                linear-gradient(135deg, #17130d, #17130d);
        }

        .section-title {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 18px;
        }

        .section-title h2 {
            margin: 0;
            font-size: clamp(28px, 4vw, 46px);
            line-height: 1.08;
        }

        .section-title p {
            max-width: 620px;
            margin: 0;
            color: var(--muted);
            line-height: 1.8;
        }

        .section.dark .section-title p,
        .section.dark .muted {
            color: rgba(255,250,240, .7);
        }

        .grid-2,
        .grid-3,
        .grid-4 {
            display: grid;
            gap: 14px;
        }

        .grid-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .grid-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .grid-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .card {
            position: relative;
            overflow: hidden;
            padding: 22px;
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            background: rgba(255,250,240, .72);
            box-shadow: 0 14px 40px rgba(23,19,13, .07);
        }

        .dark .card {
            border-color: rgba(255,250,240, .14);
            background: rgba(255,250,240, .08);
            box-shadow: none;
        }

        .card h3 {
            margin: 0 0 10px;
            font-size: 22px;
        }

        .card p,
        .card li {
            color: rgba(23,19,13, .7);
            line-height: 1.8;
        }

        .dark .card p,
        .dark .card li {
            color: rgba(255,250,240, .72);
        }

        .card p {
            margin: 0;
        }

        .chip,
        .step-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 28px;
            padding: 5px 10px;
            border-radius: 999px;
            color: var(--olive-dark);
            background: rgba(23,19,13, .72);
            font-size: 12px;
            font-weight: 800;
        }

        .step-badge {
            width: 46px;
            height: 46px;
            color: #fffaf0;
            background: linear-gradient(135deg, var(--copper), var(--gold));
            font-size: 15px;
            box-shadow: 0 14px 26px rgba(201,138,46, .18);
        }

        .timeline {
            display: grid;
            gap: 12px;
        }

        .timeline-row {
            display: grid;
            grid-template-columns: 58px minmax(0, 1fr);
            gap: 14px;
            align-items: start;
            padding: 14px;
            border: 1px solid rgba(23,19,13, .12);
            border-radius: 22px;
            background: rgba(255,250,240, .62);
        }

        .dark .timeline-row {
            border-color: rgba(255,250,240, .14);
            background: rgba(255,250,240, .08);
        }

        .timeline-row h3 {
            margin: 0 0 6px;
        }

        .timeline-row p {
            margin: 0;
            color: var(--muted);
            line-height: 1.75;
        }

        .dark .timeline-row p {
            color: rgba(255,250,240, .68);
        }

        .route-pill {
            display: inline-flex;
            margin-top: 16px;
            color: var(--olive);
            font-weight: 800;
        }

        .plan-card {
            display: flex;
            flex-direction: column;
            min-height: 280px;
        }

        .price {
            margin: 12px 0;
            color: var(--copper);
            font-size: 36px;
            font-weight: 800;
            letter-spacing: -.04em;
        }

        .plan-card .button {
            margin-top: auto;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        label {
            display: grid;
            gap: 7px;
            color: var(--olive-dark);
            font-size: 13px;
            font-weight: 800;
        }

        input,
        select,
        textarea {
            width: 100%;
            border: 1px solid rgba(23,19,13, .16);
            border-radius: 16px;
            background: rgba(255,250,240, .78);
            color: var(--ink);
            font: inherit;
            padding: 13px 14px;
            outline: none;
            transition: border .2s ease, box-shadow .2s ease;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: rgba(23,19,13, .58);
            box-shadow: 0 0 0 4px rgba(23,19,13, .12);
        }

        textarea {
            min-height: 150px;
            resize: vertical;
        }

        .full {
            grid-column: 1 / -1;
        }

        .notice {
            padding: 14px 16px;
            margin-bottom: 14px;
            border: 1px solid rgba(23,19,13, .18);
            border-radius: 18px;
            color: var(--olive-dark);
            background: rgba(23,19,13, .72);
            font-weight: 800;
        }

        .error-list {
            padding: 14px 18px;
            margin: 0 0 14px;
            border: 1px solid rgba(201,138,46, .22);
            border-radius: 18px;
            color: #17130d;
            background: rgba(201,138,46, .11);
        }

        .footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-top: 26px;
            padding: 24px 4px 0;
            color: rgba(23,19,13, .62);
            font-size: 13px;
        }

        .muted {
            color: var(--muted);
        }

        @keyframes drift {
            from {
                transform: translate3d(0, 0, 0) rotate(0);
            }

            to {
                transform: translate3d(34px, -20px, 0) rotate(8deg);
            }
        }

        @media (max-width: 1120px) {
            .hero-grid,
            .grid-2 {
                grid-template-columns: 1fr;
            }

            .grid-3,
            .grid-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .topbar {
                align-items: stretch;
                border-radius: 28px;
                flex-direction: column;
            }

            .brand,
            .top-actions {
                justify-content: center;
            }
        }

        @media (max-width: 720px) {
            .shell {
                width: min(100% - 20px, 1320px);
                padding-top: 10px;
            }

            .hero {
                border-radius: 32px;
            }

            .hero-grid {
                padding: 28px;
            }

            .section {
                padding: 22px;
                border-radius: 28px;
            }

            .section-title {
                align-items: start;
                flex-direction: column;
            }

            .grid-3,
            .grid-4,
            .form-grid {
                grid-template-columns: 1fr;
            }

            .nav a,
            .button {
                width: 100%;
            }

            .top-actions,
            .hero-actions,
            .nav,
            .footer {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <a class="brand" href="{{ route('public.landing') }}">
                <span class="mark">Kb</span>
                <span>
                    <strong>KABEERI</strong>
                    <small>Public Bridge V11</small>
                </span>
            </a>

            <nav class="nav" aria-label="KABEERI public navigation">
                @foreach ($publicNav as $item)
                    <a class="{{ request()->routeIs($item['route']) ? 'active' : '' }}" href="{{ route($item['route']) }}">{{ $item['label'] }}</a>
                @endforeach
            </nav>

            <div class="top-actions">
                <a class="button" href="{{ route('system.command-center') }}">فحص النظام</a>
                <a class="button primary" href="/admin">Admin</a>
                @include('components.language-switcher', ['context' => 'visitor'])
            </div>
        </header>

        <main>
            <section class="hero">
                <div class="hero-grid">
                    <div>
                        <span class="eyebrow">KABEERI Public Bridge</span>
                        <h1>
                            @if ($page === 'landing')
                                منصة تبدأ كموقع واضح وتنتهي كنظام تشغيل للشركة.
                            @else
                                {{ $pageConfig['label'] }}
                            @endif
                        </h1>
                        <p>{{ $pageConfig['intent'] }} هذه الصفحة تفصل العرض العام عن أدوات الأدمن الداخلية، وتشرح للعميل أين يبدأ، ماذا يحصل عليه، ومتى ينتقل للخطوة التالية.</p>
                        <div class="hero-actions">
                            <a class="button primary" href="{{ route('public.contact') }}">احجز ديمو مبكر</a>
                            <a class="button copper" href="{{ route('public.audiences') }}">اختر مسارك</a>
                            <a class="button" href="{{ route('public.onboarding') }}">شاهد onboarding</a>
                        </div>
                    </div>

                    <aside class="signal-board" aria-label="KABEERI public positioning">
                        <div class="signal dark">
                            <strong>من WordPress Alternative إلى Company OS</strong>
                            <span>لا نرمي كل المنصة على العميل مرة واحدة. نبدأ بالاحتياج المفهوم، ثم نفتح التجارة والثقة والعمليات والسوق.</span>
                        </div>
                        <div class="signal">
                            <strong>Audience Selector</strong>
                            <span>صاحب مشروع، مؤسسة، مطور، مسوق، شريك، أو زائر Mall. كل جمهور له مسار واشتراك واستفادة واضحة.</span>
                        </div>
                        <div class="signal">
                            <strong>Admin split</strong>
                            <span>فحص النظام وtask tracker وقواعد النشر تبقى للأدمن. التفاصيل التسويقية ومسارات الاشتراك تبقى للجمهور.</span>
                        </div>
                    </aside>
                </div>
            </section>

            @if (in_array($page, ['landing', 'wordpress'], true))
                <section class="section dark">
                    <div class="section-title">
                        <div>
                            <span class="chip">Progressive Narrative</span>
                            <h2>نشرح كابيري كرحلة، مش كقائمة ضخمة من الموديولات.</h2>
                        </div>
                        <p>الزائر يفهم أولًا أنه يستطيع إطلاق موقع/تطبيق أعمال منظم، ثم يكتشف التجارة والثقة والعمليات وMall واقتصاد المطورين تدريجيًا.</p>
                    </div>

                    <div class="timeline">
                        @foreach ($storyLayers as $layer)
                            <article class="timeline-row">
                                <span class="step-badge">{{ $layer['step'] }}</span>
                                <div>
                                    <h3>{{ $layer['title'] }}</h3>
                                    <p>{{ $layer['text'] }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if (in_array($page, ['landing', 'audiences'], true))
                <section class="section" id="audiences">
                    <div class="section-title">
                        <div>
                            <span class="chip">Audience Selector</span>
                            <h2>كل جمهور يدخل من باب واضح.</h2>
                        </div>
                        <p>التقسيم هنا يمنع تشتيت العميل: كل مسار يشرح الألم، النتيجة، والصفحة التالية المناسبة.</p>
                    </div>

                    <div class="grid-3">
                        @foreach ($audiences as $key => $audience)
                            <article class="card">
                                <span class="chip">{{ $audience['label'] }}</span>
                                <h3>{{ $audience['headline'] }}</h3>
                                <p>{{ $audience['pain'] }}</p>
                                <p style="margin-top: 10px;"><strong>النتيجة:</strong> {{ $audience['outcome'] }}</p>
                                <a class="route-pill" href="{{ route($audience['route']) }}">افتح المسار</a>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($selectedAudience)
                <section class="section">
                    <div class="section-title">
                        <div>
                            <span class="chip">{{ $selectedAudience['label'] }}</span>
                            <h2>{{ $selectedAudience['headline'] }}</h2>
                        </div>
                        <p>{{ $selectedAudience['pain'] }}</p>
                    </div>

                    <div class="grid-2">
                        <article class="card">
                            <h3>القيمة التي يحصل عليها {{ $selectedAudience['label'] }}</h3>
                            <p>{{ $selectedAudience['outcome'] }}</p>
                            <a class="route-pill" href="{{ route('public.pricing') }}">شاهد الاشتراكات المناسبة</a>
                        </article>

                        <article class="card">
                            <h3>مسار العمل المقترح</h3>
                            <div class="timeline">
                                @foreach ($journeys[$audienceKey] as $index => $step)
                                    <div class="timeline-row">
                                        <span class="step-badge">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                        <div>
                                            <h3>{{ $step }}</h3>
                                            <p>خطوة عملية ضمن onboarding حتى لا تتحول المنصة إلى بحر غير مفهوم.</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </article>
                    </div>
                </section>
            @endif

            @if ($page === 'wordpress')
                <section class="section">
                    <div class="section-title">
                        <div>
                            <span class="chip">WordPress Alternative</span>
                            <h2>بديل أكثر تنظيمًا من فوضى الإضافات.</h2>
                        </div>
                        <p>الرسالة ليست مهاجمة WordPress، بل شرح أن كابيري يقدم مسارًا محكومًا: موقع، محتوى، تجارة، CRM، عمليات، وثقة في نفس النظام.</p>
                    </div>

                    <div class="grid-3">
                        <article class="card">
                            <h3>موقع ومحتوى</h3>
                            <p>صفحات، SEO، ميديا، نماذج، قوائم، وربط لاحق بثيمات تجارية مستقلة.</p>
                        </article>
                        <article class="card">
                            <h3>تجارة وعمليات</h3>
                            <p>منتجات، خدمات، عروض أسعار، فواتير، CRM، workflows وتقارير عند نضج الشركة.</p>
                        </article>
                        <article class="card">
                            <h3>ثقة وسوق</h3>
                            <p>Rabet للتحقق والهوية، وKabeeri Mall لاكتشاف الخدمات والمنتجات والقوائم العامة.</p>
                        </article>
                    </div>
                </section>
            @endif

            @if ($page === 'service_business')
                <section class="section">
                    <div class="section-title">
                        <div>
                            <span class="chip">Service Business Use Case</span>
                            <h2>شركة خدمات تبدأ بصفحة واضحة وتنتهي بتشغيل قابل للقياس.</h2>
                        </div>
                        <p>هذا المسار مناسب للاستشارات، الصيانة، التدريب، الخدمات الطبية، الخدمات المنزلية، وأي نشاط يحتاج طلبات وعروض وفواتير.</p>
                    </div>

                    <div class="grid-3">
                        @foreach (['صفحة خدمات', 'نموذج طلب', 'CRM Lead', 'عرض سعر', 'فاتورة', 'Mall Listing'] as $index => $serviceStep)
                            <article class="card">
                                <span class="step-badge">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                <h3>{{ $serviceStep }}</h3>
                                <p>العميل يرى خطوة مفهومة، والفريق يرى بيانات قابلة للمتابعة داخل النظام.</p>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if (in_array($page, ['landing', 'templates', 'service_business'], true))
                <section class="section">
                    <div class="section-title">
                        <div>
                            <span class="chip">Use Cases and Templates</span>
                            <h2>قوالب عمل تساعد العميل يبدأ بسرعة.</h2>
                        </div>
                        <p>القوالب ليست مجرد شكل UI. هي recipe جاهزة: صفحات، موديولات، بيانات أولية، وخطوات تشغيل.</p>
                    </div>

                    <div class="grid-3">
                        @foreach ($templates as $template)
                            <article class="card">
                                <h3>{{ $template['title'] }}</h3>
                                <p>{{ implode('، ', $template['includes']) }}</p>
                                <a class="route-pill" href="{{ route($template['route']) }}">استكشف القالب</a>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if (in_array($page, ['landing', 'onboarding', 'workspace_setup'], true))
                <section class="section dark">
                    <div class="section-title">
                        <div>
                            <span class="chip">Onboarding</span>
                            <h2>العميل لا يحتاج خريطة ضخمة، يحتاج أول خطوة صحيحة.</h2>
                        </div>
                        <p>مسار onboarding يترجم المنصة إلى قرارات صغيرة: الجمهور، workspace، نوع التطبيق، الثيم، الموديولات، الفريق، ثم الإطلاق.</p>
                    </div>

                    <div class="grid-3">
                        @foreach ($onboardingSteps as $step)
                            <article class="card">
                                <span class="step-badge">{{ $step['n'] }}</span>
                                <h3>{{ $step['title'] }}</h3>
                                <p>{{ $step['text'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($page === 'workspace_setup')
                <section class="section">
                    <div class="section-title">
                        <div>
                            <span class="chip">Workspace Setup Wizard</span>
                            <h2>Wizard foundation يمشي حسب الجمهور لا حسب رغبة النظام في الكلام.</h2>
                        </div>
                        <p>الحقول تظهر تدريجيًا. ERP وMarketplace وMall لا يظهروا إلا عندما يصبحوا منطقيين في رحلة المستخدم.</p>
                    </div>

                    <div class="grid-2">
                        <article class="card">
                            <h3>حقول التأسيس</h3>
                            <div class="grid-3">
                                @foreach ($wizard['fields'] as $field)
                                    <span class="chip">{{ $field }}</span>
                                @endforeach
                            </div>
                        </article>
                        <article class="card">
                            <h3>Progressive disclosure rules</h3>
                            <div class="timeline">
                                @foreach ($wizard['progressive_rules'] as $index => $rule)
                                    <div class="timeline-row">
                                        <span class="step-badge">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                        <p>{{ $rule }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </article>
                    </div>
                </section>
            @endif

            @if (in_array($page, ['landing', 'pricing'], true))
                <section class="section">
                    <div class="section-title">
                        <div>
                            <span class="chip">Pricing and Monetization</span>
                            <h2>الاشتراكات تتشرح كطبقات قيمة، مش كجدول أسعار فقط.</h2>
                        </div>
                        <p>V11 يوضح الاشتراك، الموديولات، التحقق، ظهور Mall، AI credits، ومشاركة أرباح Marketplace.</p>
                    </div>

                    <div class="grid-3">
                        @foreach ($plans as $plan)
                            <article class="card plan-card">
                                <span class="chip">{{ $plan['highlight'] }}</span>
                                <h3>{{ $plan['name'] }}</h3>
                                <div class="price">{{ $plan['price'] }}</div>
                                <p>{{ $plan['best_for'] }}</p>
                                <a class="button" href="{{ route('public.contact') }}">ناقش الخطة</a>
                            </article>
                        @endforeach
                    </div>

                    <div class="grid-3" style="margin-top: 14px;">
                        @foreach ($pricingLayers as $layer)
                            <article class="card">
                                <h3>{{ $layer['label'] }}</h3>
                                <p>{{ $layer['text'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($page === 'trust')
                <section class="section">
                    <div class="section-title">
                        <div>
                            <span class="chip">FAQ and Trust</span>
                            <h2>نرد على الأسئلة قبل ما تتحول لاعتراضات.</h2>
                        </div>
                        <p>الثقة هنا تشمل الفرق بين Marketplace وMall، ملكية البيانات، الثيمات، المطورين، وRabet.</p>
                    </div>

                    <div class="grid-2">
                        @foreach ($faq as $item)
                            <article class="card">
                                <h3>{{ $item['q'] }}</h3>
                                <p>{{ $item['a'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </section>

                <section class="section dark">
                    <div class="section-title">
                        <div>
                            <span class="chip">Release Gates</span>
                            <h2>حالة الجاهزية العامة لا تختلط بحالة الأدمن.</h2>
                        </div>
                        <p>هذه المؤشرات للشفافية الداخلية أثناء التطوير، وليست نسخة نهائية للعميل قبل اعتمادك.</p>
                    </div>

                    <div class="grid-3">
                        @foreach ($release['gates'] as $gate)
                            <article class="card">
                                <span class="chip">{{ $gate['ready'] ? 'Ready' : 'Pending' }}</span>
                                <h3>{{ $gate['label'] }}</h3>
                                <p>{{ $gate['key'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($page === 'contact')
                <section class="section">
                    <div class="section-title">
                        <div>
                            <span class="chip">Contact Sales</span>
                            <h2>طلب ديمو يتحول مباشرة إلى CRM Lead.</h2>
                        </div>
                        <p>هذا هو مسار early access/demo request في V11. الطلب لا يشغل أتمتة حساسة، فقط يلتقط بيانات مؤهلة للفريق.</p>
                    </div>

                    @if (session('status'))
                        <div class="notice">{{ session('status') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="error-list">
                            <strong>راجع البيانات التالية:</strong>
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('public.contact.store') }}">
                        @csrf
                        <div class="form-grid">
                            <label>
                                الجمهور المستهدف
                                <select name="audience" required>
                                    @foreach ($audiences as $key => $audience)
                                        <option value="{{ $key }}" @selected(old('audience', 'business') === $key)>{{ $audience['label'] }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label>
                                الخطة المهتم بها
                                <select name="plan_interest">
                                    <option value="">لسه محتاج توجيه</option>
                                    @foreach ($plans as $plan)
                                        <option value="{{ $plan['code'] }}" @selected(old('plan_interest') === $plan['code'])>{{ $plan['name'] }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label>
                                اسم الشركة
                                <input name="company_name" value="{{ old('company_name') }}" placeholder="مثال: Acme Services">
                            </label>

                            <label>
                                اسمك
                                <input name="name" value="{{ old('name') }}" required placeholder="الاسم الكامل">
                            </label>

                            <label>
                                البريد الإلكتروني
                                <input name="email" type="email" value="{{ old('email') }}" required placeholder="name@example.com">
                            </label>

                            <label>
                                الهاتف
                                <input name="phone" value="{{ old('phone') }}" placeholder="+20...">
                            </label>

                            <label class="full">
                                ماذا تريد أن تبني؟
                                <textarea name="message" required placeholder="اكتب احتياجك، جمهورك، وعدد الفريق أو العملاء المتوقعين.">{{ old('message') }}</textarea>
                            </label>
                        </div>

                        <div class="hero-actions">
                            <button class="button primary" type="submit">إرسال الطلب إلى CRM</button>
                            <a class="button" href="{{ route('public.pricing') }}">راجع الأسعار أولًا</a>
                        </div>
                    </form>
                </section>
            @endif

            <section class="section">
                <div class="section-title">
                    <div>
                        <span class="chip">Next.js Runtime Plan</span>
                        <h2>Blade هنا جسر V11، وليس وجهة الثيمات النهائية.</h2>
                    </div>
                    <p>{{ $nextRuntime['decision'] }}</p>
                </div>

                <div class="grid-2">
                    <article class="card">
                        <h3>API contracts المطلوبة</h3>
                        <div class="grid-3">
                            @foreach ($nextRuntime['contract_needs'] as $contract)
                                <span class="chip">{{ $contract }}</span>
                            @endforeach
                        </div>
                    </article>

                    <article class="card">
                        <h3>Migration path</h3>
                        <div class="timeline">
                            @foreach ($nextRuntime['migration_steps'] as $index => $step)
                                <div class="timeline-row">
                                    <span class="step-badge">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                    <p>{{ $step }}</p>
                                </div>
                            @endforeach
                        </div>
                    </article>
                </div>
            </section>
        </main>

        <footer class="footer">
            <span>KABEERI V11 Public Marketing UX</span>
            <span>{{ $pageConfig['uri'] }} · {{ $pageConfig['label'] }} · {{ $pageConfig['intent'] }}</span>
        </footer>
    </div>
</body>
</html>
