<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Localization\KabeeriLocale::direction() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Guided Onboarding - KABEERI</title>
    @include('customer.v16-style')
</head>
<body>
<div class="shell">
    <header class="top">
        <a class="brand" href="{{ route('customer.start') }}">
            <span class="mark">K</span>
            <span><strong>Guided Onboarding</strong><small>إنشاء مساحة العميل وتطبيقه الأول</small></span>
        </a>
        <nav class="nav">
            <a href="{{ route('customer.start') }}">البداية</a>
            <a href="{{ route('customer.workspace') }}">Customer Dashboard</a>
            @include('components.language-switcher', ['context' => 'customer'])
        </nav>
    </header>

    <main class="hero">
        <section>
            <span class="kicker">Step 1 to 4</span>
            <h1>اختار المسار، التطبيق، الثيم، ثم فعّل أول Workspace.</h1>
            <p class="lead">هذا الفورم يحول العميل من زائر إلى مالك مساحة عمل: Organization، Company عند الحاجة، Site/App، Theme settings، ومحتوى بداية.</p>
        </section>
        <aside class="card dark">
            <h2>المخرجات بعد الضغط</h2>
            <p>Workspace active + app active + compatible theme installed + starter pages imported + customer capabilities saved.</p>
        </aside>
    </main>

    <form class="section" method="POST" action="{{ route('customer.onboarding.store') }}">
        @csrf
        @if ($errors->any())
            <div class="card error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="grid two">
            <section class="card">
                <span class="tag">1. Audience path</span>
                <div class="field" style="margin-top:14px">
                    <label for="customer_path">مسارك الأساسي</label>
                    <select id="customer_path" name="customer_path">
                        @foreach ($paths as $key => $path)
                            <option value="{{ $key }}" @selected(old('customer_path', $selectedPath) === $key)>{{ $path['label'] }} - {{ $path['headline'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="organization_name">اسم مساحة العمل</label>
                    <input id="organization_name" name="organization_name" value="{{ old('organization_name', auth()->user()->name . ' Workspace') }}" required>
                </div>
                <div class="field">
                    <label for="company_name">اسم الشركة أو العلامة التجارية</label>
                    <input id="company_name" name="company_name" value="{{ old('company_name') }}" placeholder="اختياري حسب المسار">
                </div>
                <div class="field">
                    <label for="site_name">اسم التطبيق أو الموقع</label>
                    <input id="site_name" name="site_name" value="{{ old('site_name', auth()->user()->name . ' App') }}" required>
                </div>
            </section>

            <section class="card">
                <span class="tag">2. App type</span>
                <div class="nav" style="margin:14px 0">
                    @foreach ($appTypes as $key => $type)
                        <a class="{{ old('app_type', $selectedAppType) === $key ? 'primary' : '' }}" href="{{ route('customer.onboarding', ['path' => old('customer_path', $selectedPath), 'app_type' => $key]) }}">{{ $type['label'] }}</a>
                    @endforeach
                </div>
                <div class="field">
                    <label for="app_type">نوع التطبيق</label>
                    <select id="app_type" name="app_type">
                        @foreach ($appTypes as $key => $type)
                            <option value="{{ $key }}" @selected(old('app_type', $selectedAppType) === $key)>{{ $type['label'] }} - {{ $type['intent'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid two">
                    <div class="field">
                        <label for="language">اللغة</label>
                        <input id="language" name="language" value="{{ old('language', 'ar') }}">
                    </div>
                    <div class="field">
                        <label for="timezone">المنطقة الزمنية</label>
                        <input id="timezone" name="timezone" value="{{ old('timezone', 'Africa/Cairo') }}">
                    </div>
                </div>
            </section>
        </div>

        <section class="card section">
            <span class="tag">3. Theme install</span>
            <h2 style="margin-top:10px">اختار الثيم المناسب لنوع التطبيق</h2>
            <div class="grid three" style="margin-top:14px">
                @foreach ($themes as $theme)
                    <label class="choice">
                        <input type="radio" name="theme_slug" value="{{ $theme->slug }}" @checked(old('theme_slug', $themes->first()?->slug) === $theme->slug) required>
                        <article class="card theme" data-score="{{ $theme->performance_score ?? 90 }}">
                            <span class="tag">{{ implode(' / ', $theme->app_types ?? []) }}</span>
                            <h3>{{ $theme->name }}</h3>
                            <p>{{ $theme->category }} - {{ $theme->publisher }} - {{ $theme->price_type }}</p>
                        </article>
                    </label>
                @endforeach
            </div>
        </section>

        <section class="card section">
            <span class="tag">4. Profile capabilities</span>
            <h2 style="margin-top:10px">قدرات إضافية على ملف العميل</h2>
            <div class="grid two">
                <div class="checks">
                    <label class="check"><input type="checkbox" name="developer_creator" value="1" @checked(old('developer_creator'))> <span>أريد مسار Developer / Creator لبناء وبيع ثيمات أو إضافات لاحقًا.</span></label>
                    <label class="check"><input type="checkbox" name="marketer_partner" value="1" @checked(old('marketer_partner'))> <span>أريد مسار Marketer / Partner للإحالات والحملات.</span></label>
                    <label class="check"><input type="checkbox" name="implementation_builder" value="1" @checked(old('implementation_builder'))> <span>أريد أن أكون Kabeeri Builder يساعد العملاء في بناء تطبيقاتهم.</span></label>
                    <label class="check"><input type="checkbox" name="needs_builder_help" value="1" @checked(old('needs_builder_help', $selectedPath === 'needs_builder'))> <span>أحتاج Kabeeri Builder يساعدني في بناء التطبيق والشركة والمحتوى.</span></label>
                </div>
                <div class="field">
                    <label for="builder_request_note">ملاحظات للـ Builder</label>
                    <textarea id="builder_request_note" name="builder_request_note" placeholder="اكتب ما تحتاجه، نوع المشروع، وما الذي تريد إنجازه أولاً.">{{ old('builder_request_note') }}</textarea>
                </div>
            </div>
            <button class="primary" type="submit">تثبيت الثيم وتفعيل التطبيق</button>
        </section>
    </form>
</div>
</body>
</html>
