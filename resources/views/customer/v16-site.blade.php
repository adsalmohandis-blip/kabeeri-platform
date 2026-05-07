<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Localization\KabeeriLocale::direction() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $site->name }} | {{ __('kabeeri.ui.app_detail') }}</title>
    @include('customer.v16-style')
</head>
<body>
@php
    $appType = $site->metadata['v16_app_type'] ?? $site->site_type;
    $themeSlug = $site->theme?->slug;
@endphp
<div class="shell">
    <header class="top">
        <a class="brand" href="{{ route('customer.workspace') }}">
            <span class="mark">{{ __('kabeeri.brand.mark') }}</span>
            <span><strong>{{ $site->name }}</strong><small>{{ __('kabeeri.ui.app_detail') }}</small></span>
        </a>
        <nav class="nav" aria-label="{{ __('kabeeri.ui.dashboard_nav') }}">
            <a href="{{ route('customer.workspace') }}"><x-kabeeri-icon name="apps" />{{ __('kabeeri.ui.apps_dashboard') }}</a>
            <a href="{{ route('customer.start') }}"><x-kabeeri-icon name="home" />{{ __('kabeeri.ui.home') }}</a>
            @include('components.language-switcher', ['context' => 'customer'])
        </nav>
    </header>

    <main class="hero">
        <section>
            <span class="kicker"><x-kabeeri-icon name="check-circle" />{{ __('kabeeri.ui.app_active') }}</span>
            <h1>{{ $site->name }}</h1>
            <p class="lead">{{ __('kabeeri.ui.app_enabled_with_theme') }}</p>
            <div class="grid four">
                <div class="metric"><strong>{{ $site->username }}</strong><span>{{ __('kabeeri.ui.username') }}</span></div>
                <div class="metric"><strong>{{ __('kabeeri.customer.app_types.'.$appType.'.label') }}</strong><span>{{ __('kabeeri.ui.site_type') }}</span></div>
                <div class="metric"><strong>{{ __('kabeeri.customer.app_types.'.$appType.'.label') }}</strong><span>{{ __('kabeeri.ui.app_type') }}</span></div>
                <div class="metric"><strong>{{ __('kabeeri.ui.active') }}</strong><span>{{ __('kabeeri.ui.theme_status') }}</span></div>
            </div>
        </section>
        <aside class="card dark">
            <h2>{{ $themeSlug ? __('kabeeri.customer.themes.'.$themeSlug.'.name') : __('kabeeri.ui.no_theme') }}</h2>
            <p>{{ $themeSlug ? __('kabeeri.customer.themes.'.$themeSlug.'.category') : __('kabeeri.ui.no_theme') }}</p>
            <div class="list" style="margin-top:14px">
                <div><span>{{ __('kabeeri.ui.installed_at') }}</span><small>{{ $site->metadata['theme_installed_at'] ?? '-' }}</small></div>
                <div><span>{{ __('kabeeri.ui.customer_path') }}</span><small>{{ __('kabeeri.customer.paths.'.($site->metadata['v16_customer_path'] ?? 'business_owner').'.label') }}</small></div>
            </div>
        </aside>
    </main>

    <section class="grid two section">
        <div class="card">
            <span class="tag"><x-kabeeri-icon name="settings" />{{ __('kabeeri.ui.theme_settings') }}</span>
            <h2 style="margin-top:10px">{{ __('kabeeri.ui.theme_settings') }}</h2>
            <div class="list">
                @forelse ($site->themeSettings as $setting)
                    <div><span>{{ $setting->key }}</span><small>{{ json_encode($setting->value['value'] ?? $setting->value, JSON_UNESCAPED_UNICODE) }}</small></div>
                @empty
                    <div><span>{{ __('kabeeri.ui.no_settings') }}</span><small>{{ __('kabeeri.ui.settings_later') }}</small></div>
                @endforelse
            </div>
        </div>
        <div class="card">
            <span class="tag"><x-kabeeri-icon name="document" />{{ __('kabeeri.ui.starter_content') }}</span>
            <h2 style="margin-top:10px">{{ __('kabeeri.ui.starter_content') }}</h2>
            <div class="list">
                @forelse ($site->contentEntries as $entry)
                    <div><span>{{ $entry->title }}</span><small>{{ $entry->slug }} / {{ $entry->status }}</small></div>
                @empty
                    <div><span>{{ __('kabeeri.ui.no_content') }}</span><small>{{ __('kabeeri.ui.content_later') }}</small></div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="card section">
        <span class="tag"><x-kabeeri-icon name="theme" />{{ __('kabeeri.ui.compatible_themes') }}</span>
        <h2 style="margin-top:10px">{{ __('kabeeri.ui.compatible_themes') }}</h2>
        <div class="grid three" style="margin-top:14px">
            @foreach ($themes as $theme)
                <article class="card theme" data-score="{{ $theme->performance_score ?? 90 }}">
                    <span class="tag"><x-kabeeri-icon name="theme" />{{ collect($theme->app_types ?? [])->map(fn ($type) => __('kabeeri.customer.app_types.'.$type.'.label'))->implode(' / ') }}</span>
                    <h3>{{ __('kabeeri.customer.themes.'.$theme->slug.'.name') }}</h3>
                    <p>{{ __('kabeeri.customer.themes.'.$theme->slug.'.category') }}</p>
                </article>
            @endforeach
        </div>
    </section>
</div>
</body>
</html>
