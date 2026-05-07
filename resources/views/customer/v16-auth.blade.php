<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Localization\KabeeriLocale::direction() }}" data-kbr-theme="{{ $kabeeriUi['theme'] ?? 'light' }}" data-kbr-font="{{ $kabeeriUi['font'] ?? 'ibm-plex-sans-arabic' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('kabeeri.brand.name') }} | {{ $mode === 'register' ? __('kabeeri.ui.start_now') : __('kabeeri.ui.login') }}</title>
    @include('customer.v16-style')
</head>
<body>
@php
    $isRegister = $mode === 'register';
@endphp
<div class="shell">
    <header class="top">
        <a class="brand" href="{{ route('customer.start') }}">
            <span class="mark">{{ __('kabeeri.brand.mark') }}</span>
            <span><strong>{{ __('kabeeri.brand.name') }}</strong><small>{{ $isRegister ? __('kabeeri.ui.start_now') : __('kabeeri.ui.login') }}</small></span>
        </a>
        <nav class="nav" aria-label="{{ __('kabeeri.ui.public_home') }}">
            <a href="{{ route('customer.start') }}"><x-kabeeri-icon name="home" />{{ __('kabeeri.ui.home') }}</a>
            <a class="{{ $isRegister ? '' : 'primary' }}" href="{{ route('login') }}"><x-kabeeri-icon name="login" />{{ __('kabeeri.ui.login') }}</a>
            <a class="{{ $isRegister ? 'primary' : '' }}" href="{{ route('register') }}"><x-kabeeri-icon name="user-plus" />{{ __('kabeeri.ui.create_account') }}</a>
            @include('components.language-switcher', ['context' => 'platform_public'])
                @include('components.theme-switcher', ['context' => 'platform_public'])
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
                <span class="tag"><x-kabeeri-icon name="user-plus" />{{ __('kabeeri.ui.start_now') }}</span>
                <h1 class="page-title" style="margin-bottom:14px">{{ __('kabeeri.ui.register_title') }}</h1>
                <form method="POST" action="{{ route('register.store') }}">
                    @csrf
                    <div class="field">
                        <label for="name">{{ __('kabeeri.ui.name') }}</label>
                        <input id="name" name="name" value="{{ old('name') }}" required autocomplete="name">
                    </div>
                    <div class="field">
                        <label for="email">{{ __('kabeeri.ui.email') }}</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                    </div>
                    <div class="field">
                        <label for="customer_path">{{ __('kabeeri.ui.path') }}</label>
                        <select id="customer_path" name="customer_path">
                            @foreach ($paths as $key => $path)
                                <option value="{{ $key }}" @selected(old('customer_path', $selectedPath ?? 'business_owner') === $key)>{{ __('kabeeri.customer.paths.'.$key.'.label') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid two">
                        <div class="field">
                            <label for="password">{{ __('kabeeri.ui.password') }}</label>
                            <input id="password" type="password" name="password" required autocomplete="new-password">
                        </div>
                        <div class="field">
                            <label for="password_confirmation">{{ __('kabeeri.ui.password_confirmation') }}</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
                        </div>
                    </div>
                    <button class="primary" type="submit"><x-kabeeri-icon name="rocket" />{{ __('kabeeri.ui.create_and_start') }}</button>
                </form>
            @else
                <span class="tag"><x-kabeeri-icon name="login" />{{ __('kabeeri.ui.login') }}</span>
                <h1 class="page-title" style="margin-bottom:14px">{{ __('kabeeri.ui.login_title') }}</h1>
                <form method="POST" action="{{ route('login.store') }}">
                    @csrf
                    <div class="field">
                        <label for="email">{{ __('kabeeri.ui.email') }}</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                    </div>
                    <div class="field">
                        <label for="password">{{ __('kabeeri.ui.password') }}</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password">
                    </div>
                    <label class="check"><input type="checkbox" name="remember" value="1"> <span>{{ __('kabeeri.ui.remember_me') }}</span></label>
                    <button class="primary" type="submit"><x-kabeeri-icon name="apps" />{{ __('kabeeri.ui.login_to_dashboard') }}</button>
                </form>
            @endif
        </section>

        <aside class="card dark">
            <span class="kicker"><x-kabeeri-icon name="account" />{{ __('kabeeri.brand.name') }}</span>
            <h1 class="page-title">{{ $isRegister ? __('kabeeri.ui.open_account_title') : __('kabeeri.ui.enter_workspace_title') }}</h1>
            <p class="lead">{{ __('kabeeri.ui.after_login_text') }}</p>
        </aside>
    </main>
</div>
</body>
</html>
