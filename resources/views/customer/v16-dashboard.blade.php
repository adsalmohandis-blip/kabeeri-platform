@php
    $user = $dashboard['user'];
    $profile = $dashboard['profile'];
    $activeOrganization = $dashboard['active_organization'];
    $sites = $dashboard['sites'];
    $firstSite = $sites->first();
    $capabilities = $dashboard['capabilities'];
    $capabilityValues = $profile->metadata['capabilities'] ?? ['customer_owner'];
    $activeThemeCount = $sites->filter(fn ($site) => filled($site->theme))->count();
    $workspaceReady = filled($activeOrganization);
    $sidebarItems = [
        ['label' => 'dashboard_overview', 'target' => route('customer.workspace'), 'icon' => 'chart', 'active' => true],
        ['label' => 'apps_manage', 'target' => route('customer.apps.index'), 'icon' => 'apps', 'active' => false],
        ['label' => 'new_app', 'target' => route('customer.apps.create'), 'icon' => 'plus', 'active' => false],
        ['label' => 'themes_plugins', 'target' => $firstSite ? route('customer.apps.plugins', ['username' => $firstSite->username]) : route('customer.apps.index'), 'icon' => 'plugin', 'active' => false],
        ['label' => 'trash', 'target' => route('customer.apps.trash'), 'icon' => 'trash', 'active' => false],
        ['label' => 'capabilities', 'target' => '#capabilities', 'icon' => 'account', 'active' => false],
    ];
    $nextActions = $workspaceReady
        ? [
            ['title' => 'review_first_app', 'text' => 'review_first_app_text', 'state' => 'state_ready'],
            ['title' => 'update_account_capabilities', 'text' => 'update_account_capabilities_text', 'state' => 'state_optional'],
            ['title' => 'choose_growth_addons', 'text' => 'choose_growth_addons_text', 'state' => 'state_next'],
        ]
        : [
            ['title' => 'start_onboarding', 'text' => 'start_onboarding_text', 'state' => 'state_required'],
            ['title' => 'choose_app_type', 'text' => 'choose_app_type_text', 'state' => 'state_required'],
            ['title' => 'install_suitable_theme', 'text' => 'install_suitable_theme_text', 'state' => 'state_required'],
        ];
@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Localization\KabeeriLocale::direction() }}" data-kbr-theme="{{ $kabeeriUi['theme'] ?? 'light' }}" data-kbr-font="{{ $kabeeriUi['font'] ?? 'ibm-plex-sans-arabic' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('kabeeri.ui.apps_dashboard') }} | {{ __('kabeeri.brand.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=almarai:400,700,800|ibm-plex-sans-arabic:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f1eadc] text-[#111111] antialiased">
    <div class="kbr-customer-shell grid min-h-screen w-full lg:grid-cols-[16rem_minmax(0,1fr)]">
        @include('customer.partials.dashboard-sidebar', ['dashboard' => $dashboard, 'activeNav' => 'overview'])

        <main class="min-w-0 px-4 py-4 sm:px-7 lg:px-10 lg:py-7">
            <header id="overview" class="relative z-[60] mb-6 rounded-[1.6rem] border border-[#111111]/10 bg-[#f1eadc]/90 px-4 py-3 backdrop-blur-xl">
                <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
                    <div class="flex min-w-0 items-center gap-3">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#111111] text-sm font-black text-[#f1eadc] lg:hidden">{{ __('kabeeri.brand.mark') }}</span>
                        <div class="min-w-0">
                            <p class="text-[10px] font-black uppercase tracking-[.18em] text-[#111111]/55">{{ __('kabeeri.brand.name') }}</p>
                            <h1 class="mt-1 truncate text-lg font-black tracking-[-.02em] sm:text-xl">{{ __('kabeeri.ui.apps_dashboard') }}</h1>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <details class="w-full sm:w-auto lg:hidden">
                            <summary class="cursor-pointer rounded-full bg-[#111111] px-4 py-2 text-center text-xs font-black text-[#f1eadc]">{{ __('kabeeri.ui.dashboard_nav') }}</summary>
                            <nav class="mt-2 grid gap-1 rounded-3xl border border-[#111111]/10 bg-[#f1eadc] p-2 sm:grid-cols-2" aria-label="{{ __('kabeeri.ui.dashboard_nav') }}">
                                @foreach ($sidebarItems as $item)
                                    <a href="{{ $item['target'] }}" class="flex items-center gap-2 rounded-2xl px-3 py-2 text-xs font-black {{ $item['active'] ? 'bg-[#111111] text-[#f1eadc]' : 'bg-white/60 text-[#111111]' }}"><x-kabeeri-icon name="{{ $item['icon'] }}" />{{ __('kabeeri.ui.'.$item['label']) }}</a>
                                @endforeach
                            </nav>
                        </details>
                        <a href="{{ route('customer.start') }}" class="inline-flex items-center rounded-full border border-[#111111]/10 bg-white/60 px-3 py-2 text-xs font-black text-[#111111]"><x-kabeeri-icon name="home" />{{ __('kabeeri.ui.home') }}</a>
                        <a href="{{ route('public.landing') }}" class="inline-flex items-center rounded-full border border-[#111111]/10 bg-white/60 px-3 py-2 text-xs font-black text-[#111111]"><x-kabeeri-icon name="info" />{{ __('kabeeri.ui.platform') }}</a>
                        @include('components.language-switcher', ['context' => 'customer'])
                        @include('components.theme-switcher', ['context' => 'customer'])
                        @include('components.font-switcher', ['context' => 'customer'])
                    </div>
                </div>
            </header>

            @if (session('status'))
                <div class="mb-5 rounded-2xl border border-[#111111]/10 bg-white/60 px-4 py-3 text-xs font-black text-[#111111]">{{ session('status') }}</div>
            @endif

            <section class="mb-7 grid gap-5 xl:grid-cols-[minmax(0,1fr)_22rem]">
                <div>
                    <p class="text-xs font-black uppercase tracking-[.18em] text-[#111111]/52">{{ __('kabeeri.ui.workspace_status') }}</p>
                    <h2 class="mt-3 max-w-3xl text-3xl font-black leading-[1.05] tracking-[-.055em] sm:text-5xl">{{ __('kabeeri.ui.'.($workspaceReady ? 'ready' : 'needs_setup')) }}</h2>
                    <p class="mt-4 max-w-2xl text-sm font-bold leading-7 text-[#111111]/62">{{ $activeOrganization?->name ?? __('kabeeri.ui.needs_setup_sentence') }}</p>
                    <div class="mt-5 flex flex-wrap gap-2">
                        <a href="{{ route('customer.apps.create') }}" class="inline-flex items-center justify-center rounded-full bg-[#111111] px-5 py-3 text-xs font-black text-[#f1eadc]"><x-kabeeri-icon name="plus" />{{ __('kabeeri.ui.new_app') }}</a>
                        <a href="{{ route('customer.apps.index') }}" class="inline-flex items-center justify-center rounded-full border border-[#111111]/10 bg-white/60 px-5 py-3 text-xs font-black text-[#111111]"><x-kabeeri-icon name="apps" />{{ __('kabeeri.ui.apps_manage') }}</a>
                    </div>
                </div>
                <aside class="rounded-[1.5rem] border border-[#111111]/10 bg-white/50 p-4">
                    <div class="grid gap-0 divide-y divide-[#111111]/10">
                        <div class="flex items-center justify-between py-3"><span class="text-xs font-black text-[#111111]/55">{{ __('kabeeri.ui.apps') }}</span><strong>{{ $sites->count() }}</strong></div>
                        <div class="flex items-center justify-between py-3"><span class="text-xs font-black text-[#111111]/55">{{ __('kabeeri.ui.theme') }}</span><strong>{{ $activeThemeCount }}</strong></div>
                        <div class="flex items-center justify-between py-3"><span class="text-xs font-black text-[#111111]/55">{{ __('kabeeri.ui.capabilities') }}</span><strong>{{ count($capabilityValues) }}</strong></div>
                    </div>
                </aside>
            </section>

            <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_23rem]">
                <div id="apps" class="rounded-[1.5rem] border border-[#111111]/10 bg-white/42 p-4">
                    <div class="mb-2 flex items-center justify-between gap-3">
                        <h2 class="text-base font-black tracking-[-.02em]">{{ __('kabeeri.ui.my_apps') }}</h2>
                        <a href="{{ route('customer.apps.create') }}" class="rounded-full bg-[#111111] px-4 py-2 text-xs font-black text-[#f1eadc]">{{ __('kabeeri.ui.new_app') }}</a>
                    </div>
                    <div class="divide-y divide-[#111111]/10">
                        @forelse ($sites as $site)
                            @php
                                $appType = $site->metadata['v16_app_type'] ?? $site->site_type;
                                $themeSlug = $site->theme?->slug;
                            @endphp
                            <a href="{{ route('customer.apps.show', ['username' => $site->username]) }}" class="grid gap-2 py-4 md:grid-cols-[minmax(0,1fr)_11rem_6rem] md:items-center">
                                <span class="min-w-0">
                                    <strong class="block truncate text-sm font-black">{{ $site->name }}</strong>
                                    <small class="mt-1 block truncate text-xs font-bold text-[#111111]/55">{{ __('kabeeri.customer.app_types.'.$appType.'.label') }} / {{ __('kabeeri.ui.username') }}: {{ $site->username }}</small>
                                </span>
                                <span class="text-xs font-black text-[#111111]/62">{{ $themeSlug ? __('kabeeri.customer.themes.'.$themeSlug.'.name') : __('kabeeri.ui.no_theme') }}</span>
                                <span class="text-xs font-black text-[#111111] md:text-end">{{ __('kabeeri.ui.active') }}</span>
                            </a>
                        @empty
                            <div class="py-8 text-center">
                                <p class="text-sm font-black">{{ __('kabeeri.ui.no_app') }}</p>
                                <p class="mx-auto mt-1 max-w-sm text-xs leading-6 text-[#111111]/62">{{ __('kabeeri.ui.first_app') }}</p>
                                <a href="{{ route('customer.onboarding') }}" class="mt-4 inline-flex items-center rounded-full bg-[#111111] px-4 py-2 text-xs font-black text-[#f1eadc]"><x-kabeeri-icon name="rocket" />{{ __('kabeeri.ui.start_now') }}</a>
                            </div>
                        @endforelse
                    </div>
                </div>

                <aside id="actions" class="rounded-[1.5rem] bg-[#111111] p-4 text-[#f1eadc]">
                    <h2 class="text-base font-black">{{ __('kabeeri.ui.next_steps') }}</h2>
                    <div class="mt-3 divide-y divide-white/10">
                        @foreach ($nextActions as $action)
                            <article class="py-3 first:pt-0 last:pb-0">
                                <div class="flex items-center justify-between gap-3">
                                    <strong class="text-sm font-black">{{ __('kabeeri.ui.'.$action['title']) }}</strong>
                                    <span class="rounded-full bg-[#f1eadc] px-2 py-0.5 text-[10px] font-black text-[#111111]">{{ __('kabeeri.ui.'.$action['state']) }}</span>
                                </div>
                                <p class="mt-1 text-xs leading-5 text-[#f1eadc]/64">{{ __('kabeeri.ui.'.$action['text']) }}</p>
                            </article>
                        @endforeach
                    </div>
                </aside>
            </section>

            <section id="capabilities" class="mt-6 rounded-[1.5rem] border border-[#111111]/10 bg-white/42 p-4">
                <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-base font-black tracking-[-.02em]">{{ __('kabeeri.ui.roles_capabilities') }}</h2>
                    <span class="w-fit rounded-full bg-[#111111] px-3 py-1 text-xs font-black text-[#f1eadc]">{{ count($capabilityValues) }} {{ __('kabeeri.ui.enabled') }}</span>
                </div>
                <form method="POST" action="{{ route('customer.capabilities.update') }}">
                    @csrf
                    <div class="grid gap-2 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($capabilities as $key => $capability)
                            <label class="flex gap-3 rounded-2xl border border-[#111111]/10 bg-[#f1eadc]/60 px-3 py-3 transition hover:border-[#111111]/35">
                                <input class="mt-1 h-4 w-4 rounded border-[#111111]/20 text-[#111111]" type="checkbox" name="capabilities[]" value="{{ $key }}" @checked(in_array($key, $capabilityValues, true)) @disabled($key === 'customer_owner')>
                                <span class="min-w-0">
                                    <strong class="block text-sm font-black">{{ __('kabeeri.customer.capabilities.'.$key.'.label') }}</strong>
                                    <span class="mt-1 block line-clamp-1 text-[11px] leading-5 text-[#111111]/62">{{ __('kabeeri.customer.capabilities.'.$key.'.description') }}</span>
                                </span>
                            </label>
                            @if ($key === 'customer_owner')
                                <input type="hidden" name="capabilities[]" value="customer_owner">
                            @endif
                        @endforeach
                    </div>
                    <div class="mt-4 grid gap-3 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
                        <label class="grid gap-1 text-xs font-black" for="capability_note">
                            {{ __('kabeeri.ui.short_note') }}
                            <textarea id="capability_note" name="capability_note" class="min-h-14 w-full rounded-2xl border border-[#111111]/10 bg-white/70 p-3 text-sm leading-6 outline-none transition focus:border-[#111111]">{{ $profile->metadata['capability_note'] ?? '' }}</textarea>
                        </label>
                        <button class="inline-flex items-center justify-center rounded-full bg-[#111111] px-5 py-3 text-xs font-black text-[#f1eadc]" type="submit"><x-kabeeri-icon name="check" />{{ __('kabeeri.ui.update_capabilities') }}</button>
                    </div>
                </form>
            </section>

            <section class="mt-6 rounded-[1.5rem] border border-[#111111]/10 bg-white/42 p-4">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <h2 class="text-base font-black">{{ __('kabeeri.ui.extensions_themes') }}</h2>
                        <div class="mt-2 divide-y divide-[#111111]/10">
                            @foreach ($dashboard['cards'] as $card)
                                <article class="py-3">
                                    <strong class="block text-sm font-black">{{ __('kabeeri.customer.dashboard_cards.'.$card['key'].'.label') }}</strong>
                                    <p class="mt-1 line-clamp-1 text-xs leading-5 text-[#111111]/62">{{ __('kabeeri.customer.dashboard_cards.'.$card['key'].'.text') }}</p>
                                </article>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <h2 class="text-base font-black">{{ __('kabeeri.ui.account_data') }}</h2>
                        <div class="mt-3 text-sm leading-6 text-[#111111]">
                            <strong class="block truncate font-black">{{ $user->name }}</strong>
                            <span class="block break-all text-xs font-bold text-[#111111]/60">{{ $user->email }}</span>
                        </div>
                        <form class="mt-4" method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center rounded-full border border-[#111111]/10 bg-white/70 px-4 py-2.5 text-xs font-black"><x-kabeeri-icon name="logout" />{{ __('kabeeri.ui.logout') }}</button>
                        </form>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
