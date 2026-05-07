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
        ['label' => 'Overview', 'target' => '#overview', 'caption' => 'ملخص سريع'],
        ['label' => 'Apps', 'target' => '#apps', 'caption' => 'التطبيقات'],
        ['label' => 'Actions', 'target' => '#actions', 'caption' => 'الخطوات'],
        ['label' => 'Capabilities', 'target' => '#capabilities', 'caption' => 'الأدوار'],
        ['label' => 'Marketplace', 'target' => '#marketplace', 'caption' => 'الإضافات'],
        ['label' => 'Account', 'target' => '#account', 'caption' => 'الحساب'],
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
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Dashboard - KABEERI</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=almarai:400,700,800|ibm-plex-sans-arabic:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#f7ecd8] text-kabeeri-ink antialiased">
    <div class="pointer-events-none fixed inset-0 -z-10 bg-[radial-gradient(circle_at_88%_8%,rgba(47,95,115,.16),transparent_26rem),radial-gradient(circle_at_5%_16%,rgba(201,138,46,.22),transparent_24rem),linear-gradient(135deg,#fffaf0_0%,#f1dfc1_54%,#dde5d8_100%)]"></div>
    <div class="pointer-events-none fixed inset-0 -z-10 opacity-20 [background-image:linear-gradient(rgba(23,19,13,.06)_1px,transparent_1px),linear-gradient(90deg,rgba(23,19,13,.045)_1px,transparent_1px)] [background-size:40px_40px] [mask-image:linear-gradient(to_bottom,#000,transparent_82%)]"></div>

    <div class="lg:grid lg:min-h-screen lg:grid-cols-[17rem_minmax(0,1fr)]">
        <aside id="workspace-sidebar" class="hidden border-l border-white/60 bg-[#17130d]/95 text-[#fffaf0] shadow-2xl shadow-[#452b10]/15 lg:sticky lg:top-0 lg:flex lg:h-screen lg:flex-col">
            <div class="border-b border-white/10 p-4">
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 place-items-center rounded-2xl bg-gradient-to-br from-[#c98a2e] via-[#9a5539] to-[#315f46] text-base font-black">K</span>
                    <div>
                        <p class="text-[11px] font-black uppercase tracking-[.22em] text-[#d8b06b]">KABEERI</p>
                        <h1 class="text-base font-black leading-tight">Sidebar Control</h1>
                    </div>
                </div>
            </div>

            <nav class="flex-1 space-y-1.5 overflow-y-auto px-3 py-4" aria-label="Customer dashboard sidebar">
                @foreach ($sidebarItems as $item)
                    <a href="{{ $item['target'] }}" class="group flex items-center justify-between rounded-2xl border border-white/10 bg-white/[.035] px-3 py-2.5 transition hover:border-[#d8b06b]/55 hover:bg-[#d8b06b]/12">
                        <span>
                            <span class="block text-sm font-black">{{ $item['label'] }}</span>
                            <span class="mt-0.5 block text-[11px] text-white/52">{{ $item['caption'] }}</span>
                        </span>
                        <span class="text-xs text-white/35 group-hover:text-[#d8b06b]">GO</span>
                    </a>
                @endforeach
            </nav>

            <div class="m-3 rounded-2xl border border-white/10 bg-white/[.055] p-3">
                <p class="text-[11px] font-black uppercase tracking-[.18em] text-[#d8b06b]">Workspace</p>
                <p class="mt-1 text-lg font-black">{{ $workspaceReady ? 'Active' : 'Needs setup' }}</p>
                <p class="mt-1 line-clamp-2 text-xs leading-6 text-white/58">{{ $activeOrganization?->name ?? 'ابدأ onboarding لإنشاء مساحة العمل الأولى.' }}</p>
                @unless ($workspaceReady)
                    <a href="{{ route('customer.onboarding') }}" class="mt-3 inline-flex w-full items-center justify-center rounded-full bg-[#fffaf0] px-3 py-2 text-xs font-black text-[#17130d]">ابدأ الآن</a>
                @endunless
            </div>
        </aside>

        <main class="min-w-0 px-3 py-3 sm:px-5 lg:px-6 lg:py-5">
            <header id="overview" class="sticky top-3 z-30 mb-4 rounded-3xl border border-white/70 bg-[#fffaf0]/82 px-3 py-2.5 shadow-kabeeri-soft backdrop-blur-2xl">
                <div class="flex flex-col gap-2.5 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="grid h-10 w-10 place-items-center rounded-2xl bg-[#17130d] text-sm font-black text-[#fffaf0] lg:hidden">K</span>
                        <div>
                            <p class="text-[11px] font-black uppercase tracking-[.18em] text-kabeeri-clay">Customer Dashboard</p>
                            <h2 class="text-lg font-black sm:text-xl">{{ $user->name }}، مركز تحكم مختصر وواضح.</h2>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-1.5">
                        <a href="{{ route('customer.start') }}" class="rounded-full border border-[#17130d]/10 bg-white/60 px-3 py-1.5 text-xs font-black">البداية</a>
                        <a href="{{ route('public.landing') }}" class="rounded-full border border-[#17130d]/10 bg-white/60 px-3 py-1.5 text-xs font-black">المنصة</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-full bg-[#17130d] px-3 py-1.5 text-xs font-black text-[#fffaf0]">خروج</button>
                        </form>
                    </div>
                </div>

                <details class="mt-2 lg:hidden">
                    <summary class="cursor-pointer rounded-2xl bg-[#17130d] px-3 py-2 text-xs font-black text-[#fffaf0]">فتح Sidebar Control</summary>
                    <nav class="mt-2 grid gap-1.5 sm:grid-cols-2" aria-label="Mobile dashboard sidebar">
                        @foreach ($sidebarItems as $item)
                            <a href="{{ $item['target'] }}" class="rounded-2xl border border-[#17130d]/10 bg-white/70 px-3 py-2 text-xs font-black">{{ $item['label'] }} <span class="block text-[11px] font-bold text-[#756a5e]">{{ $item['caption'] }}</span></a>
                        @endforeach
                    </nav>
                </details>
            </header>

            @if (session('status'))
                <div class="mb-4 rounded-2xl border border-emerald-700/20 bg-emerald-100/80 px-4 py-3 text-xs font-black text-emerald-800 shadow-kabeeri-soft">{{ session('status') }}</div>
            @endif

            <section class="rounded-[1.75rem] border border-white/70 bg-[#17130d] p-4 text-[#fffaf0] shadow-kabeeri-soft sm:p-5">
                <div class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_22rem] xl:items-center">
                    <div>
                        <span class="inline-flex rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-[11px] font-black uppercase tracking-[.16em] text-[#d8b06b]">Control Center</span>
                        <h1 class="mt-3 max-w-3xl text-2xl font-black leading-tight tracking-[-.035em] sm:text-4xl">لوحة أقل زحمة لإدارة التطبيق والقدرات.</h1>
                        <p class="mt-2 max-w-3xl text-sm leading-7 text-white/64">ركزت الصفحة على المهم الآن: حالة المساحة، التطبيقات، الخطوات التالية، وتحديث الدور داخل المنصة.</p>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="rounded-2xl border border-white/10 bg-white/[.075] p-3">
                            <p class="text-[11px] text-white/52">Workspaces</p>
                            <strong class="mt-1 block text-2xl font-black">{{ $organizations->count() }}</strong>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/[.075] p-3">
                            <p class="text-[11px] text-white/52">Apps</p>
                            <strong class="mt-1 block text-2xl font-black">{{ $sites->count() }}</strong>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/[.075] p-3">
                            <p class="text-[11px] text-white/52">Roles</p>
                            <strong class="mt-1 block text-2xl font-black">{{ count($capabilityValues) }}</strong>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-2xl border border-white/70 bg-[#fffaf0]/84 p-3.5 shadow-kabeeri-soft backdrop-blur">
                    <p class="text-xs font-bold text-[#756a5e]">الحالة</p>
                    <strong class="mt-1 block text-lg font-black">{{ $workspaceReady ? 'Workspace Active' : 'Needs Onboarding' }}</strong>
                </article>
                <article class="rounded-2xl border border-white/70 bg-[#fffaf0]/84 p-3.5 shadow-kabeeri-soft backdrop-blur">
                    <p class="text-xs font-bold text-[#756a5e]">الثيمات</p>
                    <strong class="mt-1 block text-lg font-black">{{ $activeThemeCount }} مثبت</strong>
                </article>
                <article class="rounded-2xl border border-white/70 bg-[#fffaf0]/84 p-3.5 shadow-kabeeri-soft backdrop-blur">
                    <p class="text-xs font-bold text-[#756a5e]">Builder</p>
                    <strong class="mt-1 block text-lg font-black">{{ $needsBuilderHelp ? 'Requested' : 'Optional' }}</strong>
                </article>
                <article class="rounded-2xl border border-white/70 bg-[#fffaf0]/84 p-3.5 shadow-kabeeri-soft backdrop-blur">
                    <p class="text-xs font-bold text-[#756a5e]">الدور</p>
                    <strong class="mt-1 block truncate text-lg font-black">{{ $capabilityNames->take(2)->implode(' / ') ?: 'Customer Owner' }}</strong>
                </article>
            </section>

            <section class="mt-4 grid gap-4 xl:grid-cols-[minmax(0,1.35fr)_minmax(280px,.65fr)]">
                <div id="apps" class="rounded-[1.75rem] border border-white/70 bg-[#fffaf0]/86 p-4 shadow-kabeeri-soft backdrop-blur">
                    <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <span class="rounded-full bg-[#315f46]/10 px-2.5 py-1 text-[11px] font-black text-[#315f46]">My Apps</span>
                            <h2 class="mt-2 text-xl font-black tracking-[-.025em]">التطبيقات</h2>
                        </div>
                        <a href="{{ route('customer.onboarding') }}" class="rounded-full border border-[#17130d]/10 bg-white/70 px-3 py-1.5 text-xs font-black">تطبيق جديد لاحقًا</a>
                    </div>

                    <div class="overflow-hidden rounded-2xl border border-[#17130d]/10 bg-white/58">
                        @forelse ($sites as $site)
                            <a href="{{ route('customer.apps.show', $site) }}" class="grid gap-2 border-b border-[#17130d]/10 px-3 py-2.5 transition last:border-b-0 hover:bg-[#f2ddb8]/55 md:grid-cols-[minmax(0,1fr)_10rem_7rem] md:items-center">
                                <span>
                                    <strong class="block text-sm font-black">{{ $site->name }}</strong>
                                    <small class="mt-0.5 block text-xs text-[#756a5e]">{{ $site->metadata['v16_app_type'] ?? $site->site_type }} / {{ $site->slug }}</small>
                                </span>
                                <span class="rounded-full bg-[#315f46]/10 px-2.5 py-1 text-xs font-black text-[#315f46]">{{ $site->theme?->name ?? 'No theme' }}</span>
                                <span class="rounded-full bg-[#c98a2e]/15 px-2.5 py-1 text-center text-xs font-black text-[#7b4b13]">{{ $site->metadata['theme_install_status'] ?? $site->status }}</span>
                            </a>
                        @empty
                            <div class="p-5 text-center">
                                <p class="text-sm font-black">لا يوجد تطبيق بعد</p>
                                <p class="mt-1 text-xs text-[#756a5e]">ابدأ onboarding لإنشاء أول موقع أو متجر.</p>
                                <a href="{{ route('customer.onboarding') }}" class="mt-3 inline-flex rounded-full bg-[#17130d] px-4 py-2 text-xs font-black text-[#fffaf0]">ابدأ الآن</a>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div id="actions" class="rounded-[1.75rem] border border-[#17130d]/10 bg-[#17130d] p-4 text-[#fffaf0] shadow-kabeeri-soft">
                    <span class="rounded-full bg-white/10 px-2.5 py-1 text-[11px] font-black uppercase tracking-[.14em] text-[#d8b06b]">Next</span>
                    <h2 class="mt-2 text-xl font-black tracking-[-.025em]">خطوات مختصرة</h2>
                    <div class="mt-3 space-y-2">
                        @foreach ($nextActions as $action)
                            <div class="rounded-2xl border border-white/10 bg-white/[.055] p-3">
                                <div class="flex items-start justify-between gap-2">
                                    <strong class="text-sm font-black">{{ $action['title'] }}</strong>
                                    <span class="rounded-full bg-[#d8b06b] px-2 py-0.5 text-[10px] font-black text-[#17130d]">{{ $action['state'] }}</span>
                                </div>
                                <p class="mt-1 text-xs leading-6 text-white/58">{{ $action['text'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="capabilities" class="mt-4 grid gap-4 xl:grid-cols-[minmax(0,1fr)_minmax(280px,.55fr)]">
                <div class="rounded-[1.75rem] border border-white/70 bg-[#fffaf0]/86 p-4 shadow-kabeeri-soft backdrop-blur">
                    <div class="mb-3 flex items-center justify-between gap-2">
                        <div>
                            <span class="rounded-full bg-[#315f46]/10 px-2.5 py-1 text-[11px] font-black text-[#315f46]">Capabilities</span>
                            <h2 class="mt-2 text-xl font-black tracking-[-.025em]">الأدوار والقدرات</h2>
                        </div>
                        <span class="rounded-full bg-white/70 px-2.5 py-1 text-xs font-black text-[#756a5e]">{{ count($capabilityValues) }} مفعلة</span>
                    </div>
                    <form method="POST" action="{{ route('customer.capabilities.update') }}">
                        @csrf
                        <div class="grid gap-2 md:grid-cols-2">
                            @foreach ($capabilities as $key => $capability)
                                <label class="flex gap-2 rounded-2xl border border-[#17130d]/10 bg-white/58 p-3 transition hover:border-[#315f46]/45 hover:bg-white/85">
                                    <input class="mt-1 h-4 w-4 rounded border-[#17130d]/20 text-[#315f46]" type="checkbox" name="capabilities[]" value="{{ $key }}" @checked(in_array($key, $capabilityValues, true)) @disabled($key === 'customer_owner')>
                                    <span>
                                        <strong class="block text-sm font-black">{{ $capability['label'] }}</strong>
                                        <span class="mt-0.5 block text-xs leading-6 text-[#756a5e]">{{ $capability['description'] }}</span>
                                    </span>
                                </label>
                                @if ($key === 'customer_owner')
                                    <input type="hidden" name="capabilities[]" value="customer_owner">
                                @endif
                            @endforeach
                        </div>
                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-black" for="capability_note">ملاحظة مختصرة</label>
                            <textarea id="capability_note" name="capability_note" class="min-h-20 w-full rounded-2xl border border-[#17130d]/10 bg-white/70 p-3 text-sm leading-7 outline-none transition focus:border-[#c98a2e]">{{ $profile->metadata['capability_note'] ?? '' }}</textarea>
                        </div>
                        <button class="mt-3 rounded-full bg-[#17130d] px-4 py-2 text-xs font-black text-[#fffaf0]" type="submit">تحديث القدرات</button>
                    </form>
                </div>

                <div id="builder-help" class="rounded-[1.75rem] border border-white/70 bg-[#fffaf0]/86 p-4 shadow-kabeeri-soft backdrop-blur">
                    <span class="rounded-full bg-[#c98a2e]/15 px-2.5 py-1 text-[11px] font-black text-[#7b4b13]">Builder Help</span>
                    <h2 class="mt-2 text-xl font-black tracking-[-.025em]">مساعدة التنفيذ</h2>
                    <p class="mt-2 text-xs leading-6 text-[#756a5e]">هذا المسار لمن يساعد العميل في بناء التطبيق والشركة والمحتوى، وليس مطور الثيمات.</p>
                    <div class="mt-3 rounded-2xl border border-[#17130d]/10 bg-white/58 p-3">
                        <p class="text-xs font-black text-[#756a5e]">الحالة</p>
                        <strong class="mt-1 block text-sm font-black">{{ $needsBuilderHelp ? 'تم طلب مساعدة Builder' : 'اختياري لاحقًا' }}</strong>
                        @if ($builderNote)
                            <p class="mt-2 text-xs leading-6 text-[#756a5e]">{{ $builderNote }}</p>
                        @endif
                    </div>
                </div>
            </section>

            <section id="marketplace" class="mt-4 rounded-[1.75rem] border border-white/70 bg-[#fffaf0]/86 p-4 shadow-kabeeri-soft backdrop-blur">
                <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <span class="rounded-full bg-[#c98a2e]/15 px-2.5 py-1 text-[11px] font-black text-[#7b4b13]">Marketplace</span>
                        <h2 class="mt-2 text-xl font-black tracking-[-.025em]">إضافات وثيمات لاحقة</h2>
                    </div>
                    <p class="max-w-xl text-xs leading-6 text-[#756a5e]">مختصر للإمكانات القادمة بدون ازدحام بصري.</p>
                </div>
                <div class="grid gap-2 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($dashboard['cards'] as $card)
                        <article class="rounded-2xl border border-[#17130d]/10 bg-white/58 p-3">
                            <div class="flex items-center justify-between gap-2">
                                <h3 class="text-sm font-black">{{ $card['label'] }}</h3>
                                <span class="rounded-full bg-[#c98a2e]/15 px-2 py-0.5 text-[10px] font-black text-[#7b4b13]">{{ $card['key'] }}</span>
                            </div>
                            <p class="mt-1 text-xs leading-6 text-[#756a5e]">{{ $card['text'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section id="account" class="mt-4 rounded-[1.75rem] border border-white/70 bg-[#fffaf0]/86 p-4 shadow-kabeeri-soft backdrop-blur">
                <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] lg:items-center">
                    <div>
                        <span class="rounded-full bg-[#315f46]/10 px-2.5 py-1 text-[11px] font-black text-[#315f46]">Account</span>
                        <h2 class="mt-2 text-xl font-black tracking-[-.025em]">بيانات الحساب</h2>
                    </div>
                    <div class="text-sm leading-7 text-[#756a5e]">
                        <strong class="font-black text-[#17130d]">{{ $user->name }}</strong>
                        <span class="mx-2 text-[#c98a2e]">/</span>
                        <span class="break-all">{{ $user->email }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-full border border-[#17130d]/10 bg-white/70 px-4 py-2 text-xs font-black">تسجيل خروج</button>
                    </form>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
