<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Localization\KabeeriLocale::direction() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('kabeeri.brand.name') }} | {{ $mode === 'register' ? 'ابدأ الآن' : 'دخول' }}</title>
    @include('customer.v16-style')
</head>
<body>
@php
    $isRegister = $mode === 'register';
@endphp
<div class="shell">
    <header class="top">
        <a class="brand" href="{{ route('customer.start') }}">
            <span class="mark">K</span>
            <span><strong>{{ __('kabeeri.brand.name') }}</strong><small>{{ $isRegister ? 'ابدأ الآن' : 'دخول' }}</small></span>
        </a>
        <nav class="nav">
            <a href="{{ route('customer.start') }}"><x-kabeeri-icon name="home" />البداية</a>
            <a class="{{ $isRegister ? '' : 'primary' }}" href="{{ route('login') }}"><x-kabeeri-icon name="login" />دخول</a>
            <a class="{{ $isRegister ? 'primary' : '' }}" href="{{ route('register') }}"><x-kabeeri-icon name="user-plus" />إنشاء حساب</a>
            @include('components.language-switcher', ['context' => 'visitor'])
        </nav>
    </header>

    <main class="split">
        <section class="card">
            @if (session('status'))
                <p class="notice">{{ session('status') }}</p>
            @endif
            @if ($errors->any())
                <div class="error">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if ($isRegister)
                <span class="tag"><x-kabeeri-icon name="user-plus" />ابدأ الآن</span>
                <h1 class="page-title" style="margin-bottom:14px">إنشاء حساب عميل جديد</h1>
                <form method="POST" action="{{ route('register.store') }}">
                    @csrf
                    <div class="field">
                        <label for="name">الاسم</label>
                        <input id="name" name="name" value="{{ old('name') }}" required autocomplete="name">
                    </div>
                    <div class="field">
                        <label for="email">البريد الإلكتروني</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                    </div>
                    <div class="field">
                        <label for="customer_path">المسار</label>
                        <select id="customer_path" name="customer_path">
                            @foreach ($paths as $key => $path)
                                <option value="{{ $key }}" @selected(old('customer_path', $selectedPath ?? 'business_owner') === $key)>{{ $path['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid two">
                        <div class="field">
                            <label for="password">كلمة المرور</label>
                            <input id="password" type="password" name="password" required autocomplete="new-password">
                        </div>
                        <div class="field">
                            <label for="password_confirmation">تأكيد كلمة المرور</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
                        </div>
                    </div>
                    <button class="primary" type="submit"><x-kabeeri-icon name="rocket" />إنشاء حساب والانتقال إلى Onboarding</button>
                </form>
            @else
                <span class="tag"><x-kabeeri-icon name="login" />دخول</span>
                <h1 class="page-title" style="margin-bottom:14px">دخول العميل</h1>
                <form method="POST" action="{{ route('login.store') }}">
                    @csrf
                    <div class="field">
                        <label for="email">البريد الإلكتروني</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                    </div>
                    <div class="field">
                        <label for="password">كلمة المرور</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password">
                    </div>
                    <label class="check"><input type="checkbox" name="remember" value="1"> <span>تذكرني على هذا الجهاز</span></label>
                    <button class="primary" type="submit"><x-kabeeri-icon name="apps" />دخول إلى لوحة إدارة التطبيقات</button>
                </form>
            @endif
        </section>

        <aside class="card dark">
            <span class="kicker"><x-kabeeri-icon name="account" />{{ __('kabeeri.brand.name') }}</span>
            <h1 class="page-title">{{ $isRegister ? 'افتح حسابك وابدأ بناء التطبيق.' : 'ادخل على مساحة عملك.' }}</h1>
            <p class="lead">بعد الدخول نكمل الإعداد أو نفتح لوحة إدارة التطبيقات حسب حالتك.</p>
        </aside>
    </main>
</div>
</body>
</html>
