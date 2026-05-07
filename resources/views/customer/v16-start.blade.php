<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Localization\KabeeriLocale::direction() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('kabeeri.brand.name') }} | {{ __('kabeeri.ui.start_now') }}</title>
    @include('customer.v16-style')
</head>
<body>
<div class="shell">
    <header class="top">
        <a class="brand" href="{{ route('home') }}">
            <span class="mark">{{ __('kabeeri.brand.mark') }}</span>
            <span><strong>{{ __('kabeeri.brand.name') }}</strong><small>{{ __('kabeeri.ui.start_now') }}</small></span>
        </a>
        <nav class="nav">
            <a href="{{ route('login') }}"><x-kabeeri-icon name="login" />{{ __('kabeeri.ui.login') }}</a>
            <a class="primary" href="{{ route('register') }}"><x-kabeeri-icon name="user-plus" />{{ __('kabeeri.ui.start_now') }}</a>
            <a href="{{ route('public.landing') }}"><x-kabeeri-icon name="info" />{{ __('kabeeri.ui.features') }}</a>
            @include('components.language-switcher', ['context' => 'visitor'])
        </nav>
    </header>

    <main class="hero">
        <section>
            <span class="kicker"><x-kabeeri-icon name="steps" />{{ __('kabeeri.ui.start_now') }}</span>
            <h1>{{ __('kabeeri.ui.start_title') }}</h1>
            <p class="lead">{{ __('kabeeri.ui.start_lead') }}</p>
            <div class="nav">
                <a class="button primary" href="{{ route('register') }}"><x-kabeeri-icon name="rocket" />{{ __('kabeeri.ui.start_now') }}</a>
                <a class="button" href="#paths"><x-kabeeri-icon name="map" />{{ __('kabeeri.ui.see_paths') }}</a>
                <a class="button" href="#themes"><x-kabeeri-icon name="theme" />{{ __('kabeeri.ui.explore_themes') }}</a>
            </div>
        </section>
        <aside class="card dark" id="quick-register">
            @auth
                <h2>{{ __('kabeeri.ui.already_signed_in') }}</h2>
                <p>{{ __('kabeeri.ui.open_or_logout') }}</p>
                <div class="nav" style="margin-top:14px">
                    <a class="button primary" href="{{ route('customer.workspace') }}"><x-kabeeri-icon name="apps" />{{ __('kabeeri.ui.open_dashboard') }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"><x-kabeeri-icon name="logout" />{{ __('kabeeri.ui.logout_new_account') }}</button>
                    </form>
                </div>
            @else
                <h2>{{ __('kabeeri.ui.new_account') }}</h2>
                <p>{{ __('kabeeri.ui.after_account') }}</p>
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
                        <label for="quick_name">{{ __('kabeeri.ui.name') }}</label>
                        <input id="quick_name" name="name" value="{{ old('name') }}" required autocomplete="name">
                    </div>
                    <div class="field">
                        <label for="quick_email">{{ __('kabeeri.ui.email') }}</label>
                        <input id="quick_email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                    </div>
                    <div class="field">
                        <label for="quick_customer_path">{{ __('kabeeri.ui.path') }}</label>
                        <select id="quick_customer_path" name="customer_path">
                            @foreach ($paths as $key => $path)
                                <option value="{{ $key }}" @selected(old('customer_path', $selectedPath) === $key)>{{ __('kabeeri.customer.paths.'.$key.'.label') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid two">
                        <div class="field">
                            <label for="quick_password">{{ __('kabeeri.ui.password') }}</label>
                            <input id="quick_password" type="password" name="password" required autocomplete="new-password">
                        </div>
                        <div class="field">
                            <label for="quick_password_confirmation">{{ __('kabeeri.ui.password_confirmation') }}</label>
                            <input id="quick_password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
                        </div>
                    </div>
                    <button class="primary" type="submit"><x-kabeeri-icon name="user-plus" />{{ __('kabeeri.ui.create_and_start') }}</button>
                </form>
            @endauth
        </aside>
    </main>

    <section class="section" id="paths">
        <div class="grid three">
            @foreach ($paths as $key => $path)
                <article class="card">
                    <span class="tag"><x-kabeeri-icon name="map" />{{ __('kabeeri.customer.paths.'.$key.'.label') }}</span>
                    <h3>{{ __('kabeeri.customer.paths.'.$key.'.label') }}</h3>
                    <p>{{ __('kabeeri.customer.paths.'.$key.'.headline') }}</p>
                    <div class="nav" style="margin-top:14px">
                        <a class="button primary" href="{{ route('customer.start', ['path' => $key]) }}#quick-register"><x-kabeeri-icon name="check-circle" />{{ __('kabeeri.ui.choose_and_start') }}</a>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section" id="themes">
        <div class="card">
            <div class="top" style="margin:0 0 14px; box-shadow:none">
                <div>
                    <span class="kicker"><x-kabeeri-icon name="theme" />{{ __('kabeeri.ui.themes') }}</span>
                    <h2 style="margin:8px 0 0">{{ __('kabeeri.ui.available_themes') }}</h2>
                </div>
                <nav class="nav">
                    <a class="{{ blank($selectedAppType) ? 'primary' : '' }}" href="{{ route('customer.start') }}#themes">{{ __('kabeeri.ui.all') }}</a>
                    @foreach ($appTypes as $key => $type)
                        <a class="{{ $selectedAppType === $key ? 'primary' : '' }}" href="{{ route('customer.start', ['app_type' => $key]) }}#themes">{{ __('kabeeri.customer.app_types.'.$key.'.label') }}</a>
                    @endforeach
                </nav>
            </div>
            <div class="grid three">
                @foreach ($themes as $theme)
                    <article class="card theme" data-score="{{ $theme->performance_score ?? 90 }}">
                        <span class="tag">{{ collect($theme->app_types ?? [])->map(fn ($type) => __('kabeeri.customer.app_types.'.$type.'.label'))->implode(' / ') }}</span>
                        <h3>{{ __('kabeeri.customer.themes.'.$theme->slug.'.name') }}</h3>
                        <p>{{ __('kabeeri.customer.themes.'.$theme->slug.'.category') }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
</div>
</body>
</html>
