<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Localization\KabeeriLocale::direction() }}" data-kbr-theme="{{ $kabeeriUi['theme'] ?? 'light' }}" data-kbr-font="{{ $kabeeriUi['font'] ?? 'ibm-plex-sans-arabic' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('kabeeri.ui.onboarding') }} | {{ __('kabeeri.brand.name') }}</title>
    @include('customer.v16-style')
</head>
<body>
<div class="shell">
    <header class="top">
        <a class="brand" href="{{ route('customer.start') }}">
            <span class="mark">{{ __('kabeeri.brand.mark') }}</span>
            <span><strong>{{ __('kabeeri.ui.onboarding') }}</strong><small>{{ __('kabeeri.ui.onboarding_lead') }}</small></span>
        </a>
        <nav class="nav" aria-label="{{ __('kabeeri.ui.dashboard_nav') }}">
            <a href="{{ route('customer.start') }}"><x-kabeeri-icon name="home" />{{ __('kabeeri.ui.home') }}</a>
            <a href="{{ route('customer.workspace') }}"><x-kabeeri-icon name="apps" />{{ __('kabeeri.ui.apps_dashboard') }}</a>
            @include('components.language-switcher', ['context' => 'customer'])
            @include('components.theme-switcher', ['context' => 'customer'])
            @include('components.font-switcher', ['context' => 'customer'])
        </nav>
    </header>

    <main class="hero">
        <section>
            <span class="kicker"><x-kabeeri-icon name="steps" />{{ __('kabeeri.ui.step_count') }}</span>
            <h1>{{ __('kabeeri.ui.onboarding_title') }}</h1>
            <p class="lead">{{ __('kabeeri.ui.onboarding_lead') }}</p>
        </section>
        <aside class="card dark">
            <h2>{{ __('kabeeri.ui.after_activation') }}</h2>
            <p>{{ __('kabeeri.ui.after_activation_text') }}</p>
        </aside>
    </main>

    <form class="section" method="POST" action="{{ route('customer.onboarding.store') }}">
        @csrf
        <input type="hidden" name="language" value="{{ app()->getLocale() }}">
        <input type="hidden" name="timezone" value="{{ config('app.timezone', 'UTC') }}">
        @if ($errors->any())
            <div class="card error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="grid two">
            <section class="card">
                <span class="tag"><x-kabeeri-icon name="map" />{{ __('kabeeri.ui.audience_path') }}</span>
                <div class="field" style="margin-top:14px">
                    <label for="customer_path">{{ __('kabeeri.ui.audience_path') }}</label>
                    <select id="customer_path" name="customer_path">
                        @foreach ($paths as $key => $path)
                            <option value="{{ $key }}" @selected(old('customer_path', $selectedPath) === $key)>{{ __('kabeeri.customer.paths.'.$key.'.label') }} - {{ __('kabeeri.customer.paths.'.$key.'.headline') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="organization_name">{{ __('kabeeri.ui.organization_name') }}</label>
                    <input id="organization_name" name="organization_name" value="{{ old('organization_name', auth()->user()->name.' '.__('kabeeri.ui.workspace_default_suffix')) }}" required>
                </div>
                <div class="field">
                    <label for="company_name">{{ __('kabeeri.ui.company_name') }}</label>
                    <input id="company_name" name="company_name" value="{{ old('company_name') }}" placeholder="{{ __('kabeeri.ui.company_optional') }}">
                </div>
                <div class="field">
                    <label for="site_name">{{ __('kabeeri.ui.site_name') }}</label>
                    <input id="site_name" name="site_name" value="{{ old('site_name', auth()->user()->name.' '.__('kabeeri.ui.app_default_suffix')) }}" required>
                </div>
            </section>

            <section class="card">
                <span class="tag"><x-kabeeri-icon name="apps" />{{ __('kabeeri.ui.app_type') }}</span>
                <div class="nav" style="margin:14px 0">
                    @foreach ($appTypes as $key => $type)
                        <a class="{{ old('app_type', $selectedAppType) === $key ? 'primary' : '' }}" href="{{ route('customer.onboarding', ['path' => old('customer_path', $selectedPath), 'app_type' => $key]) }}">{{ __('kabeeri.customer.app_types.'.$key.'.label') }}</a>
                    @endforeach
                </div>
                <div class="field">
                    <label for="app_type">{{ __('kabeeri.ui.app_type') }}</label>
                    <select id="app_type" name="app_type">
                        @foreach ($appTypes as $key => $type)
                            <option value="{{ $key }}" @selected(old('app_type', $selectedAppType) === $key)>{{ __('kabeeri.customer.app_types.'.$key.'.label') }} - {{ __('kabeeri.customer.app_types.'.$key.'.intent') }}</option>
                        @endforeach
                    </select>
                </div>
            </section>
        </div>

        <section class="card section">
            <span class="tag"><x-kabeeri-icon name="theme" />{{ __('kabeeri.ui.theme_install') }}</span>
            <h2 style="margin-top:10px">{{ __('kabeeri.ui.choose_theme_for_app') }}</h2>
            <div class="grid three" style="margin-top:14px">
                @foreach ($themes as $theme)
                    <label class="choice">
                        <input type="radio" name="theme_slug" value="{{ $theme->slug }}" @checked(old('theme_slug', $themes->first()?->slug) === $theme->slug) required>
                        <article class="card theme" data-score="{{ $theme->performance_score ?? 90 }}">
                            <span class="tag"><x-kabeeri-icon name="theme" />{{ collect($theme->app_types ?? [])->map(fn ($type) => __('kabeeri.customer.app_types.'.$type.'.label'))->implode(' / ') }}</span>
                            <h3>{{ __('kabeeri.customer.themes.'.$theme->slug.'.name') }}</h3>
                            <p>{{ __('kabeeri.customer.themes.'.$theme->slug.'.category') }}</p>
                        </article>
                    </label>
                @endforeach
            </div>
        </section>

        <section class="card section">
            <span class="tag"><x-kabeeri-icon name="account" />{{ __('kabeeri.ui.profile_capabilities') }}</span>
            <h2 style="margin-top:10px">{{ __('kabeeri.ui.profile_capabilities') }}</h2>
            <div class="grid two">
                <div class="checks">
                    @foreach (['developer_creator', 'marketer_partner', 'implementation_builder', 'needs_builder_help'] as $capabilityKey)
                        <label class="check">
                            <input type="checkbox" name="{{ $capabilityKey }}" value="1" @checked(old($capabilityKey, $capabilityKey === 'needs_builder_help' && $selectedPath === 'needs_builder'))>
                            <span>{{ __('kabeeri.customer.capabilities.'.$capabilityKey.'.label') }}: {{ __('kabeeri.customer.capabilities.'.$capabilityKey.'.description') }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="field">
                    <label for="builder_request_note">{{ __('kabeeri.ui.builder_note') }}</label>
                    <textarea id="builder_request_note" name="builder_request_note" placeholder="{{ __('kabeeri.ui.builder_note_placeholder') }}">{{ old('builder_request_note') }}</textarea>
                </div>
            </div>
            <button class="primary" type="submit"><x-kabeeri-icon name="check-circle" />{{ __('kabeeri.ui.install_theme') }}</button>
        </section>
    </form>
</div>
</body>
</html>
