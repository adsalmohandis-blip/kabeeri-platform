<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Localization\KabeeriLocale::direction() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('kabeeri.brand.name') }} Customer Start</title>
    @include('customer.v16-style')
</head>
<body>
<div class="shell">
    <header class="top">
        <a class="brand" href="{{ route('home') }}">
            <span class="mark">K</span>
            <span><strong>{{ __('kabeeri.brand.name') }} Customer Start</strong><small>مدخل العميل العام</small></span>
        </a>
        <nav class="nav">
            <a href="{{ route('login') }}"><x-kabeeri-icon name="login" />دخول</a>
            <a class="primary" href="{{ route('register') }}"><x-kabeeri-icon name="user-plus" />إنشاء حساب</a>
            <a href="{{ route('public.landing') }}"><x-kabeeri-icon name="info" />عن المنصة</a>
            @include('components.language-switcher', ['context' => 'visitor'])
        </nav>
    </header>

    <main class="hero">
        <section>
            <span class="kicker"><x-kabeeri-icon name="steps" />V16 Customer Onboarding</span>
            <h1>ابدأ من المسار المناسب، ثم ابن موقعك أو متجرك أو تطبيقك.</h1>
            <p class="lead">اختر مسارك، نوع التطبيق، والثيم المناسب. بعدها نفتح لك لوحة إدارة التطبيقات.</p>
            <div class="nav">
                <a class="button primary" href="{{ route('register') }}"><x-kabeeri-icon name="rocket" />ابدأ الآن</a>
                <a class="button" href="#paths"><x-kabeeri-icon name="map" />شاهد المسارات</a>
                <a class="button" href="#themes"><x-kabeeri-icon name="theme" />استكشف الثيمات</a>
            </div>
        </section>
        <aside class="card dark" id="quick-register">
            @auth
                <h2>أنت داخل بالفعل</h2>
                <p>افتح لوحة إدارة التطبيقات أو اخرج لإنشاء حساب جديد.</p>
                <div class="nav" style="margin-top:14px">
                    <a class="button primary" href="{{ route('customer.workspace') }}"><x-kabeeri-icon name="apps" />افتح لوحة إدارة التطبيقات</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"><x-kabeeri-icon name="logout" />خروج وإنشاء حساب جديد</button>
                    </form>
                </div>
            @else
                <h2>إنشاء حساب عميل جديد</h2>
                <p>بعد الحساب نكمل اختيار التطبيق والثيم.</p>
                @if ($errors->any())
                    <div class="error" style="color:#fffaf0">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif
                <form method="POST" action="{{ route('register.store') }}" style="margin-top:14px">
                    @csrf
                    <div class="field">
                        <label for="quick_name">الاسم</label>
                        <input id="quick_name" name="name" value="{{ old('name') }}" required autocomplete="name">
                    </div>
                    <div class="field">
                        <label for="quick_email">البريد الإلكتروني</label>
                        <input id="quick_email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                    </div>
                    <div class="field">
                        <label for="quick_customer_path">المسار</label>
                        <select id="quick_customer_path" name="customer_path">
                            @foreach ($paths as $key => $path)
                                <option value="{{ $key }}" @selected(old('customer_path', $selectedPath) === $key)>{{ $path['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid two">
                        <div class="field">
                            <label for="quick_password">كلمة المرور</label>
                            <input id="quick_password" type="password" name="password" required autocomplete="new-password">
                        </div>
                        <div class="field">
                            <label for="quick_password_confirmation">تأكيد كلمة المرور</label>
                            <input id="quick_password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
                        </div>
                    </div>
                    <button class="primary" type="submit"><x-kabeeri-icon name="user-plus" />إنشاء الحساب وبدء Onboarding</button>
                </form>
            @endauth
        </aside>
    </main>

    <section class="section" id="paths">
        <div class="grid three">
            @foreach ($paths as $key => $path)
                <article class="card">
                    <span class="tag"><x-kabeeri-icon name="map" />{{ $key }}</span>
                    <h3>{{ $path['label'] }}</h3>
                    <p>{{ $path['headline'] }}</p>
                    <div class="nav" style="margin-top:14px">
                        <a class="button primary" href="{{ route('customer.start', ['path' => $key]) }}#quick-register"><x-kabeeri-icon name="check-circle" />اختار هذا المسار وأنشئ حساب</a>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section" id="themes">
        <div class="card">
            <div class="top" style="margin:0 0 14px; box-shadow:none">
                <div>
                    <span class="kicker"><x-kabeeri-icon name="theme" />Theme marketplace preview</span>
                    <h2 style="margin:8px 0 0">ثيمات متاحة حسب نوع التطبيق</h2>
                </div>
                <nav class="nav">
                    <a class="{{ blank($selectedAppType) ? 'primary' : '' }}" href="{{ route('customer.start') }}#themes">الكل</a>
                    @foreach ($appTypes as $key => $type)
                        <a class="{{ $selectedAppType === $key ? 'primary' : '' }}" href="{{ route('customer.start', ['app_type' => $key]) }}#themes">{{ $type['label'] }}</a>
                    @endforeach
                </nav>
            </div>
            <div class="grid three">
                @foreach ($themes as $theme)
                    <article class="card theme" data-score="{{ $theme->performance_score ?? 90 }}">
                        <span class="tag">{{ implode(' / ', $theme->app_types ?? []) }}</span>
                        <h3>{{ $theme->name }}</h3>
                        <p>{{ $theme->category }} - {{ $theme->publisher }} - {{ $theme->price_type }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
</div>
</body>
</html>
