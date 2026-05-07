@php
    $page = $data['page'];
    $pageConfig = $data['page_config'];
    $pages = $data['pages'];
    $lanes = $data['lanes'];
    $taxonomy = $data['taxonomy'];
    $themes = $data['themes'];
    $plugins = $data['plugins'];
    $bundles = $data['bundles'];
    $themeRecipes = $data['theme_recipes'];
    $onboarding = $data['developer_onboarding'];
    $statuses = $data['lifecycle_statuses'];
    $manifestFields = $data['manifest_fields'];
    $permissions = $data['permissions'];
    $compatibilityAxes = $data['compatibility_axes'];
    $qa = $data['qa_checklists'];
    $componentLibrary = $data['component_library'];
    $reviewFlow = $data['review_flow'];
    $licensing = $data['licensing'];
    $inventory = $data['inventory'];
    $release = $data['release'];
    $selectedTheme = $data['selected_theme'];
    $selectedPlugin = $data['selected_plugin'];

    $nav = [
        ['label' => 'Marketplace', 'route' => 'marketplace.home'],
        ['label' => 'Themes', 'route' => 'marketplace.themes.index'],
        ['label' => 'Plugins', 'route' => 'marketplace.plugins.index'],
        ['label' => 'Developers', 'route' => 'developers.portal'],
        ['label' => 'QA', 'route' => 'developers.qa'],
        ['label' => 'Revenue', 'route' => 'marketplace.licensing'],
        ['label' => 'Governance', 'route' => 'marketplace.governance'],
    ];

    $developerPages = [
        'developer_landing',
        'developer_onboarding',
        'theme_builder_docs',
        'plugin_manifest_docs',
        'connector_sdk_docs',
        'submission_checklist',
        'developer_listings',
        'developer_sales',
        'developer_profile',
        'qa_center',
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Localization\KabeeriLocale::direction() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageConfig['label'] }} | KABEERI V12 Marketplace Studio</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=almarai:400,700,800|ibm-plex-sans-arabic:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --ink: #17130d;
            --soft: #17130d;
            --paper: #fffaf0;
            --sand: #fffaf0;
            --charcoal: #17130d;
            --pine: #17130d;
            --teal: #17130d;
            --amber: #c98a2e;
            --rust: #17130d;
            --cream-line: rgba(255,250,240, .18);
            --ink-line: rgba(23,19,13, .13);
            --shadow: 0 28px 90px rgba(23,19,13, .18);
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            color: var(--ink);
            background:
                radial-gradient(circle at 82% 8%, rgba(23,19,13, .28), transparent 26rem),
                radial-gradient(circle at 8% 24%, rgba(201,138,46, .32), transparent 28rem),
                linear-gradient(130deg, #fffaf0 0%, #fffaf0 44%, #17130d 100%);
            font-family: "IBM Plex Sans Arabic", "Almarai", sans-serif;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background:
                linear-gradient(120deg, transparent 0 48%, rgba(23,19,13, .035) 48% 50%, transparent 50% 100%),
                linear-gradient(60deg, rgba(23,19,13, .035) 1px, transparent 1px);
            background-size: 76px 76px, 34px 34px;
            mask-image: linear-gradient(to bottom, rgba(0,0,0, .72), transparent 78%);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .shell {
            width: min(100% - 32px, 1340px);
            margin: 0 auto;
            padding: 18px 0 72px;
        }

        .topbar {
            position: sticky;
            top: 14px;
            z-index: 30;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 14px;
            border: 1px solid rgba(255,250,240, .68);
            border-radius: 30px;
            background: rgba(255,250,240, .74);
            box-shadow: 0 20px 70px rgba(23,19,13, .13);
            backdrop-filter: blur(22px);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 230px;
        }

        .brand-mark {
            display: grid;
            place-items: center;
            width: 50px;
            height: 50px;
            border-radius: 18px;
            color: #fffaf0;
            background:
                radial-gradient(circle at 30% 20%, rgba(255,250,240, .44), transparent 32%),
                linear-gradient(135deg, var(--charcoal), var(--teal));
            font-weight: 900;
            letter-spacing: -.1em;
        }

        .brand small {
            display: block;
            margin-top: 2px;
            color: var(--soft);
            font-size: 12px;
        }

        .nav {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 7px;
        }

        .nav a {
            padding: 10px 13px;
            border-radius: 999px;
            color: rgba(23,19,13, .7);
            font-size: 13px;
            font-weight: 800;
        }

        .nav a:hover,
        .nav a.active {
            color: var(--charcoal);
            background: rgba(23,19,13, .12);
        }

        .top-actions {
            display: flex;
            gap: 8px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 44px;
            padding: 11px 17px;
            border: 1px solid rgba(23,19,13, .14);
            border-radius: 999px;
            background: rgba(255,250,240, .68);
            color: var(--charcoal);
            font-weight: 900;
            transition: .2s ease;
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 34px rgba(23,19,13, .12);
        }

        .button.primary {
            color: #fffaf0;
            border-color: transparent;
            background: linear-gradient(135deg, var(--charcoal), var(--pine), var(--teal));
        }

        .button.amber {
            color: #fffaf0;
            border-color: transparent;
            background: linear-gradient(135deg, var(--rust), var(--amber));
        }

        .hero {
            position: relative;
            overflow: hidden;
            margin-top: 22px;
            border: 1px solid rgba(255,250,240, .72);
            border-radius: 44px;
            background:
                radial-gradient(circle at top right, rgba(201,138,46, .22), transparent 24rem),
                linear-gradient(140deg, rgba(255,250,240, .9), rgba(255,250,240, .48));
            box-shadow: var(--shadow);
        }

        .hero::after {
            content: "";
            position: absolute;
            inset-inline-start: -8rem;
            bottom: -10rem;
            width: 28rem;
            height: 28rem;
            border-radius: 999px;
            background: conic-gradient(from 130deg, rgba(23,19,13, .24), rgba(201,138,46, .22), transparent, rgba(23,19,13, .24));
            filter: blur(2px);
            animation: turn 12s linear infinite;
        }

        .hero-grid {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: minmax(0, 1.06fr) minmax(330px, .94fr);
            gap: 24px;
            padding: 52px;
        }

        .eyebrow,
        .chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 30px;
            padding: 6px 11px;
            border: 1px solid rgba(23,19,13, .22);
            border-radius: 999px;
            color: var(--pine);
            background: rgba(23,19,13, .11);
            font-size: 12px;
            font-weight: 900;
        }

        .eyebrow::before {
            content: "";
            width: 9px;
            height: 9px;
            border-radius: 999px;
            background: var(--amber);
            box-shadow: 0 0 0 7px rgba(201,138,46, .17);
        }

        h1,
        h2,
        h3,
        .brand strong {
            letter-spacing: -.045em;
        }

        h1 {
            max-width: 820px;
            margin: 20px 0 15px;
            color: #17130d;
            font-size: clamp(42px, 7vw, 84px);
            line-height: .98;
        }

        .hero p,
        .section-title p,
        .card p,
        .timeline p,
        .card li {
            line-height: 1.82;
        }

        .hero p {
            max-width: 780px;
            margin: 0;
            color: rgba(23,19,13, .72);
            font-size: clamp(17px, 2vw, 22px);
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 25px;
        }

        .studio-board {
            display: grid;
            gap: 12px;
        }

        .signal {
            padding: 20px;
            border: 1px solid var(--ink-line);
            border-radius: 26px;
            background: rgba(255,250,240, .72);
            box-shadow: 0 16px 42px rgba(23,19,13, .08);
        }

        .signal.dark {
            color: #fffaf0;
            border-color: rgba(255,250,240, .14);
            background:
                radial-gradient(circle at 20% 16%, rgba(201,138,46, .22), transparent 18rem),
                linear-gradient(135deg, var(--charcoal), #17130d);
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
            border: 1px solid rgba(255,250,240, .66);
            border-radius: 34px;
            background: rgba(255,250,240, .66);
            box-shadow: 0 16px 54px rgba(23,19,13, .08);
        }

        .section.dark {
            color: #fffaf0;
            border-color: rgba(255,250,240, .12);
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
            margin: 8px 0 0;
            font-size: clamp(28px, 4vw, 46px);
            line-height: 1.08;
        }

        .section-title p {
            max-width: 640px;
            margin: 0;
            color: var(--soft);
        }

        .dark .section-title p,
        .dark .muted,
        .dark .card p,
        .dark .card li,
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

        .card {
            position: relative;
            overflow: hidden;
            padding: 22px;
            border: 1px solid var(--ink-line);
            border-radius: 26px;
            background: rgba(255,250,240, .72);
            box-shadow: 0 14px 38px rgba(23,19,13, .07);
        }

        .dark .card {
            border-color: var(--cream-line);
            background: rgba(255,250,240, .08);
            box-shadow: none;
        }

        .card h3 {
            margin: 11px 0 10px;
            font-size: 22px;
        }

        .card p,
        .card li {
            margin: 0;
            color: rgba(23,19,13, .72);
        }

        .metric {
            margin-top: 12px;
            color: var(--teal);
            font-size: 38px;
            font-weight: 900;
            letter-spacing: -.05em;
        }

        .mini-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 14px;
        }

        .risk-low {
            color: #17130d;
            background: rgba(23,19,13, .12);
            border-color: rgba(23,19,13, .18);
        }

        .risk-medium {
            color: #c98a2e;
            background: rgba(201,138,46, .16);
            border-color: rgba(201,138,46, .28);
        }

        .risk-high,
        .risk-critical {
            color: #17130d;
            background: rgba(23,19,13, .13);
            border-color: rgba(23,19,13, .24);
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
            border: 1px solid var(--ink-line);
            border-radius: 22px;
            background: rgba(255,250,240, .62);
        }

        .dark .timeline-row {
            border-color: var(--cream-line);
            background: rgba(255,250,240, .07);
        }

        .badge {
            display: grid;
            place-items: center;
            width: 44px;
            height: 44px;
            border-radius: 16px;
            color: #fffaf0;
            background: linear-gradient(135deg, var(--rust), var(--amber));
            font-weight: 900;
        }

        .timeline h3 {
            margin: 0 0 4px;
        }

        .muted {
            color: var(--soft);
        }

        .table-card {
            overflow: hidden;
            border: 1px solid var(--ink-line);
            border-radius: 24px;
        }

        .table-row {
            display: grid;
            grid-template-columns: minmax(120px, .7fr) minmax(0, 1fr) minmax(150px, .8fr);
            gap: 12px;
            padding: 14px 16px;
            background: rgba(255,250,240, .58);
            border-bottom: 1px solid var(--ink-line);
        }

        .table-row:last-child {
            border-bottom: 0;
        }

        .table-row strong {
            color: var(--charcoal);
        }

        .footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 26px;
            padding: 24px 4px 0;
            color: rgba(23,19,13, .6);
            font-size: 13px;
        }

        @keyframes turn {
            to {
                transform: rotate(360deg);
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
                flex-direction: column;
            }

            .brand,
            .top-actions {
                justify-content: center;
            }
        }

        @media (max-width: 720px) {
            .shell {
                width: min(100% - 20px, 1340px);
                padding-top: 10px;
            }

            .hero {
                border-radius: 32px;
            }

            .hero-grid,
            .section {
                padding: 24px;
            }

            .section-title,
            .top-actions,
            .hero-actions,
            .footer {
                align-items: stretch;
                flex-direction: column;
            }

            .grid-3,
            .grid-4,
            .table-row {
                grid-template-columns: 1fr;
            }

            .button,
            .nav a {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <a class="brand" href="{{ route('marketplace.home') }}">
                <span class="brand-mark">Kb</span>
                <span>
                    <strong>KABEERI Marketplace</strong>
                    <small>V12 Themes, Plugins, Developer Economy</small>
                </span>
            </a>

            <nav class="nav" aria-label="KABEERI V12 navigation">
                @foreach ($nav as $item)
                    <a class="{{ request()->routeIs($item['route']) ? 'active' : '' }}" href="{{ route($item['route']) }}">{{ $item['label'] }}</a>
                @endforeach
            </nav>

            <div class="top-actions">
                <a class="button" href="{{ route('public.landing') }}">Public V11</a>
                <a class="button primary" href="/admin">Admin</a>
                @include('components.language-switcher', ['context' => 'visitor'])
            </div>
        </header>

        <main>
            <section class="hero">
                <div class="hero-grid">
                    <div>
                        <span class="eyebrow">KABEERI V12 Marketplace Studio</span>
                        <h1>{{ in_array($page, $developerPages, true) ? 'بوابة المطورين والمبدعين لبيع الثيمات والبلجنز.' : $pageConfig['label'] }}</h1>
                        <p>{{ $pageConfig['intent'] }} V12 تفصل Marketplace الداخلي عن Kabeeri Mall، وتعرض قبل أي تثبيت: permissions، compatibility، signing، license، rollback، والدعم.</p>
                        <div class="hero-actions">
                            <a class="button primary" href="{{ route('marketplace.themes.index') }}">تصفح Theme Catalog</a>
                            <a class="button amber" href="{{ route('marketplace.plugins.index') }}">تصفح Plugin Bundle Catalog</a>
                            <a class="button" href="{{ route('developers.onboarding') }}">ابدأ Publisher Account</a>
                        </div>
                    </div>

                    <aside class="studio-board">
                        <div class="signal dark">
                            <strong>Marketplace is not Mall</strong>
                            <span>Marketplace للثيمات والبلجنز والـ connectors. Mall لاكتشاف الشركات والمنتجات والخدمات. الخلط بينهم يبوظ الثقة والمسار التجاري.</span>
                        </div>
                        <div class="signal">
                            <strong>Manifest Permissions</strong>
                            <span>كل package لازم يعلن scopes، dependencies، migrations، uninstall path، compatibility، license، support، والتوقيع.</span>
                        </div>
                        <div class="signal">
                            <strong>Next.js Component Library</strong>
                            <span>Blade هنا bridge فقط. الثيمات التجارية النهائية تتحول إلى React components ببيانات من Laravel APIs.</span>
                        </div>
                    </aside>
                </div>
            </section>

            @if (in_array($page, ['marketplace_home', 'developer_landing'], true))
                <section class="section dark">
                    <div class="section-title">
                        <div>
                            <span class="chip">Marketplace Lanes</span>
                            <h2>أربع مسارات بدل متجر عشوائي.</h2>
                        </div>
                        <p>V12 يعرض السوق كمنظومة: Design Market، Package Store، Developer Console، وGovernance Console. كل lane لها جمهور وسلوك ومخاطر مختلفة.</p>
                    </div>

                    <div class="grid-4">
                        @foreach ($lanes as $lane)
                            <article class="card">
                                <span class="chip">{{ $lane['key'] }}</span>
                                <h3>{{ $lane['label'] }}</h3>
                                <p>{{ implode('، ', $lane['sells']) }}</p>
                                <div class="hero-actions">
                                    <a class="button" href="{{ route($lane['route']) }}">افتح المسار</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if (in_array($page, ['marketplace_home', 'theme_catalog'], true))
                <section class="section">
                    <div class="section-title">
                        <div>
                            <span class="chip">Design Market</span>
                            <h2>Theme Catalog بفلترة مفهومة قبل المعاينة.</h2>
                        </div>
                        <p>الكارت لا يقول "جميل" فقط. يعرض الفئة، الأداء، RTL، الترخيص، publisher trust، وruntime compatibility.</p>
                    </div>

                    <div class="mini-grid" style="margin-bottom: 16px;">
                        @foreach ($taxonomy['filters'] as $filter)
                            <span class="chip">{{ $filter }}</span>
                        @endforeach
                    </div>

                    <div class="grid-3">
                        @foreach ($themes as $theme)
                            <article class="card">
                                <span class="chip">{{ $theme['type'] }}</span>
                                <h3>{{ $theme['name'] }}</h3>
                                <p>{{ $theme['best_for'] }}</p>
                                <div class="metric">{{ $theme['performance_score'] }}</div>
                                <p class="muted">Performance score · {{ $theme['price_type'] }} · {{ $theme['license'] }}</p>
                                <div class="mini-grid">
                                    @foreach ($theme['compatibility'] as $item)
                                        <span class="chip">{{ $item }}</span>
                                    @endforeach
                                </div>
                                <div class="hero-actions">
                                    <a class="button primary" href="{{ route('marketplace.themes.show', $theme['slug']) }}">Theme Detail</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($page === 'theme_detail')
                <section class="section">
                    <div class="section-title">
                        <div>
                            <span class="chip">Theme Detail and Preview</span>
                            <h2>{{ $selectedTheme['name'] }} كـ preview قابل للفحص، مش مجرد screenshot.</h2>
                        </div>
                        <p>{{ $selectedTheme['best_for'] }}</p>
                    </div>

                    <div class="grid-2">
                        <article class="card">
                            <span class="chip">{{ $selectedTheme['publisher'] }}</span>
                            <h3>Compatibility Matrix</h3>
                            <div class="mini-grid">
                                @foreach ($selectedTheme['compatibility'] as $item)
                                    <span class="chip">{{ $item }}</span>
                                @endforeach
                            </div>
                            <div class="metric">{{ $selectedTheme['performance_score'] }}</div>
                            <p class="muted">RTL: {{ $selectedTheme['supports_rtl'] ? 'supported' : 'not supported' }} · License: {{ $selectedTheme['license'] }}</p>
                        </article>

                        <article class="card">
                            <span class="chip">React Component Runtime Mapping</span>
                            <h3>Theme Manifest to React Components</h3>
                            <div class="mini-grid">
                                @foreach ($selectedTheme['components'] as $component)
                                    <span class="chip">{{ $component }}</span>
                                @endforeach
                            </div>
                            <p style="margin-top: 14px;">Manifest section keys map to typed React components. Laravel keeps data, permissions, and APIs. The theme owns presentation only.</p>
                        </article>
                    </div>

                    <div class="grid-3" style="margin-top: 14px;">
                        @foreach ($selectedTheme['qa'] as $item)
                            <article class="card">
                                <span class="chip">QA</span>
                                <h3>{{ $item }}</h3>
                                <p>Required before publish or agency reuse.</p>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($page === 'theme_recipes')
                <section class="section">
                    <div class="section-title">
                        <div>
                            <span class="chip">Theme Recipes</span>
                            <h2>Apply Preview يشرح ماذا سيتفعل قبل الضغط.</h2>
                        </div>
                        <p>العميل أو الأدمن يرى required، recommended، optional packages، demo content، وخطة rollback قبل التطبيق.</p>
                    </div>

                    <div class="grid-3">
                        @foreach ($themeRecipes as $recipe)
                            <article class="card">
                                <span class="chip">{{ $recipe['app_type'] }}</span>
                                <h3>{{ $recipe['project_type'] }}</h3>
                                <p>{{ $recipe['preview'] }}</p>
                                <div class="mini-grid">
                                    @foreach ($recipe['required'] as $item)
                                        <span class="chip">{{ $item }}</span>
                                    @endforeach
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if (in_array($page, ['marketplace_home', 'plugin_catalog'], true))
                <section class="section">
                    <div class="section-title">
                        <div>
                            <span class="chip">Plugin Bundle Catalog</span>
                            <h2>كل bundle يظهر قيمته ومخاطره قبل التثبيت.</h2>
                        </div>
                        <p>الهدف ليس install سريع. الهدف install مفهوم: permissions، dependencies، compatibility، support، وrisk.</p>
                    </div>

                    <div class="grid-3">
                        @foreach ($plugins as $plugin)
                            <article class="card">
                                <span class="chip risk-{{ $plugin['risk'] }}">{{ $plugin['risk'] }} risk</span>
                                <h3>{{ $plugin['name'] }}</h3>
                                <p>{{ $plugin['support_policy'] }}</p>
                                <div class="mini-grid">
                                    @foreach ($plugin['permissions'] as $scope)
                                        <span class="chip">{{ $scope }}</span>
                                    @endforeach
                                </div>
                                <div class="hero-actions">
                                    <a class="button primary" href="{{ route('marketplace.plugins.show', $plugin['slug']) }}">Plugin Detail</a>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="grid-3" style="margin-top: 14px;">
                        @foreach ($bundles as $bundle)
                            <article class="card">
                                <span class="chip risk-{{ $bundle['risk'] }}">{{ $bundle['bundle_type'] }}</span>
                                <h3>{{ $bundle['name'] }}</h3>
                                <p>{{ implode('، ', $bundle['includes']) }}</p>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($page === 'plugin_detail')
                <section class="section dark">
                    <div class="section-title">
                        <div>
                            <span class="chip">Plugin Detail and Compatibility</span>
                            <h2>{{ $selectedPlugin['name'] }} لا يثبت قبل disclosure واضح.</h2>
                        </div>
                        <p>{{ $selectedPlugin['support_policy'] }}</p>
                    </div>

                    <div class="grid-3">
                        <article class="card">
                            <span class="chip risk-{{ $selectedPlugin['risk'] }}">{{ $selectedPlugin['risk'] }} risk</span>
                            <h3>Manifest Permissions</h3>
                            <div class="mini-grid">
                                @foreach ($selectedPlugin['permissions'] as $scope)
                                    <span class="chip">{{ $scope }}</span>
                                @endforeach
                            </div>
                        </article>

                        <article class="card">
                            <span class="chip">Dependencies</span>
                            <h3>Required modules</h3>
                            <div class="mini-grid">
                                @foreach ($selectedPlugin['dependencies'] as $dependency)
                                    <span class="chip">{{ $dependency }}</span>
                                @endforeach
                            </div>
                        </article>

                        <article class="card">
                            <span class="chip">Compatibility</span>
                            <h3>Install safety</h3>
                            <div class="mini-grid">
                                @foreach ($selectedPlugin['compatibility'] as $item)
                                    <span class="chip">{{ $item }}</span>
                                @endforeach
                            </div>
                        </article>
                    </div>
                </section>
            @endif

            @if (in_array($page, ['developer_landing', 'developer_onboarding'], true))
                <section class="section dark">
                    <div class="section-title">
                        <div>
                            <span class="chip">Creator and Publisher Account Onboarding</span>
                            <h2>من فكرة package إلى Publisher Account قابل للبيع.</h2>
                        </div>
                        <p>الـ developer لا يحتاج صفحة كلام عامة. يحتاج مسار: هوية، حساب publisher، manifest، checks، review، signing، publish، ثم support/revenue.</p>
                    </div>

                    <div class="grid-3">
                        @foreach ($onboarding as $step)
                            <article class="card">
                                <span class="badge">{{ $step['n'] }}</span>
                                <h3>{{ $step['title'] }}</h3>
                                <p>{{ $step['text'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if (in_array($page, ['theme_builder_docs', 'plugin_manifest_docs', 'connector_sdk_docs'], true))
                <section class="section">
                    <div class="section-title">
                        <div>
                            <span class="chip">Developer Documentation</span>
                            <h2>{{ $pageConfig['label'] }}</h2>
                        </div>
                        <p>هذه صفحات docs تشغيلية: fields، runtime، permissions، compatibility، support، وreview gate. ليست مقالات عامة.</p>
                    </div>

                    <div class="grid-3">
                        @foreach ($manifestFields as $kind => $fields)
                            <article class="card">
                                <span class="chip">{{ $kind }} manifest</span>
                                <h3>{{ ucfirst($kind) }} fields</h3>
                                <div class="mini-grid">
                                    @foreach ($fields as $field)
                                        <span class="chip">{{ $field }}</span>
                                    @endforeach
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>

                <section class="section dark">
                    <div class="section-title">
                        <div>
                            <span class="chip">Next.js Theme Component Library Foundation</span>
                            <h2>{{ $componentLibrary['runtime'] }}</h2>
                        </div>
                        <p>{{ $componentLibrary['mapping_rule'] }}</p>
                    </div>

                    <div class="grid-2">
                        <article class="card">
                            <h3>Foundations</h3>
                            <div class="mini-grid">
                                @foreach ($componentLibrary['foundations'] as $foundation)
                                    <span class="chip">{{ $foundation }}</span>
                                @endforeach
                            </div>
                        </article>
                        <article class="card">
                            <h3>Components</h3>
                            <div class="mini-grid">
                                @foreach ($componentLibrary['components'] as $component)
                                    <span class="chip">{{ $component }}</span>
                                @endforeach
                            </div>
                        </article>
                    </div>
                </section>
            @endif

            @if (in_array($page, ['submission_checklist', 'qa_center'], true))
                <section class="section">
                    <div class="section-title">
                        <div>
                            <span class="chip">Theme QA Publishing Checklist UI</span>
                            <h2>QA قبل النشر، مش بعد أول incident.</h2>
                        </div>
                        <p>V12 يجعل theme QA وplugin security permission checklist جزء واضح من تجربة المطور.</p>
                    </div>

                    <div class="grid-2">
                        <article class="card">
                            <span class="chip">Theme QA</span>
                            <h3>Publishing requirements</h3>
                            <div class="mini-grid">
                                @foreach ($qa['theme'] as $item)
                                    <span class="chip">{{ $item }}</span>
                                @endforeach
                            </div>
                        </article>
                        <article class="card">
                            <span class="chip">Plugin QA Security Permission Checklist UI</span>
                            <h3>Security requirements</h3>
                            <div class="mini-grid">
                                @foreach ($qa['plugin'] as $item)
                                    <span class="chip">{{ $item }}</span>
                                @endforeach
                            </div>
                        </article>
                    </div>
                </section>
            @endif

            @if (in_array($page, ['review_status', 'developer_listings', 'governance'], true))
                <section class="section dark">
                    <div class="section-title">
                        <div>
                            <span class="chip">Package Lifecycle</span>
                            <h2>كل status له owner وnext action.</h2>
                        </div>
                        <p>المطور لا يرى "pending" فقط. يرى من يملك الخطوة التالية، وماذا يمنع النشر، وهل القائمة public أم internal أم suspended.</p>
                    </div>

                    <div class="timeline">
                        @foreach ($statuses as $index => $status)
                            <div class="timeline-row">
                                <span class="badge">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                <div>
                                    <h3>{{ $status['status'] }} · {{ $status['owner'] }}</h3>
                                    <p>{{ $status['next_action'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="section">
                    <div class="section-title">
                        <div>
                            <span class="chip">Marketplace Governance and Review Admin UI</span>
                            <h2>Review flow يربط public listing بالـ Filament admin.</h2>
                        </div>
                        <p>المراجعة الحقيقية تبقى في الأدمن، لكن V12 يشرح gates للمطور ولصاحب المنصة بوضوح.</p>
                    </div>

                    <div class="grid-3">
                        @foreach ($reviewFlow as $gate)
                            <article class="card">
                                <span class="chip">{{ $gate['gate'] }}</span>
                                <h3>{{ $gate['gate'] }}</h3>
                                <div class="mini-grid">
                                    @foreach ($gate['checks'] as $check)
                                        <span class="chip">{{ $check }}</span>
                                    @endforeach
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="hero-actions">
                        @foreach ($inventory['admin_routes'] as $adminRoute)
                            @if ($adminRoute['exists'])
                                <a class="button primary" href="{{ route($adminRoute['route']) }}">{{ $adminRoute['route'] }}</a>
                            @else
                                <span class="button">{{ $adminRoute['route'] }} missing</span>
                            @endif
                        @endforeach
                    </div>
                </section>
            @endif

            @if (in_array($page, ['licensing', 'developer_sales'], true))
                <section class="section">
                    <div class="section-title">
                        <div>
                            <span class="chip">Marketplace Licensing Pricing and Revenue Share UI</span>
                            <h2>Revenue Share واضح قبل أول بيع.</h2>
                        </div>
                        <p>V12 يوضح السعر، نسبة المنصة، نوع الترخيص، refunds، tax holds، support SLA، وpayout status بدل أرقام مبهمة.</p>
                    </div>

                    <div class="grid-3">
                        @foreach ($licensing['plans'] as $plan)
                            <article class="card">
                                <span class="chip">Revenue Share</span>
                                <h3>{{ $plan['label'] }}</h3>
                                <div class="metric">{{ $plan['fee'] }}</div>
                                <p>{{ $plan['best_for'] }}</p>
                            </article>
                        @endforeach
                    </div>

                    <div class="mini-grid">
                        @foreach ($licensing['payout_signals'] as $signal)
                            <span class="chip">{{ $signal }}</span>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($page === 'developer_profile')
                <section class="section">
                    <div class="section-title">
                        <div>
                            <span class="chip">Developer Profile and Creator Public Page UX</span>
                            <h2>الثقة في المطور جزء من المنتج نفسه.</h2>
                        </div>
                        <p>Developer profile يعرض الهوية، publisher type، verification، specialties، packages، support policy، certification، وpublic creator page عند السماح.</p>
                    </div>

                    <div class="grid-3">
                        @foreach (['Kabeeri Creator', 'Kabeeri Developer', 'Certified Creator', 'Publisher Account', 'Developer Profile', 'Creator Public Page'] as $identity)
                            <article class="card">
                                <span class="chip">Identity</span>
                                <h3>{{ $identity }}</h3>
                                <p>تعريف واضح يمنع الخلط بين من يصمم، من يبرمج، من يبيع، ومن يتحمل الدعم.</p>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if (in_array($page, ['plugin_detail', 'plugin_manifest_docs', 'governance'], true))
                <section class="section">
                    <div class="section-title">
                        <div>
                            <span class="chip">Package Manifest Permissions Signing and Compatibility UI</span>
                            <h2>Permission disclosure يسبق install/update.</h2>
                        </div>
                        <p>كل scope يظهر risk وشرح بشري. الصلاحيات الحرجة تحتاج review، audit، وسياسات backend حقيقية.</p>
                    </div>

                    <div class="table-card">
                        @foreach ($permissions as $permission)
                            <div class="table-row">
                                <strong>{{ $permission['scope'] }}</strong>
                                <span>{{ $permission['explain'] }}</span>
                                <span class="chip risk-{{ $permission['risk'] }}">{{ $permission['risk'] }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="mini-grid">
                        @foreach ($compatibilityAxes as $axis)
                            <span class="chip">{{ $axis }}</span>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($page === 'governance')
                <section class="section dark">
                    <div class="section-title">
                        <div>
                            <span class="chip">Release Gates</span>
                            <h2>V12 لا يقفل إلا لو routes، docs، DB، admin resources، والتاسكات متزامنين.</h2>
                        </div>
                        <p>هذه مؤشرات داخلية تساعدك كمالك منصة تعرف هل Marketplace Studio جاهز للتجربة.</p>
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
            <span>KABEERI V12 Marketplace Studio</span>
            <span>{{ $pageConfig['uri'] }} · {{ $pageConfig['label'] }}</span>
        </footer>
    </div>
</body>
</html>
