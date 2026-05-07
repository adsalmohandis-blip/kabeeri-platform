@php
    $page = $data['page'];
    $pageConfig = $data['page_config'];
    $pages = $data['pages'];
    $storyLayers = collect($data['story_layers']);
    $audiences = collect($data['audiences']);
    $journeys = collect($data['journeys']);
    $onboardingSteps = collect($data['onboarding_steps']);
    $plans = collect($data['plans']);
    $templates = collect($data['templates']);
    $faq = collect($data['faq']);
    $isArabic = app()->getLocale() === 'ar';
    $copy = fn (string $ar, string $en): string => $isArabic ? $ar : $en;
    $brief = fn (?string $text, int $words = 16): string => \Illuminate\Support\Str::words(\Illuminate\Support\Str::squish((string) $text), $words, '');
    $pageLabel = (string) ($pageConfig['label'] ?? __('kabeeri.brand.name'));
    $pageIntent = (string) ($pageConfig['intent'] ?? '');
    $navKeys = ['landing', 'business', 'onboarding', 'trust', 'contact'];
    $audienceKeys = ['business', 'enterprise', 'developers', 'partners'];
    $audienceKey = match ($page) {
        'business' => 'business',
        'enterprise' => 'enterprise',
        'developers' => 'developers',
        'partners' => 'partners',
        default => null,
    };
    $selectedAudience = $audienceKey ? $audiences->get($audienceKey) : null;
    $isAudiencePage = filled($selectedAudience);
    $isOnboardingPage = in_array($page, ['onboarding', 'workspace_setup'], true);

    $leadTitle = match ($page) {
        'business' => $copy('ابدأ حضورك التجاري ثم وسع التشغيل', 'Launch the business presence, then expand operations'),
        'enterprise' => $copy('حوكمة واضحة لفريق كبير', 'Controlled adoption for larger teams'),
        'developers' => $copy('ابن وبيع داخل اقتصاد كبيري', 'Build and sell inside the kabeeri economy'),
        'partners' => $copy('حوّل الإحالات إلى نمو واضح', 'Turn referrals into measurable growth'),
        'wordpress' => $copy('بديل أبسط لفوضى الإضافات', 'A simpler alternative to plugin sprawl'),
        'service_business' => $copy('مسار واضح لشركات الخدمات', 'A clear path for service businesses'),
        'templates' => $copy('قوالب بداية حسب نوع العمل', 'Starter templates by business type'),
        'onboarding', 'workspace_setup' => $copy('من اختيار المسار إلى أول تطبيق', 'From path choice to first app'),
        'pricing' => $copy('اشترك حسب المرحلة', 'Subscribe by stage'),
        'trust' => $copy('افهم الملكية والثقة قبل القرار', 'Understand ownership and trust before deciding'),
        'contact' => $copy('اكتب احتياجك في دقيقة', 'Share your need in one minute'),
        'audiences' => $copy('اختر المسار الأقرب لك', 'Choose the closest path'),
        default => $copy('منصة واحدة تبدأ بسيطة وتكبر معك', 'One platform that starts simple and grows with you'),
    };

    $leadText = match ($page) {
        'business' => $copy('موقع، محتوى، عملاء، تجارة، ثم تشغيل عندما تحتاج.', 'Website, content, leads, commerce, then operations when needed.'),
        'enterprise' => $copy('صلاحيات، تدقيق، تكاملات، وقرار نشر منضبط.', 'Permissions, audit, integrations, and controlled release decisions.'),
        'developers' => $copy('ثيمات وإضافات بحوكمة مراجعة ونشر وربح.', 'Themes and plugins with review, publishing, and revenue rules.'),
        'partners' => $copy('إحالات وحملات وتسليم عملاء بدون ضياع.', 'Referrals, campaigns, and lead handoff without noise.'),
        'wordpress' => $copy('ابدأ بموقع منظم ثم أضف التجارة والتشغيل عند الحاجة.', 'Start with a structured site, then add commerce and operations.'),
        'service_business' => $copy('خدمات، طلبات، عروض أسعار، وفواتير في مسار واحد.', 'Services, requests, quotes, and invoices in one path.'),
        'templates' => $copy('اختيارات قليلة تساعدك تبدأ بدون حيرة.', 'A few focused choices to start without confusion.'),
        'onboarding', 'workspace_setup' => $copy('نسأل فقط عما يلزم الآن، ثم نفتح الباقي تدريجيا.', 'We ask only what is needed now, then reveal the rest gradually.'),
        'pricing' => $copy('الخطة تبدأ صغيرة ثم تكبر حسب الاستخدام.', 'The plan starts small and grows with usage.'),
        'trust' => $copy('إجابات مختصرة عن البيانات، النشر، المتجر، والظهور العام.', 'Short answers about data, publishing, marketplace, and public visibility.'),
        'contact' => $copy('لا تحتاج شرحا طويلا. الهدف الحالي يكفي.', 'No long brief needed. The current goal is enough.'),
        default => $brief($pageIntent, 18),
    };

    $summaryRows = [
        ['icon' => 'apps', 'label' => $copy('ابدأ بتطبيق', 'Start with an app'), 'text' => $copy('موقع أو متجر أو خدمات حسب احتياجك.', 'Website, store, or services by need.')],
        ['icon' => 'theme', 'label' => $copy('اختر ثيم', 'Choose a theme'), 'text' => $copy('واجهة أولى واضحة قابلة للتبديل لاحقا.', 'A clear first interface you can switch later.')],
        ['icon' => 'plugin', 'label' => $copy('أضف قدرات', 'Add capabilities'), 'text' => $copy('فعّل فقط ما يخدم المرحلة الحالية.', 'Enable only what serves the current stage.')],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Localization\KabeeriLocale::direction() }}" data-kbr-theme="{{ $kabeeriUi['theme'] ?? 'light' }}" data-kbr-font="{{ $kabeeriUi['font'] ?? 'ibm-plex-sans-arabic' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageLabel }} | {{ __('kabeeri.brand.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=almarai:400,700,800|ibm-plex-sans-arabic:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('components.theme-foundation')
    <style>
        :root {
            --kbr-public-ink: #111111;
            --kbr-public-paper: #f1eadc;
            --kbr-public-paper-strong: #ebe1d0;
            --kbr-public-white: #ffffff;
            --kbr-public-muted: rgba(17,17,17,.62);
            --kbr-public-line: rgba(17,17,17,.12);
            --kbr-public-soft: rgba(255,255,255,.42);
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            min-height: 100vh;
            color: var(--kbr-public-ink);
            background: linear-gradient(180deg, var(--kbr-public-paper) 0%, var(--kbr-public-paper-strong) 100%);
            font-family: "IBM Plex Sans Arabic", "Almarai", sans-serif;
        }
        a { color: inherit; text-decoration: none; }
        .shell { width: min(100% - 32px, 1160px); margin-inline: auto; padding: 16px 0 52px; }
        .topbar {
            position: sticky;
            top: 12px;
            z-index: var(--kbr-nav-layer, 1000);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            border: 1px solid var(--kbr-public-line);
            border-radius: 999px;
            background: rgba(241,234,220,.9);
            padding: 9px 11px;
            backdrop-filter: blur(18px);
        }
        .brand { display: inline-flex; align-items: center; gap: 10px; font-weight: 900; }
        .mark { display: grid; width: 40px; height: 40px; place-items: center; border-radius: 999px; background: #111; color: #f1eadc; }
        .brand small { display: block; margin-top: 1px; color: var(--kbr-public-muted); font-size: 12px; font-weight: 800; }
        .nav, .actions { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
        .nav a, .button, button.button {
            display: inline-flex;
            min-height: 38px;
            align-items: center;
            justify-content: center;
            gap: 7px;
            border: 1px solid var(--kbr-public-line);
            border-radius: 999px;
            background: rgba(255,255,255,.5);
            padding: 0 13px;
            color: #111;
            font-size: 13px;
            font-weight: 900;
        }
        .nav a.active, .button.primary { background: #111; color: #f1eadc; border-color: #111; }
        .hero { padding: clamp(36px, 7vw, 86px) 0 28px; }
        .eyebrow { display: inline-flex; align-items: center; gap: 8px; margin-bottom: 16px; color: var(--kbr-public-muted); font-size: 13px; font-weight: 900; }
        h1 { max-width: 900px; margin: 0; font-size: clamp(35px, 6vw, 74px); line-height: .98; letter-spacing: -.06em; }
        .lead { max-width: 690px; margin: 18px 0 0; color: var(--kbr-public-muted); font-size: clamp(15px, 1.4vw, 18px); line-height: 1.72; }
        .hero .actions { margin-top: 26px; }
        .section { padding: 26px 0; border-top: 1px solid var(--kbr-public-line); }
        .section-head { display: grid; grid-template-columns: minmax(0,.9fr) minmax(0,1.1fr); gap: 22px; align-items: start; margin-bottom: 12px; }
        h2 { margin: 0; font-size: clamp(24px, 3vw, 38px); line-height: 1.08; letter-spacing: -.045em; }
        .section-head p, .muted { margin: 0; color: var(--kbr-public-muted); line-height: 1.72; }
        .line-list { display: grid; border-top: 1px solid var(--kbr-public-line); }
        .line-row { display: grid; grid-template-columns: 2.6rem minmax(0,.86fr) minmax(0,1.14fr); gap: 14px; align-items: center; border-bottom: 1px solid var(--kbr-public-line); padding: 15px 0; }
        .line-row strong { font-size: 15px; }
        .line-row p, .line-row span { margin: 0; color: var(--kbr-public-muted); line-height: 1.62; }
        .line-icon, .number { display: inline-grid; width: 34px; height: 34px; place-items: center; border-radius: 999px; background: #111; color: #f1eadc; font-size: 12px; font-weight: 900; }
        .intro-strip { display: grid; grid-template-columns: repeat(3,minmax(0,1fr)); gap: 0; border-top: 1px solid var(--kbr-public-line); border-bottom: 1px solid var(--kbr-public-line); }
        .intro-item { display: grid; gap: 8px; padding: 18px 18px; border-inline-end: 1px solid var(--kbr-public-line); }
        .intro-item:last-child { border-inline-end: 0; }
        .intro-item strong { display: flex; align-items: center; gap: 8px; }
        .intro-item p { margin: 0; color: var(--kbr-public-muted); line-height: 1.62; font-size: 13px; }
        .focus-band { display: grid; grid-template-columns: minmax(0,1fr) minmax(280px,.55fr); gap: 18px; align-items: stretch; }
        .focus-dark, .focus-light { border: 1px solid var(--kbr-public-line); border-radius: 28px; padding: 22px; }
        .focus-dark { background: #111; color: #f1eadc; }
        .focus-dark p { color: rgba(241,234,220,.72); }
        .focus-light { background: rgba(255,255,255,.38); }
        .chips { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 14px; }
        .chip { display: inline-flex; min-height: 32px; align-items: center; border: 1px solid var(--kbr-public-line); border-radius: 999px; background: rgba(255,255,255,.5); padding: 0 11px; font-size: 12px; font-weight: 900; }
        .form-panel { border: 1px solid var(--kbr-public-line); border-radius: 28px; background: rgba(255,255,255,.42); padding: 18px; }
        .form-grid { display: grid; grid-template-columns: repeat(2,minmax(0,1fr)); gap: 12px; }
        label { display: grid; gap: 7px; color: #111; font-size: 13px; font-weight: 900; }
        input, select, textarea { width: 100%; border: 1px solid var(--kbr-public-line); border-radius: 16px; background: #fff; color: #111; padding: 12px 13px; font: inherit; }
        textarea { min-height: 128px; resize: vertical; }
        .full { grid-column: 1 / -1; }
        .footer { display: flex; justify-content: space-between; gap: 14px; padding-top: 22px; color: var(--kbr-public-muted); font-size: 13px; }
        @media (max-width: 900px) {
            .topbar { align-items: stretch; border-radius: 24px; flex-direction: column; }
            .nav, .actions { align-items: stretch; flex-direction: column; }
            .nav a, .button, button.button { width: 100%; }
            .section-head, .line-row, .focus-band, .form-grid, .intro-strip { grid-template-columns: 1fr; }
            .intro-item { border-inline-end: 0; border-bottom: 1px solid var(--kbr-public-line); }
            .intro-item:last-child { border-bottom: 0; }
            h1 { letter-spacing: -.04em; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <a class="brand" href="{{ route('public.landing') }}">
                <span class="mark">{{ __('kabeeri.brand.mark') }}</span>
                <span>
                    <strong>{{ __('kabeeri.brand.name') }}</strong>
                    <small>{{ $copy('منصة أعمال مرنة', 'Business platform') }}</small>
                </span>
            </a>
            <nav class="nav" aria-label="{{ $copy('تنقل المنصة', 'Platform navigation') }}">
                @foreach ($navKeys as $key)
                    @php($navPage = $pages[$key] ?? null)
                    @if ($navPage && \Illuminate\Support\Facades\Route::has($navPage['route']))
                        <a class="{{ $page === $key ? 'active' : '' }}" href="{{ route($navPage['route']) }}">{{ $navPage['label'] }}</a>
                    @endif
                @endforeach
                @include('components.language-switcher', ['context' => 'platform_public'])
                @include('components.theme-switcher', ['context' => 'platform_public'])
            </nav>
        </header>

        <main>
            <section class="hero">
                <span class="eyebrow"><x-kabeeri-icon name="info" />{{ $pageLabel }}</span>
                <h1>{{ $leadTitle }}</h1>
                <p class="lead">{{ $leadText }}</p>
                <div class="actions">
                    <a class="button primary" href="{{ route('customer.start') }}"><x-kabeeri-icon name="rocket" />{{ $copy('ابدأ الآن', 'Start now') }}</a>
                    <a class="button" href="{{ route('public.contact') }}"><x-kabeeri-icon name="mail" />{{ $copy('تواصل مختصر', 'Short contact') }}</a>
                </div>
            </section>

            @if ($page === 'contact')
                <section class="section">
                    <div class="section-head">
                        <h2>{{ $copy('طلب واحد يكفي للبدء', 'One request is enough') }}</h2>
                        <p>{{ $copy('سنحوّل الطلب إلى عميل محتمل داخل النظام ونحدد الخطوة التالية.', 'We turn the request into a CRM lead and define the next step.') }}</p>
                    </div>
                    <form class="form-panel" method="POST" action="{{ route('public.contact.store') }}">
                        @csrf
                        @if ($errors->any())
                            <p class="muted">{{ $errors->first() }}</p>
                        @endif
                        <div class="form-grid">
                            <label>{{ $copy('المسار', 'Audience') }}<select name="audience" required>@foreach ($audiences as $key => $audience)<option value="{{ $key }}">{{ $audience['label'] }}</option>@endforeach</select></label>
                            <label>{{ $copy('الخطة', 'Plan') }}<select name="plan_interest"><option value="business">{{ $copy('أعمال', 'Business') }}</option><option value="starter">{{ $copy('بداية', 'Starter') }}</option><option value="enterprise">{{ $copy('مؤسسات', 'Enterprise') }}</option></select></label>
                            <label>{{ $copy('الشركة', 'Company') }}<input name="company_name"></label>
                            <label>{{ $copy('الاسم', 'Name') }}<input name="name" required></label>
                            <label>{{ $copy('البريد', 'Email') }}<input type="email" name="email" required></label>
                            <label>{{ $copy('الهاتف', 'Phone') }}<input name="phone"></label>
                            <label class="full">{{ $copy('الاحتياج', 'Need') }}<textarea name="message" required></textarea></label>
                        </div>
                        <button class="button primary" type="submit" style="margin-top:12px"><x-kabeeri-icon name="check" />{{ $copy('إرسال', 'Send') }}</button>
                    </form>
                </section>
            @elseif ($page === 'trust')
                <section class="section">
                    <div class="section-head">
                        <h2>{{ $copy('ما يجب أن تعرفه أولا', 'What to know first') }}</h2>
                        <p>{{ $copy('نركز هنا على القرار: هل المنصة مناسبة؟ ماذا تملك؟ وكيف يظهر عملك؟', 'This page focuses on the decision: fit, ownership, and visibility.') }}</p>
                    </div>
                    <div class="line-list">
                        @foreach ($faq->take(5) as $item)
                            <article class="line-row">
                                <span class="number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <strong>{{ $item['q'] }}</strong>
                                <p>{{ $brief($item['a'], 18) }}</p>
                            </article>
                        @endforeach
                    </div>
                </section>
            @elseif ($isOnboardingPage)
                <section class="section">
                    <div class="section-head">
                        <h2>{{ $copy('خطوات قليلة، قرار أوضح', 'Few steps, clearer decision') }}</h2>
                        <p>{{ $copy('نرتب البداية حتى لا يرى العميل إلا ما يحتاجه في هذه المرحلة.', 'The start is staged so the customer sees only what matters now.') }}</p>
                    </div>
                    <div class="line-list">
                        @foreach ($onboardingSteps->take(6) as $step)
                            <article class="line-row">
                                <span class="number">{{ $step['n'] ?? str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <strong>{{ $step['title'] }}</strong>
                                <p>{{ $brief($step['text'], 16) }}</p>
                            </article>
                        @endforeach
                    </div>
                </section>
            @elseif ($page === 'pricing')
                <section class="section">
                    <div class="section-head"><h2>{{ $copy('خطة حسب المرحلة', 'Plan by stage') }}</h2><p>{{ $copy('ابدأ بتكلفة صغيرة، ثم أضف ما يخدم النمو.', 'Start small, then add what supports growth.') }}</p></div>
                    <div class="line-list">
                        @foreach ($plans as $plan)
                            <article class="line-row"><span class="number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><strong>{{ $plan['name'] }}</strong><p>{{ $plan['price'] }} · {{ $brief($plan['best_for'], 12) }}</p></article>
                        @endforeach
                    </div>
                </section>
            @elseif ($page === 'templates')
                <section class="section">
                    <div class="section-head"><h2>{{ $copy('ابدأ من نموذج قريب', 'Start from a close fit') }}</h2><p>{{ $copy('القالب ليس زخرفة، بل اختصار لمسار العمل.', 'A template is not decoration; it shortens the workflow.') }}</p></div>
                    <div class="line-list">
                        @foreach ($templates as $template)
                            <article class="line-row"><span class="number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><strong>{{ $template['title'] }}</strong><p>{{ collect($template['includes'])->take(4)->implode(' / ') }}</p></article>
                        @endforeach
                    </div>
                </section>
            @elseif ($isAudiencePage)
                <section class="section">
                    <div class="focus-band">
                        <div class="focus-dark">
                            <h2>{{ $selectedAudience['headline'] }}</h2>
                            <p class="lead">{{ $brief($selectedAudience['outcome'], 18) }}</p>
                        </div>
                        <div class="focus-light">
                            <strong>{{ $copy('المشكلة', 'Pain') }}</strong>
                            <p class="muted" style="margin-top:10px">{{ $brief($selectedAudience['pain'], 18) }}</p>
                        </div>
                    </div>
                </section>
                <section class="section">
                    <div class="section-head"><h2>{{ $copy('المسار العملي', 'Working path') }}</h2><p>{{ $copy('خطوات واضحة بدون عرض كل إمكانيات المنصة مرة واحدة.', 'Clear steps without showing every capability at once.') }}</p></div>
                    <div class="line-list">
                        @foreach (collect($journeys->get($audienceKey, []))->take(6) as $item)
                            <article class="line-row"><span class="number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><strong>{{ $item }}</strong><p>{{ $loop->last ? $copy('بعدها توسع حسب الاحتياج.', 'Then expand by need.') : $copy('انتقل للخطوة التالية فقط عند جاهزيتك.', 'Move forward only when ready.') }}</p></article>
                        @endforeach
                    </div>
                </section>
            @else
                <section class="section">
                    <div class="intro-strip">
                        @foreach ($summaryRows as $row)
                            <article class="intro-item">
                                <strong><x-kabeeri-icon name="{{ $row['icon'] }}" />{{ $row['label'] }}</strong>
                                <p>{{ $row['text'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </section>
                <section class="section">
                    <div class="section-head">
                        <h2>{{ $copy('اختر مسارك', 'Choose your path') }}</h2>
                        <p>{{ $copy('كل مسار له بداية مختلفة، لكنهم يلتقون داخل نفس المنصة.', 'Each path starts differently, but meets inside the same platform.') }}</p>
                    </div>
                    <div class="line-list">
                        @foreach ($audienceKeys as $key)
                            @php($audience = $audiences->get($key))
                            @if ($audience)
                                <a class="line-row" href="{{ \Illuminate\Support\Facades\Route::has($audience['route']) ? route($audience['route']) : '#' }}">
                                    <span class="line-icon"><x-kabeeri-icon name="account" style="margin-inline-end:0" /></span>
                                    <strong>{{ $audience['label'] }}</strong>
                                    <p>{{ $brief($audience['headline'], 14) }}</p>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </section>
                <section class="section">
                    <div class="section-head">
                        <h2>{{ $copy('تنمو بدون تعقيد', 'Grow without complexity') }}</h2>
                        <p>{{ $copy('لا نعرض كل شيء من البداية. نضيف الإمكانيات عند الحاجة.', 'We do not show everything upfront. Capabilities appear when needed.') }}</p>
                    </div>
                    <div class="line-list">
                        @foreach ($storyLayers->take(4) as $layer)
                            <article class="line-row"><span class="number">{{ $layer['step'] ?? str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><strong>{{ $layer['title'] }}</strong><p>{{ $brief($layer['text'], 14) }}</p></article>
                        @endforeach
                    </div>
                </section>
            @endif
        </main>

        <footer class="footer">
            <span>{{ __('kabeeri.brand.name') }}</span>
            <span>{{ $copy('كلام أقل. قرار أوضح.', 'Less copy. Clearer decision.') }}</span>
        </footer>
    </div>
</body>
</html>
