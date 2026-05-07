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
    $brief = fn (?string $text, int $words = 18): string => \Illuminate\Support\Str::words(\Illuminate\Support\Str::squish((string) $text), $words, '');
    $pageLabel = (string) ($pageConfig['label'] ?? __('kabeeri.brand.name'));
    $pageIntent = (string) ($pageConfig['intent'] ?? '');
    $audienceKey = match ($page) {
        'business' => 'business',
        'enterprise' => 'enterprise',
        'developers' => 'developers',
        'partners' => 'partners',
        default => null,
    };
    $selectedAudience = $audienceKey ? $audiences->get($audienceKey) : null;
    $navKeys = ['landing', 'business', 'onboarding', 'trust', 'contact'];
    $leadTitle = match ($page) {
        'business' => $copy('مسار عمل واضح من أول تطبيق إلى نمو حقيقي', 'A clear business path from first app to growth'),
        'onboarding' => $copy('بداية قصيرة ثم مساحة عمل جاهزة', 'A short start, then a ready workspace'),
        'trust' => $copy('ثقة مفهومة قبل الاشتراك', 'Trust made clear before signup'),
        'contact' => $copy('حدثنا عن احتياجك', 'Tell us what you need'),
        default => $pageLabel,
    };
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
            --kbr-public-muted: rgba(17,17,17,.64);
            --kbr-public-line: rgba(17,17,17,.12);
            --kbr-public-shadow: 0 20px 70px rgba(17,17,17,.08);
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            min-height: 100vh;
            color: var(--kbr-public-ink);
            background:
                linear-gradient(180deg, var(--kbr-public-paper) 0%, var(--kbr-public-paper-strong) 100%);
            font-family: "IBM Plex Sans Arabic", "Almarai", sans-serif;
        }
        a { color: inherit; text-decoration: none; }
        .shell { width: min(100% - 32px, 1180px); margin-inline: auto; padding: 18px 0 56px; }
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
            background: rgba(241,234,220,.86);
            padding: 10px 12px;
            backdrop-filter: blur(18px);
        }
        .brand { display: inline-flex; align-items: center; gap: 10px; font-weight: 900; }
        .mark { display: grid; width: 42px; height: 42px; place-items: center; border-radius: 999px; background: #111; color: #f1eadc; }
        .brand small { display: block; margin-top: 2px; color: var(--kbr-public-muted); font-size: 12px; font-weight: 800; }
        .nav, .actions { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
        .nav a, .button {
            display: inline-flex;
            min-height: 38px;
            align-items: center;
            justify-content: center;
            gap: 7px;
            border: 1px solid var(--kbr-public-line);
            border-radius: 999px;
            background: rgba(255,255,255,.46);
            padding: 0 13px;
            color: #111;
            font-size: 13px;
            font-weight: 900;
        }
        .nav a.active, .button.primary { background: #111; color: #f1eadc; border-color: #111; }
        .hero { padding: clamp(34px, 7vw, 90px) 0 34px; }
        .eyebrow { display: inline-flex; margin-bottom: 18px; color: var(--kbr-public-muted); font-size: 13px; font-weight: 900; }
        h1 { max-width: 880px; margin: 0; font-size: clamp(34px, 6vw, 76px); line-height: .98; letter-spacing: -.06em; }
        .lead { max-width: 720px; margin: 20px 0 0; color: var(--kbr-public-muted); font-size: clamp(15px, 1.5vw, 19px); line-height: 1.75; }
        .hero .actions { margin-top: 28px; }
        .section { padding: 28px 0; border-top: 1px solid var(--kbr-public-line); }
        .section-head { display: grid; grid-template-columns: minmax(0, .85fr) minmax(0, 1.15fr); gap: 22px; align-items: start; margin-bottom: 18px; }
        h2 { margin: 0; font-size: clamp(23px, 3vw, 38px); line-height: 1.08; letter-spacing: -.045em; }
        .section-head p { margin: 0; color: var(--kbr-public-muted); line-height: 1.75; }
        .line-list { display: grid; gap: 0; border-top: 1px solid var(--kbr-public-line); }
        .line-row { display: grid; grid-template-columns: minmax(0,.8fr) minmax(0,1.2fr) auto; gap: 16px; align-items: center; border-bottom: 1px solid var(--kbr-public-line); padding: 16px 0; }
        .line-row strong { font-size: 16px; }
        .line-row p, .line-row span { margin: 0; color: var(--kbr-public-muted); line-height: 1.65; }
        .number { display: inline-grid; width: 34px; height: 34px; place-items: center; border-radius: 999px; background: #111; color: #f1eadc; font-size: 12px; font-weight: 900; }
        .quiet-panel { border: 1px solid var(--kbr-public-line); border-radius: 28px; background: rgba(255,255,255,.42); padding: 20px; box-shadow: var(--kbr-public-shadow); }
        .split { display: grid; grid-template-columns: minmax(0,1fr) minmax(320px,.72fr); gap: 18px; align-items: start; }
        .dark-panel { background: #111; color: #f1eadc; }
        .dark-panel p, .dark-panel span { color: rgba(241,234,220,.68); }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 12px; }
        label { display: grid; gap: 7px; color: #111; font-size: 13px; font-weight: 900; }
        input, select, textarea { width: 100%; border: 1px solid var(--kbr-public-line); border-radius: 16px; background: #fff; color: #111; padding: 12px 13px; font: inherit; }
        textarea { min-height: 130px; resize: vertical; }
        .full { grid-column: 1 / -1; }
        .footer { display: flex; justify-content: space-between; gap: 14px; padding-top: 24px; color: var(--kbr-public-muted); font-size: 13px; }
        @media (max-width: 900px) {
            .topbar { align-items: stretch; border-radius: 24px; flex-direction: column; }
            .nav, .actions { align-items: stretch; flex-direction: column; }
            .nav a, .button { width: 100%; }
            .section-head, .line-row, .split, .form-grid { grid-template-columns: 1fr; }
            .line-row { gap: 8px; }
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
                <span class="eyebrow">{{ $pageLabel }}</span>
                <h1>{{ $leadTitle }}</h1>
                <p class="lead">{{ $brief($pageIntent, 18) }}</p>
                <div class="actions">
                    <a class="button primary" href="{{ route('customer.start') }}">{{ $copy('ابدأ الآن', 'Start now') }}</a>
                    <a class="button" href="{{ route('public.contact') }}">{{ $copy('تحدث معنا', 'Talk to us') }}</a>
                </div>
            </section>

            @if ($page === 'contact')
                <section class="section">
                    <div class="split">
                        <div class="quiet-panel dark-panel">
                            <h2>{{ $copy('طلب مختصر يكفي كبداية', 'A short request is enough') }}</h2>
                            <p>{{ $copy('اكتب هدفك وسنرتب الخطوة التالية.', 'Share the goal and we will map the next step.') }}</p>
                        </div>
                        <form class="quiet-panel" method="POST" action="{{ route('public.contact.store') }}">
                            @csrf
                            @if ($errors->any())
                                <p>{{ $errors->first() }}</p>
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
                            <button class="button primary" type="submit" style="margin-top:12px">{{ $copy('إرسال', 'Send') }}</button>
                        </form>
                    </div>
                </section>
            @elseif ($page === 'trust')
                <section class="section">
                    <div class="section-head">
                        <h2>{{ $copy('أسئلة الثقة', 'Trust questions') }}</h2>
                        <p>{{ $copy('إجابات قصيرة عن الملكية، الخصوصية، المراجعة، والنشر.', 'Short answers about ownership, privacy, review, and publishing.') }}</p>
                    </div>
                    <div class="line-list">
                        @foreach ($faq->take(6) as $item)
                            <article class="line-row">
                                <strong>{{ $item['q'] }}</strong>
                                <p>{{ $brief($item['a'], 20) }}</p>
                                <span class="number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            </article>
                        @endforeach
                    </div>
                </section>
            @elseif (in_array($page, ['onboarding', 'workspace_setup'], true))
                <section class="section">
                    <div class="section-head">
                        <h2>{{ $copy('مسار البداية', 'Onboarding path') }}</h2>
                        <p>{{ $copy('خطوات قليلة حتى يصبح أول تطبيق جاهزا.', 'A few steps until the first app is ready.') }}</p>
                    </div>
                    <div class="line-list">
                        @foreach ($onboardingSteps as $step)
                            <article class="line-row">
                                <strong>{{ $step['title'] }}</strong>
                                <p>{{ $brief($step['text'], 18) }}</p>
                                <span class="number">{{ $step['n'] ?? str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            </article>
                        @endforeach
                    </div>
                </section>
            @elseif ($page === 'pricing')
                <section class="section">
                    <div class="section-head"><h2>{{ $copy('خطط بسيطة', 'Simple plans') }}</h2><p>{{ $copy('ابدأ صغيرا ثم وسع عند الحاجة.', 'Start small and expand when needed.') }}</p></div>
                    <div class="line-list">
                        @foreach ($plans as $plan)
                            <article class="line-row"><strong>{{ $plan['name'] }}</strong><p>{{ $brief($plan['best_for'], 16) }}</p><span>{{ $plan['price'] }}</span></article>
                        @endforeach
                    </div>
                </section>
            @elseif ($page === 'templates')
                <section class="section">
                    <div class="section-head"><h2>{{ $copy('قوالب البداية', 'Starter templates') }}</h2><p>{{ $copy('اختيارات واضحة حسب نوع العمل.', 'Clear choices by business type.') }}</p></div>
                    <div class="line-list">
                        @foreach ($templates as $template)
                            <article class="line-row"><strong>{{ $template['title'] }}</strong><p>{{ collect($template['includes'])->take(5)->implode(' / ') }}</p><span class="number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span></article>
                        @endforeach
                    </div>
                </section>
            @elseif ($selectedAudience)
                <section class="section">
                    <div class="split">
                        <div class="quiet-panel dark-panel">
                            <h2>{{ $selectedAudience['headline'] }}</h2>
                            <p>{{ $brief($selectedAudience['outcome'], 20) }}</p>
                        </div>
                        <div class="quiet-panel">
                            <strong>{{ $copy('المشكلة التي نحلها', 'Pain solved') }}</strong>
                            <p>{{ $brief($selectedAudience['pain'], 20) }}</p>
                        </div>
                    </div>
                </section>
                <section class="section">
                    <div class="section-head"><h2>{{ $copy('الخطوات', 'Steps') }}</h2><p>{{ $copy('مسار واضح بدون إرباك.', 'A clear path without noise.') }}</p></div>
                    <div class="line-list">
                        @foreach (collect($journeys->get($audienceKey, [])) as $item)
                            <article class="line-row"><strong>{{ $item }}</strong><p>{{ $copy('انتقل للخطوة التالية عند جاهزيتك.', 'Move to the next step when ready.') }}</p><span class="number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span></article>
                        @endforeach
                    </div>
                </section>
            @else
                <section class="section">
                    <div class="section-head">
                        <h2>{{ $copy('اختر المسار المناسب', 'Choose the right path') }}</h2>
                        <p>{{ $copy('كل مسار يبدأ بتطبيق ثم يتوسع حسب احتياجك.', 'Each path starts with an app, then grows with your needs.') }}</p>
                    </div>
                    <div class="line-list">
                        @foreach ($audiences->take(4) as $audience)
                            <a class="line-row" href="{{ \Illuminate\Support\Facades\Route::has($audience['route']) ? route($audience['route']) : '#' }}">
                                <strong>{{ $audience['label'] }}</strong>
                                <p>{{ $brief($audience['headline'], 18) }}</p>
                                <span class="number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            </a>
                        @endforeach
                    </div>
                </section>
                <section class="section">
                    <div class="section-head">
                        <h2>{{ $copy('ماذا تفعل المنصة؟', 'What the platform does') }}</h2>
                        <p>{{ $copy('شرح قصير للأجزاء الأساسية بدون ازدحام.', 'A short explanation of the essentials.') }}</p>
                    </div>
                    <div class="line-list">
                        @foreach ($storyLayers->take(4) as $layer)
                            <article class="line-row"><strong>{{ $layer['title'] }}</strong><p>{{ $brief($layer['text'], 18) }}</p><span class="number">{{ $layer['step'] ?? str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span></article>
                        @endforeach
                    </div>
                </section>
            @endif
        </main>

        <footer class="footer">
            <span>{{ __('kabeeri.brand.name') }}</span>
            <span>{{ $copy('تصميم هادئ. قرار أوضح.', 'Calm design. Clearer decision.') }}</span>
        </footer>
    </div>
</body>
</html>
