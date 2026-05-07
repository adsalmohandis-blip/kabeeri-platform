@php
    $user = $dashboard['user'];
    $profile = $dashboard['profile'];
    $activeOrganization = $dashboard['active_organization'];
    $sites = $dashboard['sites'];
    $capabilities = $dashboard['capabilities'];
    $capabilityValues = $profile->metadata['capabilities'] ?? ['customer_owner'];
    $activeThemeCount = $sites->filter(fn ($site) => filled($site->theme))->count();
    $needsBuilderHelp = in_array('needs_builder_help', $capabilityValues, true);
    $builderNote = $profile->metadata['capability_note'] ?? $profile->metadata['builder_request_note'] ?? null;
    $workspaceReady = filled($activeOrganization);
    $sidebarItems = [
        ['label' => 'overview', 'target' => '#overview', 'caption' => 'simple_path', 'icon' => 'chart'],
        ['label' => 'apps', 'target' => '#apps', 'caption' => 'my_apps', 'icon' => 'apps'],
        ['label' => 'actions', 'target' => '#actions', 'caption' => 'next_steps', 'icon' => 'steps'],
        ['label' => 'capabilities', 'target' => '#capabilities', 'caption' => 'roles_capabilities', 'icon' => 'account'],
        ['label' => 'marketplace', 'target' => '#marketplace', 'caption' => 'extensions_themes', 'icon' => 'mall'],
        ['label' => 'account', 'target' => '#client-account', 'caption' => 'account_data', 'icon' => 'settings'],
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
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Localization\KabeeriLocale::direction() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('kabeeri.ui.apps_dashboard') }} | {{ __('kabeeri.brand.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=almarai:400,700,800|ibm-plex-sans-arabic:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#fffaf0] text-kabeeri-ink antialiased">
    <div class="pointer-events-none fixed inset-0 -z-10 bg-[radial-gradient(circle_at_88%_8%,rgba(23,19,13,.16),transparent_26rem),radial-gradient(circle_at_5%_16%,rgba(201,138,46,.22),transparent_24rem),linear-gradient(135deg,#fffaf0_0%,#fffaf0_62%,#17130d_100%)]"></div>

    <div class="lg:grid lg:min-h-screen lg:grid-cols-[15rem_minmax(0,1fr)]">
        <aside id="workspace-sidebar" class="hidden border-l border-white/60 bg-[#17130d]/95 text-[#fffaf0] shadow-2xl shadow-[#17130d]/15 lg:sticky lg:top-0 lg:flex lg:h-screen lg:flex-col">
            <div class="border-b border-white/10 p-4">
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 place-items-center rounded-2xl bg-[#c98a2e] text-base font-black text-[#17130d]">{{ __('kabeeri.brand.mark') }}</span>
                    <div>
                        <p class="text-[11px] font-black uppercase tracking-[.18em] text-[#c98a2e]">{{ __('kabeeri.brand.name') }}</p>
                        <h1 class="text-sm font-black leading-tight">{{ __('kabeeri.ui.apps_dashboard') }}</h1>
                    </div>
                </div>
            </div>

            <nav class="flex-1 space-y-1.5 overflow-y-auto px-3 py-4" aria-label="{{ __('kabeeri.ui.dashboard_nav') }}">
                @foreach ($sidebarItems as $item)
                    <a href="{{ $item['target'] }}" class="group flex items-center justify-between rounded-2xl border border-white/10 bg-white/[.035] px-3 py-2.5 transition hover:border-[#c98a2e]/55 hover:bg-[#c98a2e]/12">
                        <span class="flex items-start gap-2">
                            <x-kabeeri-icon name="{{ $item['icon'] }}" class="mt-1 text-[#c98a2e]" />
                            <span>
                                <span class="block text-sm font-black">{{ __('kabeeri.ui.'.$item['label']) }}</span>
                                <span class="mt-0.5 block text-[11px] text-white/58">{{ __('kabeeri.ui.'.$item['caption']) }}</span>
                            </span>
                        </span>
                        <span class="text-xs text-white/35 group-hover:text-[#c98a2e]">{{ __('kabeeri.ui.go') }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="m-3 rounded-2xl border border-white/10 bg-white/[.055] p-3">
                <p class="text-[11px] font-black uppercase tracking-[.18em] text-[#c98a2e]">{{ __('kabeeri.ui.workspace_status') }}</p>
                <p class="mt-1 text-lg font-black">{{ __('kabeeri.ui.'.($workspaceReady ? 'ready' : 'needs_setup')) }}</p>
                <p class="mt-1 line-clamp-2 text-xs leading-6 text-white/58">{{ $activeOrganization?->name ?? __('kabeeri.ui.needs_setup_sentence') }}</p>
                @unless ($workspaceReady)
                    <a href="{{ route('customer.onboarding') }}" class="mt-3 inline-flex w-full items-center justify-center rounded-full bg-[#fffaf0] px-3 py-2 text-xs font-black text-[#17130d]"><x-kabeeri-icon name="plus" />{{ __('kabeeri.ui.start_now') }}</a>
                @endunless
            </div>
        </aside>

        <main class="min-w-0 px-3 py-3 sm:px-5 lg:px-6 lg:py-5">
            <header id="overview" class="sticky top-3 z-30 mb-4 rounded-3xl border border-white/70 bg-[#fffaf0]/86 px-3 py-2.5 shadow-kabeeri-soft backdrop-blur-2xl">
                <div class="flex flex-col gap-2.5 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="grid h-10 w-10 place-items-center rounded-2xl bg-[#17130d] text-sm font-black text-[#fffaf0] lg:hidden">{{ __('kabeeri.brand.mark') }}</span>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[.18em] text-kabeeri-clay">{{ __('kabeeri.brand.name') }}</p>
                            <h2 class="text-base font-black sm:text-lg">{{ __('kabeeri.ui.apps_dashboard') }}</h2>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-1.5">
                        <a href="{{ route('customer.start') }}" class="inline-flex items-center rounded-full border border-[#17130d]/10 bg-white/60 px-3 py-1.5 text-xs font-black"><x-kabeeri-icon name="home" />{{ __('kabeeri.ui.home') }}</a>
                        <a href="{{ route('public.landing') }}" class="inline-flex items-center rounded-full border border-[#17130d]/10 bg-white/60 px-3 py-1.5 text-xs font-black"><x-kabeeri-icon name="info" />{{ __('kabeeri.ui.platform') }}</a>
                        @include('components.language-switcher', ['context' => 'customer'])
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center rounded-full bg-[#17130d] px-3 py-1.5 text-xs font-black text-[#fffaf0]"><x-kabeeri-icon name="logout" />{{ __('kabeeri.ui.logout') }}</button>
                        </form>
                    </div>
                </div>

                <details class="mt-2 lg:hidden">
                    <summary class="cursor-pointer rounded-2xl bg-[#17130d] px-3 py-2 text-xs font-black text-[#fffaf0]">{{ __('kabeeri.ui.dashboard_nav') }}</summary>
                    <nav class="mt-2 grid gap-1.5 sm:grid-cols-2" aria-label="{{ __('kabeeri.ui.dashboard_nav') }}">
                        @foreach ($sidebarItems as $item)
                            <a href="{{ $item['target'] }}" class="rounded-2xl border border-[#17130d]/10 bg-white/70 px-3 py-2 text-xs font-black"><x-kabeeri-icon name="{{ $item['icon'] }}" />{{ __('kabeeri.ui.'.$item['label']) }} <span class="block text-[11px] font-bold text-[#17130d]">{{ __('kabeeri.ui.'.$item['caption']) }}</span></a>
                        @endforeach
                    </nav>
                </details>
            </header>

            @if (session('status'))
                <div class="mb-4 rounded-2xl border border-[#c98a2e]/20 bg-[#fffaf0]/80 px-4 py-3 text-xs font-black text-[#17130d] shadow-kabeeri-soft">{{ session('status') }}</div>
            @endif

            <section class="rounded-3xl border border-white/60 bg-[#17130d] p-4 text-[#fffaf0] sm:p-5">
                <div class="grid gap-3 xl:grid-cols-[minmax(0,1fr)_18rem] xl:items-center">
                    <div>
                        <span class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-[10px] font-black uppercase tracking-[.16em] text-[#c98a2e]"><x-kabeeri-icon name="apps" />{{ __('kabeeri.ui.apps') }}</span>
                        <h1 class="mt-3 max-w-2xl text-lg font-black leading-tight tracking-[-.02em] sm:text-xl">{{ __('kabeeri.ui.apps_dashboard') }}</h1>
                    </div>
                    <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs font-bold text-white/72">
                        <span><strong class="text-[#fffaf0]">{{ __('kabeeri.ui.'.($workspaceReady ? 'ready' : 'needs_setup')) }}</strong> {{ __('kabeeri.ui.workspace') }}</span>
                        <span><strong class="text-[#fffaf0]">{{ $sites->count() }}</strong> {{ __('kabeeri.ui.app') }}</span>
                        <span><strong class="text-[#fffaf0]">{{ $activeThemeCount }}</strong> {{ __('kabeeri.ui.theme') }}</span>
                    </div>
                </div>
            </section>

            <section class="mt-4 grid gap-4 xl:grid-cols-[minmax(0,1.35fr)_minmax(280px,.65fr)]">
                <div id="apps" class="rounded-3xl border border-[#17130d]/10 bg-[#fffaf0]/86 p-4 backdrop-blur">
                    <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <span class="inline-flex items-center rounded-full bg-[#17130d]/10 px-2.5 py-1 text-[11px] font-black text-[#17130d]"><x-kabeeri-icon name="apps" />{{ __('kabeeri.ui.my_apps') }}</span>
                            <h2 class="mt-2 text-base font-black tracking-[-.02em]">{{ __('kabeeri.ui.apps') }}</h2>
                        </div>
                        <a href="{{ route('customer.onboarding') }}" class="inline-flex items-center rounded-full border border-[#17130d]/10 bg-white/70 px-3 py-1.5 text-xs font-black"><x-kabeeri-icon name="plus" />{{ __('kabeeri.ui.new_app_later') }}</a>
                    </div>

                    <div class="overflow-hidden rounded-2xl border border-[#17130d]/10 bg-white/58">
                        @forelse ($sites as $site)
                            @php
                                $appType = $site->metadata['v16_app_type'] ?? $site->site_type;
                                $themeSlug = $site->theme?->slug;
                            @endphp
                            <a href="{{ route('customer.apps.show', ['username' => $site->username]) }}" class="grid gap-2 border-b border-[#17130d]/10 px-3 py-2.5 transition last:border-b-0 hover:bg-[#fffaf0]/55 md:grid-cols-[minmax(0,1fr)_10rem_7rem] md:items-center">
                                <span>
                                    <strong class="block text-sm font-black">{{ $site->name }}</strong>
                                    <small class="mt-0.5 block text-xs text-[#17130d]">{{ __('kabeeri.customer.app_types.'.$appType.'.label') }} / {{ __('kabeeri.ui.username') }}: {{ $site->username }}</small>
                                </span>
                                <span class="rounded-full bg-[#17130d]/10 px-2.5 py-1 text-xs font-black text-[#17130d]">{{ $themeSlug ? __('kabeeri.customer.themes.'.$themeSlug.'.name') : __('kabeeri.ui.no_theme') }}</span>
                                <span class="rounded-full bg-[#c98a2e]/15 px-2.5 py-1 text-center text-xs font-black text-[#17130d]">{{ __('kabeeri.ui.active') }}</span>
                            </a>
                        @empty
                            <div class="p-5 text-center">
                                <p class="text-sm font-black">{{ __('kabeeri.ui.no_app') }}</p>
                                <p class="mt-1 text-xs text-[#17130d]">{{ __('kabeeri.ui.first_app') }}</p>
                                <a href="{{ route('customer.onboarding') }}" class="mt-3 inline-flex items-center rounded-full bg-[#17130d] px-4 py-2 text-xs font-black text-[#fffaf0]"><x-kabeeri-icon name="plus" />{{ __('kabeeri.ui.start_now') }}</a>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div id="actions" class="rounded-3xl border border-[#17130d]/10 bg-[#17130d] p-4 text-[#fffaf0]">
                    <span class="inline-flex items-center rounded-full bg-white/10 px-2.5 py-1 text-[11px] font-black uppercase tracking-[.14em] text-[#c98a2e]"><x-kabeeri-icon name="steps" />{{ __('kabeeri.ui.next') }}</span>
                    <h2 class="mt-2 text-base font-black tracking-[-.02em]">{{ __('kabeeri.ui.next_steps') }}</h2>
                    <div class="mt-3 divide-y divide-white/10">
                        @foreach ($nextActions as $action)
                            <div class="py-2.5">
                                <div class="flex items-center justify-between gap-3">
                                    <strong class="text-sm font-black">{{ __('kabeeri.ui.'.$action['title']) }}</strong>
                                    <span class="shrink-0 rounded-full bg-[#c98a2e] px-2 py-0.5 text-[10px] font-black text-[#17130d]">{{ __('kabeeri.ui.'.$action['state']) }}</span>
                                </div>
                                <p class="mt-1 text-xs leading-5 text-white/64">{{ __('kabeeri.ui.'.$action['text']) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="capabilities" class="mt-4 grid gap-4 xl:grid-cols-[minmax(0,1fr)_minmax(280px,.55fr)]">
                <div class="rounded-3xl border border-[#17130d]/10 bg-[#fffaf0]/86 p-4 backdrop-blur">
                    <div class="mb-3 flex items-center justify-between gap-2">
                        <div>
                            <span class="inline-flex items-center rounded-full bg-[#17130d]/10 px-2.5 py-1 text-[11px] font-black text-[#17130d]"><x-kabeeri-icon name="account" />{{ __('kabeeri.ui.capabilities') }}</span>
                            <h2 class="mt-2 text-base font-black tracking-[-.02em]">{{ __('kabeeri.ui.roles_capabilities') }}</h2>
                        </div>
                        <span class="rounded-full bg-white/70 px-2.5 py-1 text-xs font-black text-[#17130d]">{{ count($capabilityValues) }} {{ __('kabeeri.ui.enabled') }}</span>
                    </div>
                    <form method="POST" action="{{ route('customer.capabilities.update') }}">
                        @csrf
                        <div class="grid gap-1.5 md:grid-cols-2">
                            @foreach ($capabilities as $key => $capability)
                                <label class="flex gap-2 rounded-2xl border border-transparent px-2 py-2 transition hover:border-[#17130d]/15 hover:bg-white/55">
                                    <input class="mt-1 h-4 w-4 rounded border-[#17130d]/20 text-[#17130d]" type="checkbox" name="capabilities[]" value="{{ $key }}" @checked(in_array($key, $capabilityValues, true)) @disabled($key === 'customer_owner')>
                                    <span>
                                        <strong class="block text-sm font-black">{{ __('kabeeri.customer.capabilities.'.$key.'.label') }}</strong>
                                        <span class="mt-0.5 block line-clamp-1 text-[11px] leading-5 text-[#17130d]">{{ __('kabeeri.customer.capabilities.'.$key.'.description') }}</span>
                                    </span>
                                </label>
                                @if ($key === 'customer_owner')
                                    <input type="hidden" name="capabilities[]" value="customer_owner">
                                @endif
                            @endforeach
                        </div>
                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-black" for="capability_note">{{ __('kabeeri.ui.short_note') }}</label>
                            <textarea id="capability_note" name="capability_note" class="min-h-16 w-full rounded-2xl border border-[#17130d]/10 bg-white/70 p-3 text-sm leading-6 outline-none transition focus:border-[#c98a2e]">{{ $profile->metadata['capability_note'] ?? '' }}</textarea>
                        </div>
                        <button class="mt-3 inline-flex items-center rounded-full bg-[#17130d] px-4 py-2 text-xs font-black text-[#fffaf0]" type="submit"><x-kabeeri-icon name="check" />{{ __('kabeeri.ui.update_capabilities') }}</button>
                    </form>
                </div>

                <div id="builder-help" class="rounded-3xl border border-[#17130d]/10 bg-[#fffaf0]/86 p-4 backdrop-blur">
                    <span class="inline-flex items-center rounded-full bg-[#c98a2e]/15 px-2.5 py-1 text-[11px] font-black text-[#17130d]"><x-kabeeri-icon name="builder" />{{ __('kabeeri.ui.builder_help') }}</span>
                    <h2 class="mt-2 text-base font-black tracking-[-.02em]">{{ __('kabeeri.ui.builder_help') }}</h2>
                    <div class="mt-3 border-t border-[#17130d]/10 pt-3">
                        <strong class="block text-sm font-black">{{ __('kabeeri.ui.'.($needsBuilderHelp ? 'builder_requested' : 'builder_not_requested')) }}</strong>
                        @if ($builderNote)
                            <p class="mt-2 line-clamp-2 text-xs leading-5 text-[#17130d]">{{ $builderNote }}</p>
                        @endif
                    </div>
                </div>
            </section>

            <section id="marketplace" class="mt-4 rounded-3xl border border-[#17130d]/10 bg-[#fffaf0]/86 p-4 backdrop-blur">
                <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <span class="inline-flex items-center rounded-full bg-[#c98a2e]/15 px-2.5 py-1 text-[11px] font-black text-[#17130d]"><x-kabeeri-icon name="mall" />{{ __('kabeeri.ui.marketplace') }}</span>
                        <h2 class="mt-2 text-base font-black tracking-[-.02em]">{{ __('kabeeri.ui.extensions_themes') }}</h2>
                    </div>
                </div>
                <div class="divide-y divide-[#17130d]/10">
                    @foreach ($dashboard['cards'] as $card)
                        <article class="flex items-center justify-between gap-3 py-2.5">
                            <div>
                                <h3 class="text-sm font-black">{{ __('kabeeri.customer.dashboard_cards.'.$card['key'].'.label') }}</h3>
                                <p class="mt-1 text-xs text-[#17130d]">{{ __('kabeeri.customer.dashboard_cards.'.$card['key'].'.text') }}</p>
                            </div>
                            <x-kabeeri-icon name="check-circle" class="text-[#c98a2e]" />
                        </article>
                    @endforeach
                </div>
            </section>

            <section id="client-account" class="mt-4 rounded-3xl border border-[#17130d]/10 bg-[#fffaf0]/86 p-4 backdrop-blur">
                <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] lg:items-center">
                    <div>
                        <span class="inline-flex items-center rounded-full bg-[#17130d]/10 px-2.5 py-1 text-[11px] font-black text-[#17130d]"><x-kabeeri-icon name="account" />{{ __('kabeeri.ui.account') }}</span>
                        <h2 class="mt-2 text-base font-black tracking-[-.02em]">{{ __('kabeeri.ui.account_data') }}</h2>
                    </div>
                    <div class="text-sm leading-7 text-[#17130d]">
                        <strong class="font-black text-[#17130d]">{{ $user->name }}</strong>
                        <span class="mx-2 text-[#c98a2e]">/</span>
                        <span class="break-all">{{ $user->email }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center rounded-full border border-[#17130d]/10 bg-white/70 px-4 py-2 text-xs font-black"><x-kabeeri-icon name="logout" />{{ __('kabeeri.ui.logout') }}</button>
                    </form>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
