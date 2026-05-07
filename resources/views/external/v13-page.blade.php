@php
    $page = $data['page'];
    $pageConfig = $data['page_config'];
    $mallSections = $data['mall_sections'];
    $filters = $data['search_filters'];
    $trustBadges = $data['trust_badges'];
    $claimFlow = $data['claim_report_flow'];
    $customerSteps = $data['customer_steps'];
    $partnerPaths = $data['partner_paths'];
    $referralMetrics = $data['referral_metrics'];
    $campaignResources = $data['campaign_resources'];
    $network = $data['network'];
    $legalSteps = $data['legal_verification_steps'];
    $release = $data['release'];
    $brief = fn (?string $text, int $words = 12): string => \Illuminate\Support\Str::words(
        \Illuminate\Support\Str::squish((string) $text),
        $words,
        ''
    );

    $nav = [
        ['label' => 'Mall', 'route' => 'mall.index', 'icon' => 'mall'],
        ['label' => 'Search', 'route' => 'mall.search', 'icon' => 'search'],
        ['label' => 'Customer', 'route' => 'customer.dashboard', 'icon' => 'apps'],
        ['label' => 'Partners', 'route' => 'partners.landing', 'icon' => 'partner'],
        ['label' => 'Referrals', 'route' => 'partners.referrals', 'icon' => 'rocket'],
        ['label' => 'Network', 'route' => 'network.academy', 'icon' => 'book'],
        ['label' => 'Trust', 'route' => 'mall.trust', 'icon' => 'trust'],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Localization\KabeeriLocale::direction() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageConfig['label'] }} | {{ __('kabeeri.brand.name') }} V13 External UX</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=almarai:400,700,800|ibm-plex-sans-arabic:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --ink: #17130d;
            --soft: #17130d;
            --paper: #fffaf0;
            --sage: #17130d;
            --forest: #17130d;
            --sky: #17130d;
            --clay: #c98a2e;
            --wheat: #c98a2e;
            --line: rgba(23,19,13, .13);
            --white-line: rgba(255,250,240, .18);
            --shadow: 0 26px 84px rgba(23,19,13, .16);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            color: var(--ink);
            background:
                radial-gradient(circle at 86% 14%, rgba(23,19,13, .74), transparent 27rem),
                radial-gradient(circle at 7% 20%, rgba(201,138,46, .36), transparent 24rem),
                linear-gradient(135deg, #fffaf0 0%, #fffaf0 48%, #17130d 100%);
            font-family: "IBM Plex Sans Arabic", "Almarai", sans-serif;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background-image:
                radial-gradient(rgba(23,19,13, .06) 1px, transparent 1px),
                linear-gradient(120deg, rgba(23,19,13, .035), transparent 38%);
            background-size: 28px 28px, 100% 100%;
            mask-image: linear-gradient(to bottom, rgba(0,0,0, .86), transparent 80%);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .shell {
            width: min(100% - 32px, 1320px);
            margin: 0 auto;
            padding: 18px 0 72px;
        }

        .topbar {
            position: sticky;
            top: 14px;
            z-index: 20;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 14px;
            border: 1px solid rgba(255,250,240, .72);
            border-radius: 999px;
            background: rgba(255,250,240, .74);
            box-shadow: 0 18px 64px rgba(23,19,13, .12);
            backdrop-filter: blur(22px);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 220px;
        }

        .brand-mark {
            display: grid;
            place-items: center;
            width: 48px;
            height: 48px;
            border-radius: 17px;
            color: var(--paper);
            background: linear-gradient(135deg, var(--forest), var(--sage));
            font-weight: 900;
            letter-spacing: -.1em;
        }

        .brand small {
            display: block;
            margin-top: 2px;
            color: var(--soft);
            font-size: 12px;
        }

        .nav,
        .actions,
        .chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .nav {
            justify-content: center;
        }

        .nav a,
        .chip,
        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            font-weight: 900;
        }

        .nav a {
            padding: 10px 13px;
            color: rgba(23,19,13, .72);
            font-size: 13px;
        }

        .nav a:hover {
            background: rgba(255,250,240, .9);
            color: var(--forest);
        }

        .nav a.active {
            background: #17130d;
            color: #fffaf0;
        }

        .button {
            min-height: 44px;
            padding: 11px 18px;
            border: 1px solid var(--line);
            background: rgba(255,250,240, .7);
            color: var(--forest);
            transition: .2s ease;
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 34px rgba(23,19,13, .13);
        }

        .button.primary {
            border-color: transparent;
            color: var(--paper);
            background: linear-gradient(135deg, var(--forest), #17130d);
        }

        .button.clay {
            border-color: var(--line);
            color: var(--forest);
            background: rgba(255,250,240, .72);
        }

        .hero {
            overflow: hidden;
            position: relative;
            margin-top: 22px;
            border: 1px solid rgba(255,250,240, .76);
            border-radius: 46px;
            background:
                radial-gradient(circle at 18% 20%, rgba(201,138,46, .2), transparent 24rem),
                linear-gradient(145deg, rgba(255,250,240, .9), rgba(255,250,240, .52));
            box-shadow: var(--shadow);
        }

        .hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.08fr) minmax(330px, .92fr);
            gap: 24px;
            padding: 34px;
        }

        h1,
        h2,
        h3,
        .brand strong {
            letter-spacing: -.045em;
        }

        h1 {
            max-width: 820px;
            margin: 18px 0 16px;
            font-size: clamp(30px, 4.8vw, 54px);
            line-height: .98;
        }

        .eyebrow,
        .chip {
            min-height: 30px;
            padding: 6px 11px;
            border: 1px solid rgba(23,19,13, .18);
            color: var(--forest);
            background: rgba(255,250,240, .76);
            font-size: 12px;
        }

        .hero p,
        .section-title p,
        .card p,
        .timeline p,
        .card li {
            line-height: 1.82;
        }

        .hero p {
            max-width: 760px;
            margin: 0;
            color: rgba(23,19,13, .72);
            font-size: clamp(14px, 1.3vw, 17px);
        }

        .signal-board {
            display: grid;
            gap: 12px;
        }

        .signal,
        .card,
        .section {
            border: 1px solid var(--line);
            background: rgba(255,250,240, .72);
            box-shadow: 0 14px 40px rgba(23,19,13, .08);
        }

        .signal,
        .card {
            padding: 22px;
            border-radius: 26px;
        }

        .signal.dark,
        .section.dark {
            color: var(--paper);
            border-color: var(--white-line);
            background:
                radial-gradient(circle at top left, rgba(201,138,46, .18), transparent 24rem),
                linear-gradient(135deg, #17130d, #17130d);
        }

        .signal span {
            display: block;
            margin-top: 7px;
            opacity: .72;
            line-height: 1.72;
        }

        .section {
            margin-top: 22px;
            padding: 30px;
            border-radius: 34px;
        }

        .section-title {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 18px;
        }

        .section-title h2 {
            margin: 8px 0 0;
            font-size: clamp(22px, 2.8vw, 32px);
            line-height: 1.08;
        }

        .section-title p,
        .card p {
            color: var(--soft);
        }

        .dark .section-title p,
        .dark .card p,
        .dark .timeline p {
            color: rgba(255,250,240, .72);
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

        .card h3 {
            margin: 11px 0 9px;
            font-size: 22px;
        }

        .metric {
            margin-top: 12px;
            color: var(--forest);
            font-size: 24px;
            font-weight: 900;
        }

        .timeline {
            display: grid;
            gap: 12px;
        }

        .timeline-row {
            display: grid;
            grid-template-columns: 54px minmax(0, 1fr);
            gap: 13px;
            align-items: start;
            padding: 14px;
            border: 1px solid var(--line);
            border-radius: 22px;
            background: rgba(255,250,240, .62);
        }

        .dark .timeline-row {
            border-color: var(--white-line);
            background: rgba(255,250,240, .08);
        }

        .badge {
            display: grid;
            place-items: center;
            width: 44px;
            height: 44px;
            border-radius: 16px;
            color: var(--paper);
            background: linear-gradient(135deg, var(--clay), var(--wheat));
            font-weight: 900;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-top: 26px;
            padding: 24px 4px 0;
            color: rgba(23,19,13, .62);
            font-size: 13px;
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
                border-radius: 30px;
                align-items: stretch;
                flex-direction: column;
            }

            .brand,
            .actions {
                justify-content: center;
            }
        }

        @media (max-width: 720px) {
            .shell {
                width: min(100% - 20px, 1320px);
            }

            .hero-grid,
            .section {
                padding: 24px;
            }

            .section-title,
            .footer,
            .actions {
                align-items: stretch;
                flex-direction: column;
            }

            .grid-3,
            .grid-4 {
                grid-template-columns: 1fr;
            }

            .nav a,
            .button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <a class="brand" href="{{ route('mall.index') }}">
                <span class="brand-mark">Kb</span>
                <span>
                    <strong>{{ __('kabeeri.brand.name') }} External</strong>
                    <small>V13 Mall, Customer, Partner, Network</small>
                </span>
            </a>

            <nav class="nav" aria-label="V13 external navigation">
                @foreach ($nav as $item)
                    <a class="{{ request()->routeIs($item['route']) ? 'active' : '' }}" href="{{ route($item['route']) }}"><x-kabeeri-icon name="{{ $item['icon'] }}" />{{ $item['label'] }}</a>
                @endforeach
            </nav>

            <div class="actions">
                <a class="button" href="{{ route('marketplace.home') }}"><x-kabeeri-icon name="mall" />السوق</a>
                <a class="button primary" href="{{ route('public.landing') }}"><x-kabeeri-icon name="home" />المنصة</a>
                @include('components.language-switcher', ['context' => 'visitor'])
            </div>
        </header>

        <main>
            <section class="hero">
                <div class="hero-grid">
                    <div>
                        <span class="eyebrow">{{ __('kabeeri.brand.name') }} V13 External Portal</span>
                        <h1>{{ $pageConfig['label'] }}</h1>
                        <p>{{ $brief($pageConfig['intent'], 10) }} اكتشاف واضح، ثم مسار مناسب.</p>
                        <div class="actions" style="margin-top: 24px;">
                            <a class="button primary" href="{{ route('mall.search') }}">ابدأ البحث في Mall</a>
                            <a class="button clay" href="{{ route('customer.dashboard') }}"><x-kabeeri-icon name="apps" />افتح لوحتك</a>
                            <a class="button" href="{{ route('partners.landing') }}">مسار الشركاء</a>
                        </div>
                    </div>

                    <aside class="signal-board">
                        <div class="signal dark">
                            <strong>Mall is public discovery</strong>
                            <span>الزائر يرى شركات، منتجات، خدمات، كورسات، مواهب، وسفر. لا يرى install permissions أو package signing هنا.</span>
                        </div>
                        <div class="signal">
                            <strong>Trust before conversion</strong>
                            <span>كل listing يحتاج سياق: consent، verification، moderation، report، claim، وreputation.</span>
                        </div>
                        <div class="signal">
                            <strong>Partner measurable path</strong>
                            <span>الشريك يرى storefront، referrals، lead handoff، campaign resources، وcommission placeholder بدل وعود غائمة.</span>
                        </div>
                    </aside>
                </div>
            </section>

            @if (in_array($page, ['mall_search', 'mall_trust', 'mall_claim_report'], true))
                <section class="section">
                    <div class="section-title">
                        <div>
                            <span class="chip">{{ __('kabeeri.brand.name') }} Mall vs {{ __('kabeeri.brand.name') }} Marketplace Public Copy Separation</span>
                            <h2>Mall للعثور على أعمال حقيقية، Marketplace لتثبيت إضافات داخلية.</h2>
                        </div>
                        <p>هذه الرسالة لازم تظل واضحة في كل صفحة عامة حتى لا يخلط الزائر بين شراء خدمة عامة وتثبيت plugin بصلاحيات حساسة.</p>
                    </div>

                    <div class="grid-3">
                        @foreach ($mallSections as $section)
                            <article class="card">
                                <span class="chip">{{ $section['label'] }}</span>
                                <h3>{{ $section['label'] }}</h3>
                                <p>{{ $section['trust'] }}</p>
                                <div class="metric">{{ $section['count'] }}</div>
                                <p>{{ $section['count'] }} published</p>
                                <div class="actions" style="margin-top: 14px;">
                                    <a class="button" href="{{ route($section['route']) }}">افتح القسم</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($page === 'mall_search')
                <section class="section dark">
                    <div class="section-title">
                        <div>
                            <span class="chip">Mall Search and Filter UX</span>
                            <h2>فلترة بالنية والثقة، مش category فقط.</h2>
                        </div>
                        <p>الزائر قد يريد شراء، طلب خدمة، تعلّم، توظيف موهبة، claim listing، أو report issue. لذلك البحث يعرض نية المستخدم والثقة مع النوع.</p>
                    </div>

                    <div class="grid-4">
                        @foreach ($filters as $label => $items)
                            <article class="card">
                                <span class="chip">{{ $label }}</span>
                                <h3>{{ str($label)->replace('_', ' ')->title() }}</h3>
                                <div class="chips">
                                    @foreach ($items as $item)
                                        <span class="chip">{{ $item }}</span>
                                    @endforeach
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($page === 'mall_trust')
                <section class="section dark">
                    <div class="section-title">
                        <div>
                            <span class="chip">Mall Verification Trust Badge and Moderation UX</span>
                            <h2>الثقة ليست badge فقط، دي رحلة واضحة.</h2>
                        </div>
                        <p>V13 يعرض معنى كل badge، ومتى تظهر، وكيف يمكن الإبلاغ أو المراجعة أو سحب النشر.</p>
                    </div>

                    <div class="grid-4">
                        @foreach ($trustBadges as $badge)
                            <article class="card">
                                <span class="chip">Trust Badge</span>
                                <h3>{{ $badge['label'] }}</h3>
                                <p>{{ $badge['meaning'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($page === 'mall_claim_report')
                <section class="section">
                    <div class="section-title">
                        <div>
                            <span class="chip">Mall Listing Claim Submit and Report Flow</span>
                            <h2>Claim وReport لهم مسار واحد مفهوم وآمن.</h2>
                        </div>
                        <p>الهدف أن صاحب العمل يربط listing بالworkspace الصحيح، والزائر يقدر يبلغ عن مشكلة بدون فتح فوضى عامة.</p>
                    </div>

                    <div class="timeline">
                        @foreach ($claimFlow as $step)
                            <div class="timeline-row">
                                <span class="badge">{{ $step['step'] }}</span>
                                <div>
                                    <h3>{{ $step['title'] }}</h3>
                                    <p>{{ $brief($step['text'], 12) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if (str_starts_with($page, 'customer_'))
                <section class="section dark">
                    <div class="section-title">
                        <div>
                            <span class="chip">لوحتك</span>
                            <h2>العميل يبدأ من checklist تشغيل، مش من لوحة ضخمة.</h2>
                        </div>
                        <p>لوحتك تجمع التطبيق، الثيم، الإضافات، والنشر في مكان واحد.</p>
                    </div>

                    <div class="grid-3">
                        @foreach ($customerSteps as $step)
                            <article class="card">
                                <span class="badge">{{ $step['n'] }}</span>
                                <h3>{{ $step['title'] }}</h3>
                                <p>{{ $brief($step['text'], 12) }}</p>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if (in_array($page, ['customer_theme_plugins', 'customer_quick_setup'], true))
                <section class="section">
                    <div class="section-title">
                        <div>
                            <span class="chip">Customer Theme and Plugin Selection UI</span>
                            <h2>اختيار الثيم والبلجن عند العميل يجب أن يشرح المخاطر ببساطة.</h2>
                        </div>
                        <p>نستفيد من V12 Marketplace، لكن customer UI لا يعرض تفاصيل مطورين زائدة. يعرض توافق، صلاحيات، plan، rollback، ودعم.</p>
                    </div>

                    <div class="grid-3">
                        @foreach (['Choose a safe theme', 'Review plugin permissions', 'Create products/services', 'Prepare Mall consent', 'Launch and monitor'] as $item)
                            <article class="card">
                                <span class="chip">Quick Setup</span>
                                <h3>{{ $item }}</h3>
                                <p>خطوة مبسطة للعميل مع next action واضح داخل workspace.</p>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if (str_starts_with($page, 'partner_') || in_array($page, ['agency_profile', 'referral_dashboard', 'campaign_resources', 'legal_verification'], true))
                <section class="section">
                    <div class="section-title">
                        <div>
                            <span class="chip">Partner, Agency, Marketer Paths</span>
                            <h2>كل شريك يرى فائدته ومسؤولياته ومسار lead handoff.</h2>
                        </div>
                        <p>V13 يحول الشراكة من صفحة كلام إلى onboarding، storefront، referrals، campaigns، verification support، وcommission placeholders.</p>
                    </div>

                    <div class="grid-4">
                        @foreach ($partnerPaths as $path)
                            <article class="card">
                                <span class="chip">{{ $path['label'] }}</span>
                                <h3>{{ $path['label'] }}</h3>
                                <p>{{ $brief($path['benefit'], 10) }}</p>
                                <div class="actions" style="margin-top: 14px;">
                                    <a class="button" href="{{ route($path['route']) }}">افتح المسار</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($page === 'referral_dashboard')
                <section class="section dark">
                    <div class="section-title">
                        <div>
                            <span class="chip">Marketer Referral Dashboard and Commission Placeholder UI</span>
                            <h2>العمولة placeholder حتى يكتمل billing settlement.</h2>
                        </div>
                        <p>نعرض pipeline وqualified leads وhandoff، ولا نوعد payout نهائي قبل سياسات الفوترة والضرائب.</p>
                    </div>

                    <div class="grid-4">
                        @foreach ($referralMetrics as $metric)
                            <article class="card">
                                <span class="chip">{{ $metric['label'] }}</span>
                                <h3>{{ $metric['value'] }}</h3>
                                <p>{{ $metric['label'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($page === 'campaign_resources')
                <section class="section dark">
                    <div class="section-title">
                        <div>
                            <span class="chip">Marketer Campaign Resources UI</span>
                            <h2>المسوق يحتاج assets وUTMs وhandoff script.</h2>
                        </div>
                        <p>كل campaign resource يرتبط بمسار جمهور واضح من V11 أو listing/claim flow من V13.</p>
                    </div>

                    <div class="chips">
                        @foreach ($campaignResources as $resource)
                            <span class="chip">{{ $resource }}</span>
                        @endforeach
                    </div>
                </section>
            @endif

            @if (in_array($page, ['network_academy', 'talent_path'], true))
                <section class="section">
                    <div class="section-title">
                        <div>
                            <span class="chip">{{ $page === 'talent_path' ? 'Public Talent Console' : 'Work Network and Academy Portal Foundation' }}</span>
                            <h2>الموهبة العامة مرتبطة بمهارات وbadges وموافقة نشر.</h2>
                        </div>
                        <p>Talent ليس مجرد profile. هو professional path فيه public consent، skills evidence، badges، moderation، وreport flow.</p>
                    </div>

                    <div class="grid-3">
                        <article class="card">
                            <span class="chip">Academy Badges</span>
                            <h3>Badges</h3>
                            <div class="chips">
                                @foreach ($network['academy_badges'] as $badge)
                                    <span class="chip">{{ $badge }}</span>
                                @endforeach
                            </div>
                        </article>
                        <article class="card">
                            <span class="chip">Work Paths</span>
                            <h3>Paths</h3>
                            <div class="chips">
                                @foreach ($network['work_paths'] as $path)
                                    <span class="chip">{{ $path }}</span>
                                @endforeach
                            </div>
                        </article>
                        <article class="card">
                            <span class="chip">Quality Rules</span>
                            <h3>Trust rules</h3>
                            <div class="chips">
                                @foreach ($network['quality_rules'] as $rule)
                                    <span class="chip">{{ $rule }}</span>
                                @endforeach
                            </div>
                        </article>
                    </div>
                </section>
            @endif

            @if ($page === 'legal_verification')
                <section class="section dark">
                    <div class="section-title">
                        <div>
                            <span class="chip">Legal Partner Verification Journey UI</span>
                            <h2>Legal partner يساعد في الثقة بدون كشف بيانات حساسة للعلن.</h2>
                        </div>
                        <p>الرحلة تربط request intake، document review، platform decision، وpublic trust record.</p>
                    </div>

                    <div class="timeline">
                        @foreach ($legalSteps as $step)
                            <div class="timeline-row">
                                <span class="badge">{{ $step['step'] }}</span>
                                <div>
                                    <h3>{{ $step['title'] }}</h3>
                                    <p>{{ $brief($step['text'], 12) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($page === 'mall_trust')
                <section class="section">
                    <div class="section-title">
                        <div>
                            <span class="chip">Release Gates</span>
                            <h2>V13 جاهز عندما يظل V9-V12 مستقرين وتثبت صفحات Mall والبوابات الخارجية.</h2>
                        </div>
                        <p>هذه مؤشرات داخلية للأدمن وليست وعدًا تجاريًا نهائيًا للعميل.</p>
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
        </main>

        <footer class="footer">
            <span>{{ __('kabeeri.brand.name') }} V13 External UX</span>
            <span>{{ $pageConfig['uri'] }} · {{ $pageConfig['label'] }}</span>
        </footer>
    </div>
</body>
</html>
