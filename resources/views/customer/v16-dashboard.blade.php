@php
    $user = $dashboard['user'];
    $profile = $dashboard['profile'];
    $organizations = $dashboard['organizations'];
    $activeOrganization = $dashboard['active_organization'];
    $sites = $dashboard['sites'];
    $capabilities = $dashboard['capabilities'];
    $capabilityValues = $profile->metadata['capabilities'] ?? ['customer_owner'];
    $capabilityNames = collect($capabilities)
        ->filter(fn ($capability, $key) => in_array($key, $capabilityValues, true))
        ->pluck('label')
        ->values();
    $activeThemeCount = $sites->filter(fn ($site) => filled($site->theme))->count();
    $needsBuilderHelp = in_array('needs_builder_help', $capabilityValues, true);
    $builderNote = $profile->metadata['capability_note'] ?? $profile->metadata['builder_request_note'] ?? null;
    $workspaceReady = filled($activeOrganization);
    $sidebarItems = [
        ['label' => 'Overview', 'target' => '#overview', 'caption' => 'ملخص سريع', 'icon' => 'chart'],
        ['label' => 'Apps', 'target' => '#apps', 'caption' => 'التطبيقات', 'icon' => 'apps'],
        ['label' => 'Actions', 'target' => '#actions', 'caption' => 'الخطوات', 'icon' => 'steps'],
        ['label' => 'Capabilities', 'target' => '#capabilities', 'caption' => 'الأدوار', 'icon' => 'account'],
        ['label' => 'Marketplace', 'target' => '#marketplace', 'caption' => 'الإضافات', 'icon' => 'mall'],
        ['label' => 'Account', 'target' => '#client-account', 'caption' => 'الحساب', 'icon' => 'settings'],
    ];
    $nextActions = $workspaceReady
        ? [
            ['title' => 'راجع التطبيق الأول', 'text' => 'افتح التطبيق وتأكد من الثيم والمحتوى الأولي.', 'state' => 'Ready'],
            ['title' => 'حدّث قدرات الحساب', 'text' => 'فعّل مسار Builder أو Marketer أو Developer حسب احتياجك.', 'state' => 'Optional'],
            ['title' => 'اختر إضافات النمو', 'text' => 'CRM، Commerce، Mall visibility، وإدارة الفريق تأتي لاحقًا.', 'state' => 'Next'],
        ]
        : [
            ['title' => 'ابدأ Guided Onboarding', 'text' => 'أنشئ Workspace، تطبيق، وثيم أولي.', 'state' => 'Required'],
            ['title' => 'اختر نوع التطبيق', 'text' => 'Website أو Store أو Service Business أو Landing.', 'state' => 'Required'],
            ['title' => 'ثبّت الثيم المناسب', 'text' => 'سيتم اختيار ثيم متوافق مع نوع التطبيق.', 'state' => 'Required'],
        ];
@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Localization\KabeeriLocale::direction() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>لوحة إدارة التطبيقات - {{ __('kabeeri.brand.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=almarai:400,700,800|ibm-plex-sans-arabic:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#fffaf0] text-kabeeri-ink antialiased">
    <div class="pointer-events-none fixed inset-0 -z-10 bg-[radial-gradient(circle_at_88%_8%,rgba(23,19,13,.16),transparent_26rem),radial-gradient(circle_at_5%_16%,rgba(201,138,46,.22),transparent_24rem),linear-gradient(135deg,#fffaf0_0%,#fffaf0_54%,#17130d_100%)]"></div>
    <div class="pointer-events-none fixed inset-0 -z-10 opacity-20 [background-image:linear-gradient(rgba(23,19,13,.06)_1px,transparent_1px),linear-gradient(90deg,rgba(23,19,13,.045)_1px,transparent_1px)] [background-size:40px_40px] [mask-image:linear-gradient(to_bottom,#000,transparent_82%)]"></div>

    <div class="lg:grid lg:min-h-screen lg:grid-cols-[16rem_minmax(0,1fr)]">
        <aside id="workspace-sidebar" class="hidden border-l border-white/60 bg-[#17130d]/95 text-[#fffaf0] shadow-2xl shadow-[#17130d]/15 lg:sticky lg:top-0 lg:flex lg:h-screen lg:flex-col">
            <div class="border-b border-white/10 p-4">
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 place-items-center rounded-2xl bg-gradient-to-br from-[#c98a2e] via-[#17130d] to-[#17130d] text-base font-black">K</span>
                    <div>
                        <p class="text-[11px] font-black uppercase tracking-[.22em] text-[#c98a2e]">{{ __('kabeeri.brand.name') }}</p>
                        <h1 class="text-sm font-black leading-tight">لوحة التطبيقات</h1>
                    </div>
                </div>
            </div>

            <nav class="flex-1 space-y-1.5 overflow-y-auto px-3 py-4" aria-label="Customer dashboard sidebar">
                @foreach ($sidebarItems as $item)
                    <a href="{{ $item['target'] }}" class="group flex items-center justify-between rounded-2xl border border-white/10 bg-white/[.035] px-3 py-2.5 transition hover:border-[#c98a2e]/55 hover:bg-[#c98a2e]/12">
                        <span class="flex items-start gap-2">
                            <x-kabeeri-icon name="{{ $item['icon'] }}" class="mt-1 text-[#c98a2e]" />
                            <span>
                                <span class="block text-sm font-black">{{ $item['label'] }}</span>
                                <span class="mt-0.5 block text-[11px] text-white/52">{{ $item['caption'] }}</span>
                            </span>
                        </span>
                        <span class="text-xs text-white/35 group-hover:text-[#c98a2e]">GO</span>
                    </a>
                @endforeach
            </nav>

            <div class="m-3 rounded-2xl border border-white/10 bg-white/[.055] p-3">
                <p class="text-[11px] font-black uppercase tracking-[.18em] text-[#c98a2e]">Workspace</p>
                <p class="mt-1 text-lg font-black">{{ $workspaceReady ? 'Active' : 'Needs setup' }}</p>
                <p class="mt-1 line-clamp-2 text-xs leading-6 text-white/58">{{ $activeOrganization?->name ?? 'ابدأ onboarding لإنشاء مساحة العمل الأولى.' }}</p>
                @unless ($workspaceReady)
                    <a href="{{ route('customer.onboarding') }}" class="mt-3 inline-flex w-full items-center justify-center rounded-full bg-[#fffaf0] px-3 py-2 text-xs font-black text-[#17130d]"><x-kabeeri-icon name="plus" />ابدأ الآن</a>
                @endunless
            </div>
        </aside>

        <main class="min-w-0 px-3 py-3 sm:px-5 lg:px-6 lg:py-5">
            <header id="overview" class="sticky top-3 z-30 mb-4 rounded-3xl border border-white/70 bg-[#fffaf0]/82 px-3 py-2.5 shadow-kabeeri-soft backdrop-blur-2xl">
                <div class="flex flex-col gap-2.5 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="grid h-10 w-10 place-items-center rounded-2xl bg-[#17130d] text-sm font-black text-[#fffaf0] lg:hidden">K</span>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[.18em] text-kabeeri-clay">Client Workspace</p>
                            <h2 class="text-base font-black sm:text-lg">{{ $user->name }}، لوحة إدارة التطبيقات.</h2>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-1.5">
                        <a href="{{ route('customer.start') }}" class="inline-flex items-center rounded-full border border-[#17130d]/10 bg-white/60 px-3 py-1.5 text-xs font-black"><x-kabeeri-icon name="home" />البداية</a>
                        <a href="{{ route('public.landing') }}" class="inline-flex items-center rounded-full border border-[#17130d]/10 bg-white/60 px-3 py-1.5 text-xs font-black"><x-kabeeri-icon name="info" />المنصة</a>
                        @include('components.language-switcher', ['context' => 'customer'])
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center rounded-full bg-[#17130d] px-3 py-1.5 text-xs font-black text-[#fffaf0]"><x-kabeeri-icon name="logout" />خروج</button>
                        </form>
                    </div>
                </div>

                <details class="mt-2 lg:hidden">
                    <summary class="cursor-pointer rounded-2xl bg-[#17130d] px-3 py-2 text-xs font-black text-[#fffaf0]">فتح لوحة التنقل</summary>
                    <nav class="mt-2 grid gap-1.5 sm:grid-cols-2" aria-label="Mobile dashboard sidebar">
                        @foreach ($sidebarItems as $item)
                            <a href="{{ $item['target'] }}" class="rounded-2xl border border-[#17130d]/10 bg-white/70 px-3 py-2 text-xs font-black"><x-kabeeri-icon name="{{ $item['icon'] }}" />{{ $item['label'] }} <span class="block text-[11px] font-bold text-[#17130d]">{{ $item['caption'] }}</span></a>
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
                        <span class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-[10px] font-black uppercase tracking-[.16em] text-[#c98a2e]"><x-kabeeri-icon name="apps" />Apps</span>
                        <h1 class="mt-3 max-w-2xl text-lg font-black leading-tight tracking-[-.02em] sm:text-xl">لوحة إدارة التطبيقات</h1>
                    </div>
                    <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs font-bold text-white/70">
                        <span><strong class="text-[#fffaf0]">{{ $workspaceReady ? 'جاهزة' : 'تحتاج إعداد' }}</strong> المساحة</span>
                        <span><strong class="text-[#fffaf0]">{{ $sites->count() }}</strong> تطبيق</span>
                        <span><strong class="text-[#fffaf0]">{{ $activeThemeCount }}</strong> ثيم</span>
                    </div>
                </div>
            </section>

            <section class="mt-4 grid gap-4 xl:grid-cols-[minmax(0,1.35fr)_minmax(280px,.65fr)]">
                <div id="apps" class="rounded-3xl border border-[#17130d]/10 bg-[#fffaf0]/84 p-4 backdrop-blur">
                    <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <span class="inline-flex items-center rounded-full bg-[#17130d]/10 px-2.5 py-1 text-[11px] font-black text-[#17130d]"><x-kabeeri-icon name="apps" />My Apps</span>
                            <h2 class="mt-2 text-base font-black tracking-[-.02em]">التطبيقات</h2>
                        </div>
                        <a href="{{ route('customer.onboarding') }}" class="inline-flex items-center rounded-full border border-[#17130d]/10 bg-white/70 px-3 py-1.5 text-xs font-black"><x-kabeeri-icon name="plus" />تطبيق جديد لاحقًا</a>
                    </div>

                    <div class="overflow-hidden rounded-2xl border border-[#17130d]/10 bg-white/58">
                        @forelse ($sites as $site)
                            <a href="{{ route('customer.apps.show', ['username' => $site->username]) }}" class="grid gap-2 border-b border-[#17130d]/10 px-3 py-2.5 transition last:border-b-0 hover:bg-[#fffaf0]/55 md:grid-cols-[minmax(0,1fr)_10rem_7rem] md:items-center">
                                <span>
                                    <strong class="block text-sm font-black">{{ $site->name }}</strong>
                                    <small class="mt-0.5 block text-xs text-[#17130d]">{{ $site->metadata['v16_app_type'] ?? $site->site_type }} / username: {{ $site->username }}</small>
                                </span>
                                <span class="rounded-full bg-[#17130d]/10 px-2.5 py-1 text-xs font-black text-[#17130d]">{{ $site->theme?->name ?? 'No theme' }}</span>
                                <span class="rounded-full bg-[#c98a2e]/15 px-2.5 py-1 text-center text-xs font-black text-[#17130d]">{{ $site->metadata['theme_install_status'] ?? $site->status }}</span>
                            </a>
                        @empty
                            <div class="p-5 text-center">
                                <p class="text-sm font-black">لا يوجد تطبيق بعد</p>
                                <p class="mt-1 text-xs text-[#17130d]">ابدأ onboarding لإنشاء أول موقع أو متجر.</p>
                                <a href="{{ route('customer.onboarding') }}" class="mt-3 inline-flex items-center rounded-full bg-[#17130d] px-4 py-2 text-xs font-black text-[#fffaf0]"><x-kabeeri-icon name="plus" />ابدأ الآن</a>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div id="actions" class="rounded-3xl border border-[#17130d]/10 bg-[#17130d] p-4 text-[#fffaf0]">
                    <span class="inline-flex items-center rounded-full bg-white/10 px-2.5 py-1 text-[11px] font-black uppercase tracking-[.14em] text-[#c98a2e]"><x-kabeeri-icon name="steps" />Next</span>
                    <h2 class="mt-2 text-base font-black tracking-[-.02em]">الخطوات التالية</h2>
                    <div class="mt-3 divide-y divide-white/10">
                        @foreach ($nextActions as $action)
                            <div class="flex items-center justify-between gap-3 py-2.5">
                                <strong class="text-sm font-black">{{ $action['title'] }}</strong>
                                <span class="shrink-0 rounded-full bg-[#c98a2e] px-2 py-0.5 text-[10px] font-black text-[#17130d]">{{ $action['state'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="capabilities" class="mt-4 grid gap-4 xl:grid-cols-[minmax(0,1fr)_minmax(280px,.55fr)]">
                <div class="rounded-3xl border border-[#17130d]/10 bg-[#fffaf0]/84 p-4 backdrop-blur">
                    <div class="mb-3 flex items-center justify-between gap-2">
                        <div>
                            <span class="inline-flex items-center rounded-full bg-[#17130d]/10 px-2.5 py-1 text-[11px] font-black text-[#17130d]"><x-kabeeri-icon name="account" />Capabilities</span>
                            <h2 class="mt-2 text-base font-black tracking-[-.02em]">الأدوار والقدرات</h2>
                        </div>
                        <span class="rounded-full bg-white/70 px-2.5 py-1 text-xs font-black text-[#17130d]">{{ count($capabilityValues) }} مفعلة</span>
                    </div>
                    <form method="POST" action="{{ route('customer.capabilities.update') }}">
                        @csrf
                        <div class="grid gap-1.5 md:grid-cols-2">
                            @foreach ($capabilities as $key => $capability)
                                <label class="flex gap-2 rounded-2xl border border-transparent px-2 py-2 transition hover:border-[#17130d]/15 hover:bg-white/55">
                                    <input class="mt-1 h-4 w-4 rounded border-[#17130d]/20 text-[#17130d]" type="checkbox" name="capabilities[]" value="{{ $key }}" @checked(in_array($key, $capabilityValues, true)) @disabled($key === 'customer_owner')>
                                    <span>
                                        <strong class="block text-sm font-black">{{ $capability['label'] }}</strong>
                                        <span class="mt-0.5 block line-clamp-1 text-[11px] leading-5 text-[#17130d]">{{ $capability['description'] }}</span>
                                    </span>
                                </label>
                                @if ($key === 'customer_owner')
                                    <input type="hidden" name="capabilities[]" value="customer_owner">
                                @endif
                            @endforeach
                        </div>
                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-black" for="capability_note">ملاحظة مختصرة</label>
                            <textarea id="capability_note" name="capability_note" class="min-h-16 w-full rounded-2xl border border-[#17130d]/10 bg-white/70 p-3 text-sm leading-6 outline-none transition focus:border-[#c98a2e]">{{ $profile->metadata['capability_note'] ?? '' }}</textarea>
                        </div>
                        <button class="mt-3 inline-flex items-center rounded-full bg-[#17130d] px-4 py-2 text-xs font-black text-[#fffaf0]" type="submit"><x-kabeeri-icon name="check" />تحديث القدرات</button>
                    </form>
                </div>

                <div id="builder-help" class="rounded-3xl border border-[#17130d]/10 bg-[#fffaf0]/84 p-4 backdrop-blur">
                    <span class="inline-flex items-center rounded-full bg-[#c98a2e]/15 px-2.5 py-1 text-[11px] font-black text-[#17130d]"><x-kabeeri-icon name="builder" />Builder Help</span>
                    <h2 class="mt-2 text-base font-black tracking-[-.02em]">مساعدة التنفيذ</h2>
                    <div class="mt-3 border-t border-[#17130d]/10 pt-3">
                        <strong class="block text-sm font-black">{{ $needsBuilderHelp ? 'تم طلب مساعدة Builder' : 'اختياري لاحقًا' }}</strong>
                        @if ($builderNote)
                            <p class="mt-2 line-clamp-2 text-xs leading-5 text-[#17130d]">{{ $builderNote }}</p>
                        @endif
                    </div>
                </div>
            </section>

            <section id="marketplace" class="mt-4 rounded-3xl border border-[#17130d]/10 bg-[#fffaf0]/84 p-4 backdrop-blur">
                <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <span class="inline-flex items-center rounded-full bg-[#c98a2e]/15 px-2.5 py-1 text-[11px] font-black text-[#17130d]"><x-kabeeri-icon name="mall" />Marketplace</span>
                        <h2 class="mt-2 text-base font-black tracking-[-.02em]">الإضافات والثيمات</h2>
                    </div>
                </div>
                <div class="divide-y divide-[#17130d]/10">
                    @foreach ($dashboard['cards'] as $card)
                        <article class="flex items-center justify-between gap-3 py-2.5">
                            <h3 class="text-sm font-black">{{ $card['label'] }}</h3>
                            <span class="shrink-0 rounded-full bg-[#c98a2e]/15 px-2 py-0.5 text-[10px] font-black text-[#17130d]">{{ $card['key'] }}</span>
                        </article>
                    @endforeach
                </div>
            </section>

            <section id="client-account" class="mt-4 rounded-3xl border border-[#17130d]/10 bg-[#fffaf0]/84 p-4 backdrop-blur">
                <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] lg:items-center">
                    <div>
                        <span class="inline-flex items-center rounded-full bg-[#17130d]/10 px-2.5 py-1 text-[11px] font-black text-[#17130d]"><x-kabeeri-icon name="account" />Account</span>
                        <h2 class="mt-2 text-base font-black tracking-[-.02em]">بيانات الحساب</h2>
                    </div>
                    <div class="text-sm leading-7 text-[#17130d]">
                        <strong class="font-black text-[#17130d]">{{ $user->name }}</strong>
                        <span class="mx-2 text-[#c98a2e]">/</span>
                        <span class="break-all">{{ $user->email }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center rounded-full border border-[#17130d]/10 bg-white/70 px-4 py-2 text-xs font-black"><x-kabeeri-icon name="logout" />تسجيل خروج</button>
                    </form>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
