@php
    $user = $dashboard['user'];
    $profile = $dashboard['profile'];
    $activeOrganization = $dashboard['active_organization'];
    $sites = $dashboard['sites'];
    $firstSite = $sites->first();
    $capabilities = $dashboard['capabilities'];
    $capabilityValues = $profile->metadata['capabilities'] ?? ['customer_owner'];
    $activeThemeCount = $sites->filter(fn ($site) => filled($site->theme))->count();
    $needsBuilderHelp = in_array('needs_builder_help', $capabilityValues, true);
    $builderNote = $profile->metadata['capability_note'] ?? $profile->metadata['builder_request_note'] ?? null;
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
            ['title' => 'review_first_app', 'text' => 'review_first_app_text', 'state' => 'state_ready', 'icon' => 'check-circle'],
            ['title' => 'update_account_capabilities', 'text' => 'update_account_capabilities_text', 'state' => 'state_optional', 'icon' => 'account'],
            ['title' => 'choose_growth_addons', 'text' => 'choose_growth_addons_text', 'state' => 'state_next', 'icon' => 'plus'],
        ]
        : [
            ['title' => 'start_onboarding', 'text' => 'start_onboarding_text', 'state' => 'state_required', 'icon' => 'rocket'],
            ['title' => 'choose_app_type', 'text' => 'choose_app_type_text', 'state' => 'state_required', 'icon' => 'apps'],
            ['title' => 'install_suitable_theme', 'text' => 'install_suitable_theme_text', 'state' => 'state_required', 'icon' => 'theme'],
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
<body class="min-h-screen bg-[#fffaf0] text-[#17130d] antialiased">
    <div class="pointer-events-none fixed inset-0 -z-10 bg-[radial-gradient(circle_at_92%_8%,rgba(201,138,46,.18),transparent_22rem),linear-gradient(180deg,#fffaf0_0%,#fffaf0_62%,rgba(201,138,46,.10)_100%)]"></div>

    <div class="kbr-customer-shell grid min-h-screen w-full lg:grid-cols-[16rem_minmax(0,1fr)]">
        @include('customer.partials.dashboard-sidebar', ['dashboard' => $dashboard, 'activeNav' => 'overview'])

        <main class="min-w-0 px-4 py-4 sm:px-6 lg:px-8 lg:py-6">
            <header id="overview" class="mb-5 rounded-[1.7rem] border border-[#17130d]/10 bg-[#fffaf0]/92 px-4 py-3 shadow-[0_18px_55px_rgba(23,19,13,.08)] backdrop-blur-xl">
                <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
                    <div class="flex min-w-0 items-center gap-3">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-[#17130d] text-sm font-black text-[#fffaf0] lg:hidden">{{ __('kabeeri.brand.mark') }}</span>
                        <div class="min-w-0">
                            <p class="text-[10px] font-black uppercase tracking-[.18em] text-[#c98a2e]">{{ __('kabeeri.brand.name') }}</p>
                            <h1 class="mt-1 truncate text-lg font-black tracking-[-.02em] sm:text-xl">{{ __('kabeeri.ui.apps_dashboard') }}</h1>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <details class="w-full sm:w-auto lg:hidden">
                            <summary class="cursor-pointer rounded-full bg-[#17130d] px-4 py-2 text-center text-xs font-black text-[#fffaf0]">{{ __('kabeeri.ui.dashboard_nav') }}</summary>
                            <nav class="mt-2 grid gap-1 rounded-3xl border border-[#17130d]/10 bg-[#fffaf0] p-2 shadow-[0_18px_55px_rgba(23,19,13,.12)] sm:grid-cols-2" aria-label="{{ __('kabeeri.ui.dashboard_nav') }}">
                                @foreach ($sidebarItems as $item)
                                    <a href="{{ $item['target'] }}" class="flex items-center gap-2 rounded-2xl px-3 py-2 text-xs font-black {{ $item['active'] ? 'bg-[#17130d] text-[#fffaf0]' : 'bg-white/70 text-[#17130d]' }}"><x-kabeeri-icon name="{{ $item['icon'] }}" />{{ __('kabeeri.ui.'.$item['label']) }}</a>
                                @endforeach
                            </nav>
                        </details>
                        <a href="{{ route('customer.start') }}" class="inline-flex items-center rounded-full border border-[#17130d]/10 bg-white/70 px-3 py-2 text-xs font-black text-[#17130d]"><x-kabeeri-icon name="home" />{{ __('kabeeri.ui.home') }}</a>
                        <a href="{{ route('public.landing') }}" class="inline-flex items-center rounded-full border border-[#17130d]/10 bg-white/70 px-3 py-2 text-xs font-black text-[#17130d]"><x-kabeeri-icon name="info" />{{ __('kabeeri.ui.platform') }}</a>
                        @include('components.language-switcher', ['context' => 'customer'])
            @include('components.theme-switcher', ['context' => 'customer'])
            @include('components.font-switcher', ['context' => 'customer'])
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center rounded-full bg-[#17130d] px-3 py-2 text-xs font-black text-[#fffaf0]"><x-kabeeri-icon name="logout" />{{ __('kabeeri.ui.logout') }}</button>
                        </form>
                    </div>
                </div>
            </header>

            @if (session('status'))
                <div class="mb-5 rounded-2xl border border-[#c98a2e]/25 bg-[#fffaf0] px-4 py-3 text-xs font-black text-[#17130d] shadow-[0_12px_34px_rgba(23,19,13,.08)]">{{ session('status') }}</div>
            @endif

            <section class="mb-5 grid gap-3 md:grid-cols-3">
                <article class="rounded-3xl border border-[#17130d]/10 bg-[#17130d] p-4 text-[#fffaf0]">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-2xl bg-[#c98a2e] text-[#17130d]"><x-kabeeri-icon name="check-circle" style="margin-inline-end:0" /></span>
                    <p class="mt-3 text-[11px] font-black text-[#fffaf0]/58">{{ __('kabeeri.ui.workspace') }}</p>
                    <strong class="mt-1 block text-base font-black">{{ __('kabeeri.ui.'.($workspaceReady ? 'ready' : 'needs_setup')) }}</strong>
                </article>
                <article class="rounded-3xl border border-[#17130d]/10 bg-white/66 p-4">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-2xl bg-[#17130d] text-[#fffaf0]"><x-kabeeri-icon name="apps" style="margin-inline-end:0" /></span>
                    <p class="mt-3 text-[11px] font-black text-[#17130d]/58">{{ __('kabeeri.ui.apps') }}</p>
                    <strong class="mt-1 block text-base font-black">{{ $sites->count() }}</strong>
                </article>
                <article class="rounded-3xl border border-[#17130d]/10 bg-white/66 p-4">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-2xl bg-[#17130d] text-[#fffaf0]"><x-kabeeri-icon name="theme" style="margin-inline-end:0" /></span>
                    <p class="mt-3 text-[11px] font-black text-[#17130d]/58">{{ __('kabeeri.ui.theme') }}</p>
                    <strong class="mt-1 block text-base font-black">{{ $activeThemeCount }}</strong>
                </article>
            </section>

            <section class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_22rem]">
                <div id="apps" class="rounded-[1.7rem] border border-[#17130d]/10 bg-[#fffaf0]/92 p-4 shadow-[0_18px_55px_rgba(23,19,13,.08)]">
                    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <span class="inline-flex items-center rounded-full bg-[#17130d]/10 px-3 py-1 text-[11px] font-black text-[#17130d]"><x-kabeeri-icon name="apps" />{{ __('kabeeri.ui.my_apps') }}</span>
                            <h2 class="mt-2 text-base font-black tracking-[-.02em]">{{ __('kabeeri.ui.apps') }}</h2>
                        </div>
                        <a href="{{ route('customer.onboarding') }}" class="inline-flex items-center justify-center rounded-full bg-[#17130d] px-4 py-2 text-xs font-black text-[#fffaf0]"><x-kabeeri-icon name="plus" />{{ __('kabeeri.ui.new_app_later') }}</a>
                    </div>

                    <div class="divide-y divide-[#17130d]/10 overflow-hidden rounded-3xl border border-[#17130d]/10 bg-white/58">
                        @forelse ($sites as $site)
                            @php
                                $appType = $site->metadata['v16_app_type'] ?? $site->site_type;
                                $themeSlug = $site->theme?->slug;
                            @endphp
                            <a href="{{ route('customer.apps.show', ['username' => $site->username]) }}" class="grid gap-3 px-4 py-3 transition hover:bg-[#fffaf0] md:grid-cols-[minmax(0,1fr)_11rem_6rem] md:items-center">
                                <span class="min-w-0">
                                    <strong class="block truncate text-sm font-black">{{ $site->name }}</strong>
                                    <small class="mt-1 block truncate text-xs font-bold text-[#17130d]/58">{{ __('kabeeri.customer.app_types.'.$appType.'.label') }} / {{ __('kabeeri.ui.username') }}: {{ $site->username }}</small>
                                </span>
                                <span class="inline-flex w-fit items-center rounded-full bg-[#fffaf0] px-3 py-1 text-xs font-black text-[#17130d] md:justify-self-start">{{ $themeSlug ? __('kabeeri.customer.themes.'.$themeSlug.'.name') : __('kabeeri.ui.no_theme') }}</span>
                                <span class="inline-flex w-fit items-center rounded-full bg-[#c98a2e]/18 px-3 py-1 text-xs font-black text-[#17130d] md:justify-self-end">{{ __('kabeeri.ui.active') }}</span>
                            </a>
                        @empty
                            <div class="px-5 py-8 text-center">
                                <span class="mx-auto grid h-10 w-10 place-items-center rounded-2xl bg-[#17130d] text-[#fffaf0]"><x-kabeeri-icon name="plus" style="margin-inline-end:0" /></span>
                                <p class="mt-3 text-sm font-black">{{ __('kabeeri.ui.no_app') }}</p>
                                <p class="mx-auto mt-1 max-w-sm text-xs leading-6 text-[#17130d]/64">{{ __('kabeeri.ui.first_app') }}</p>
                                <a href="{{ route('customer.onboarding') }}" class="mt-4 inline-flex items-center rounded-full bg-[#17130d] px-4 py-2 text-xs font-black text-[#fffaf0]"><x-kabeeri-icon name="rocket" />{{ __('kabeeri.ui.start_now') }}</a>
                            </div>
                        @endforelse
                    </div>
                </div>

                <aside id="actions" class="space-y-5">
                    <section class="rounded-[1.7rem] border border-[#17130d]/10 bg-[#17130d] p-4 text-[#fffaf0] shadow-[0_18px_55px_rgba(23,19,13,.10)]">
                        <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-[11px] font-black text-[#c98a2e]"><x-kabeeri-icon name="steps" />{{ __('kabeeri.ui.next_steps') }}</span>
                        <div class="mt-3 divide-y divide-white/10">
                            @foreach ($nextActions as $action)
                                <article class="py-3 first:pt-0 last:pb-0">
                                    <div class="flex items-start gap-3">
                                        <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-2xl bg-[#c98a2e] text-[#17130d]"><x-kabeeri-icon name="{{ $action['icon'] }}" style="margin-inline-end:0" /></span>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center justify-between gap-2">
                                                <strong class="text-sm font-black">{{ __('kabeeri.ui.'.$action['title']) }}</strong>
                                                <span class="shrink-0 rounded-full bg-[#fffaf0] px-2 py-0.5 text-[10px] font-black text-[#17130d]">{{ __('kabeeri.ui.'.$action['state']) }}</span>
                                            </div>
                                            <p class="mt-1 text-xs leading-5 text-[#fffaf0]/62">{{ __('kabeeri.ui.'.$action['text']) }}</p>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>

                    <section id="builder-help" class="rounded-[1.7rem] border border-[#17130d]/10 bg-white/66 p-4">
                        <span class="inline-flex items-center rounded-full bg-[#c98a2e]/16 px-3 py-1 text-[11px] font-black text-[#17130d]"><x-kabeeri-icon name="builder" />{{ __('kabeeri.ui.builder_help') }}</span>
                        <strong class="mt-3 block text-sm font-black">{{ __('kabeeri.ui.'.($needsBuilderHelp ? 'builder_requested' : 'builder_not_requested')) }}</strong>
                        @if ($builderNote)
                            <p class="mt-2 line-clamp-2 text-xs leading-5 text-[#17130d]/64">{{ $builderNote }}</p>
                        @endif
                    </section>
                </aside>
            </section>

            <section id="capabilities" class="mt-5 rounded-[1.7rem] border border-[#17130d]/10 bg-[#fffaf0]/92 p-4 shadow-[0_18px_55px_rgba(23,19,13,.08)]">
                <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <span class="inline-flex items-center rounded-full bg-[#17130d]/10 px-3 py-1 text-[11px] font-black text-[#17130d]"><x-kabeeri-icon name="account" />{{ __('kabeeri.ui.capabilities') }}</span>
                        <h2 class="mt-2 text-base font-black tracking-[-.02em]">{{ __('kabeeri.ui.roles_capabilities') }}</h2>
                    </div>
                    <span class="w-fit rounded-full bg-white/70 px-3 py-1 text-xs font-black text-[#17130d]">{{ count($capabilityValues) }} {{ __('kabeeri.ui.enabled') }}</span>
                </div>

                <form method="POST" action="{{ route('customer.capabilities.update') }}">
                    @csrf
                    <div class="grid gap-2 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($capabilities as $key => $capability)
                            <label class="flex gap-3 rounded-2xl border border-[#17130d]/10 bg-white/58 px-3 py-3 transition hover:border-[#c98a2e]/45">
                                <input class="mt-1 h-4 w-4 rounded border-[#17130d]/20 text-[#17130d]" type="checkbox" name="capabilities[]" value="{{ $key }}" @checked(in_array($key, $capabilityValues, true)) @disabled($key === 'customer_owner')>
                                <span class="min-w-0">
                                    <strong class="block text-sm font-black">{{ __('kabeeri.customer.capabilities.'.$key.'.label') }}</strong>
                                    <span class="mt-1 block line-clamp-1 text-[11px] leading-5 text-[#17130d]/62">{{ __('kabeeri.customer.capabilities.'.$key.'.description') }}</span>
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
                            <textarea id="capability_note" name="capability_note" class="min-h-14 w-full rounded-2xl border border-[#17130d]/10 bg-white/70 p-3 text-sm leading-6 outline-none transition focus:border-[#c98a2e]">{{ $profile->metadata['capability_note'] ?? '' }}</textarea>
                        </label>
                        <button class="inline-flex items-center justify-center rounded-full bg-[#17130d] px-5 py-3 text-xs font-black text-[#fffaf0]" type="submit"><x-kabeeri-icon name="check" />{{ __('kabeeri.ui.update_capabilities') }}</button>
                    </div>
                </form>
            </section>

            <section class="mt-5 grid gap-5 xl:grid-cols-[minmax(0,1fr)_22rem]">
                <section id="marketplace" class="rounded-[1.7rem] border border-[#17130d]/10 bg-white/66 p-4">
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <div>
                            <span class="inline-flex items-center rounded-full bg-[#c98a2e]/16 px-3 py-1 text-[11px] font-black text-[#17130d]"><x-kabeeri-icon name="mall" />{{ __('kabeeri.ui.marketplace') }}</span>
                            <h2 class="mt-2 text-base font-black tracking-[-.02em]">{{ __('kabeeri.ui.extensions_themes') }}</h2>
                        </div>
                    </div>
                    <div class="grid gap-2 md:grid-cols-2">
                        @foreach ($dashboard['cards'] as $card)
                            <article class="rounded-2xl border border-[#17130d]/10 bg-[#fffaf0]/80 px-3 py-3">
                                <h3 class="text-sm font-black">{{ __('kabeeri.customer.dashboard_cards.'.$card['key'].'.label') }}</h3>
                                <p class="mt-1 line-clamp-1 text-xs leading-5 text-[#17130d]/62">{{ __('kabeeri.customer.dashboard_cards.'.$card['key'].'.text') }}</p>
                            </article>
                        @endforeach
                    </div>
                </section>

                <section id="client-account" class="rounded-[1.7rem] border border-[#17130d]/10 bg-[#fffaf0]/92 p-4 shadow-[0_18px_55px_rgba(23,19,13,.08)]">
                    <span class="inline-flex items-center rounded-full bg-[#17130d]/10 px-3 py-1 text-[11px] font-black text-[#17130d]"><x-kabeeri-icon name="account" />{{ __('kabeeri.ui.account') }}</span>
                    <h2 class="mt-2 text-base font-black tracking-[-.02em]">{{ __('kabeeri.ui.account_data') }}</h2>
                    <div class="mt-4 space-y-2 text-sm leading-6 text-[#17130d]">
                        <strong class="block truncate font-black">{{ $user->name }}</strong>
                        <span class="block break-all text-xs font-bold text-[#17130d]/60">{{ $user->email }}</span>
                    </div>
                    <form class="mt-4" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-full border border-[#17130d]/10 bg-white/70 px-4 py-2.5 text-xs font-black"><x-kabeeri-icon name="logout" />{{ __('kabeeri.ui.logout') }}</button>
                    </form>
                </section>
            </section>
        </main>
    </div>
</body>
</html>
