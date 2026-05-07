@php
    $activeNav = $mode === 'create' ? 'create' : 'apps';
    $formAction = $mode === 'create'
        ? route('customer.apps.store')
        : route('customer.apps.update', ['username' => $site->username]);
    $title = $mode === 'create' ? __('kabeeri.ui.create_app') : __('kabeeri.ui.edit_app');
    $siteMetadata = $site?->metadata ?? [];
    $supportedLocales = \App\Support\Localization\KabeeriLocale::supported();
    $uiThemes = config('kabeeri_ui_preferences.themes', []);
    $uiFonts = config('kabeeri_ui_preferences.fonts', []);
    $selectedPublicLanguage = old('public_language', $site?->language ?? $siteMetadata['public_language'] ?? app()->getLocale());
    $selectedPublicTheme = old('public_theme_mode', $siteMetadata['public_theme_mode'] ?? config('kabeeri_ui_preferences.default_theme', 'light'));
    $selectedPublicFont = old('public_font', $siteMetadata['public_font'] ?? config('kabeeri_ui_preferences.default_font', 'ibm-plex-sans-arabic'));
@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Localization\KabeeriLocale::direction() }}" data-kbr-theme="{{ $kabeeriUi['theme'] ?? 'light' }}" data-kbr-font="{{ $kabeeriUi['font'] ?? 'ibm-plex-sans-arabic' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | {{ __('kabeeri.brand.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#fffaf0] text-[#17130d] antialiased">
    <div class="kbr-customer-shell grid min-h-screen w-full lg:grid-cols-[16rem_minmax(0,1fr)]">
        @include('customer.partials.dashboard-sidebar', ['dashboard' => $dashboard, 'activeNav' => $activeNav])

        <main class="min-w-0 px-4 py-4 sm:px-6 lg:px-8 lg:py-6">
            @include('customer.partials.dashboard-header', ['title' => $title])

            <form method="POST" action="{{ $formAction }}" class="rounded-[1.7rem] border border-[#17130d]/10 bg-[#fffaf0]/92 p-4 shadow-[0_18px_55px_rgba(23,19,13,.08)]">
                @csrf
                @if ($mode === 'edit')
                    @method('PUT')
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-2xl border border-[#c98a2e]/30 bg-white/70 p-3 text-xs font-black text-[#17130d]">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @if ($mode === 'create')
                    <input type="hidden" name="organization_id" value="{{ $dashboard['active_organization']?->id }}">
                @endif

                <div class="grid gap-4 md:grid-cols-2">
                    <label class="grid gap-1 text-xs font-black">
                        {{ __('kabeeri.ui.site_name') }}
                        <input name="site_name" value="{{ old('site_name', $site?->name) }}" required class="rounded-2xl border border-[#17130d]/10 bg-white/80 px-3 py-3 text-sm outline-none focus:border-[#c98a2e]">
                    </label>
                    <label class="grid gap-1 text-xs font-black">
                        {{ __('kabeeri.ui.username') }}
                        <input name="username" value="{{ old('username', $site?->username) }}" placeholder="my-app" class="rounded-2xl border border-[#17130d]/10 bg-white/80 px-3 py-3 text-sm outline-none focus:border-[#c98a2e]">
                    </label>
                    <label class="grid gap-1 text-xs font-black">
                        {{ __('kabeeri.ui.app_type') }}
                        <select name="app_type" class="rounded-2xl border border-[#17130d]/10 bg-white/80 px-3 py-3 text-sm outline-none focus:border-[#c98a2e]">
                            @foreach ($appTypes as $key => $type)
                                <option value="{{ $key }}" @selected(old('app_type', $site?->metadata['v16_app_type'] ?? $selectedAppType) === $key)>{{ __('kabeeri.customer.app_types.'.$key.'.label') }}</option>
                            @endforeach
                        </select>
                    </label>
                    @if ($mode === 'edit')
                        <label class="grid gap-1 text-xs font-black">
                            {{ __('kabeeri.ui.state_ready') }}
                            <select name="status" class="rounded-2xl border border-[#17130d]/10 bg-white/80 px-3 py-3 text-sm outline-none focus:border-[#c98a2e]">
                                <option value="active" @selected(old('status', $site->status) === 'active')>{{ __('kabeeri.ui.active') }}</option>
                                <option value="paused" @selected(old('status', $site->status) === 'paused')>{{ __('kabeeri.ui.paused') }}</option>
                            </select>
                        </label>
                    @else
                        <label class="grid gap-1 text-xs font-black">
                            {{ __('kabeeri.ui.theme') }}
                            <select name="theme_slug" class="rounded-2xl border border-[#17130d]/10 bg-white/80 px-3 py-3 text-sm outline-none focus:border-[#c98a2e]">
                                @foreach ($themes as $theme)
                                    <option value="{{ $theme->slug }}">{{ __('kabeeri.customer.themes.'.$theme->slug.'.name') }}</option>
                                @endforeach
                            </select>
                        </label>
                    @endif
                    <label class="grid gap-1 text-xs font-black">
                        {{ __('kabeeri.ui.public_language') }}
                        <select name="public_language" class="rounded-2xl border border-[#17130d]/10 bg-white/80 px-3 py-3 text-sm outline-none focus:border-[#c98a2e]">
                            @foreach ($supportedLocales as $locale => $language)
                                <option value="{{ $locale }}" @selected($selectedPublicLanguage === $locale)>{{ $language['native_label'] }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="grid gap-1 text-xs font-black">
                        {{ __('kabeeri.ui.public_theme_mode') }}
                        <select name="public_theme_mode" class="rounded-2xl border border-[#17130d]/10 bg-white/80 px-3 py-3 text-sm outline-none focus:border-[#c98a2e]">
                            @foreach ($uiThemes as $themeMode => $themeItem)
                                @php($themeLabelKey = 'theme_mode_'.$themeMode)
                                <option value="{{ $themeMode }}" @selected($selectedPublicTheme === $themeMode)>{{ __("kabeeri.ui.{$themeLabelKey}") }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="grid gap-1 text-xs font-black">
                        {{ __('kabeeri.ui.public_font') }}
                        <select name="public_font" class="rounded-2xl border border-[#17130d]/10 bg-white/80 px-3 py-3 text-sm outline-none focus:border-[#c98a2e]">
                            @foreach ($uiFonts as $fontKey => $fontItem)
                                @php($fontLabelKey = 'font_'.str_replace('-', '_', $fontKey))
                                <option value="{{ $fontKey }}" @selected($selectedPublicFont === $fontKey)>{{ __("kabeeri.ui.{$fontLabelKey}") }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>

                <div class="mt-5 flex flex-wrap gap-2">
                    <button class="rounded-full bg-[#17130d] px-5 py-3 text-xs font-black text-[#fffaf0]" type="submit">{{ $mode === 'create' ? __('kabeeri.ui.create_app') : __('kabeeri.ui.save_app') }}</button>
                    <a href="{{ route('customer.apps.index') }}" class="rounded-full bg-white px-5 py-3 text-xs font-black text-[#17130d] ring-1 ring-[#17130d]/10">{{ __('kabeeri.ui.apps_manage') }}</a>
                </div>
            </form>

            @if ($mode === 'edit')
                <form method="POST" action="{{ route('customer.apps.destroy', ['username' => $site->username]) }}" class="mt-5 rounded-[1.7rem] border border-[#17130d]/10 bg-white/70 p-4">
                    @csrf
                    @method('DELETE')
                    <h2 class="text-sm font-black">{{ __('kabeeri.ui.delete_app') }}</h2>
                    <div class="mt-3 flex flex-wrap items-end gap-3">
                        <label class="grid gap-1 text-xs font-black">
                            {{ __('kabeeri.ui.retention_days') }}
                            <select name="retention_days" class="rounded-2xl border border-[#17130d]/10 bg-[#fffaf0] px-3 py-2">
                                <option value="30">30</option>
                                <option value="60">60</option>
                                <option value="90">90</option>
                            </select>
                        </label>
                        <button class="rounded-full bg-[#17130d] px-5 py-3 text-xs font-black text-[#fffaf0]" type="submit">{{ __('kabeeri.ui.delete_app') }}</button>
                    </div>
                </form>
            @endif
        </main>
    </div>
</body>
</html>
