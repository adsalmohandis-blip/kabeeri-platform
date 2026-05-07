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
        ['label' => 'Overview', 'target' => '#overview', 'caption' => 'صورة عامة'],
        ['label' => 'Apps', 'target' => '#apps', 'caption' => 'التطبيقات والثيمات'],
        ['label' => 'Capabilities', 'target' => '#capabilities', 'caption' => 'تطوير دورك'],
        ['label' => 'Builder Help', 'target' => '#builder-help', 'caption' => 'مساعدة تنفيذ'],
        ['label' => 'Marketplace', 'target' => '#marketplace', 'caption' => 'إضافات لاحقة'],
        ['label' => 'Account', 'target' => '#account', 'caption' => 'الحساب والخروج'],
    ];
    $nextActions = $workspaceReady
        ? [
            ['title' => 'راجع التطبيق الأول', 'text' => 'افتح التطبيق وتأكد من الثيم والمحتوى الأولي.', 'state' => 'Ready'],
            ['title' => 'حدّث قدرات الحساب', 'text' => 'فعّل مسارات المطور أو المسوق أو Builder حسب احتياجك.', 'state' => 'Optional'],
            ['title' => 'اختر إضافات النمو', 'text' => 'التجارة، CRM، الظهور في Mall، وإدارة الفريق تأتي في المراحل التالية.', 'state' => 'Next'],
        ]
        : [
            ['title' => 'ابدأ Guided Onboarding', 'text' => 'أنشئ Workspace، تطبيق، وثيم أولي.', 'state' => 'Required'],
            ['title' => 'اختر نوع التطبيق', 'text' => 'Website أو Store أو Service Business أو Landing.', 'state' => 'Required'],
            ['title' => 'ثبت الثيم المناسب', 'text' => 'يتم اختيار ثيم متوافق مع نوع التطبيق.', 'state' => 'Required'],
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
<body class="min-h-screen overflow-x-hidden bg-[#f6ead6] text-kabeeri-ink antialiased">
    <div class="pointer-events-none fixed inset-0 -z-10 bg-[radial-gradient(circle_at_82%_8%,rgba(47,95,115,.22),transparent_30rem),radial-gradient(circle_at_8%_14%,rgba(201,138,46,.32),transparent_28rem),linear-gradient(135deg,#fffaf0_0%,#f2ddb8_48%,#d7dfcf_100%)]"></div>
    <div class="pointer-events-none fixed inset-0 -z-10 opacity-25 [background-image:linear-gradient(rgba(23,19,13,.08)_1px,transparent_1px),linear-gradient(90deg,rgba(23,19,13,.06)_1px,transparent_1px)] [background-size:42px_42px] [mask-image:linear-gradient(to_bottom,#000,transparent_82%)]"></div>

    <div class="lg:grid lg:min-h-screen lg:grid-cols-[21rem_minmax(0,1fr)]">
        <aside id="workspace-sidebar" class="hidden border-l border-white/60 bg-[#17130d]/95 text-[#fffaf0] shadow-2xl shadow-[#452b10]/20 lg:sticky lg:top-0 lg:flex lg:h-screen lg:flex-col">
            <div class="flex items-center gap-3 border-b border-white/10 p-6">
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-gradient-to-br from-[#c98a2e] via-[#9a5539] to-[#315f46] text-lg font-black">K</span>
                <div>
                    <p class="text-sm font-black uppercase tracking-[.24em] text-[#d8b06b]">KABEERI</p>
                    <h1 class="text-xl font-black leading-tight">Sidebar Control</h1>
                </div>
            </div>

            <nav class="flex-1 space-y-2 overflow-y-auto px-4 py-5" aria-label="Customer dashboard sidebar">
                @foreach ($sidebarItems as $item)
                    <a href="{{ $item['target'] }}" class="group flex items-center justify-between rounded-3xl border border-white/10 bg-white/[.04] px-4 py-3 transition hover:border-[#d8b06b]/60 hover:bg-[#d8b06b]/15">
                        <span>
                            <span class="block text-sm font-black">{{ $item['label'] }}</span>
                            <span class="mt-1 block text-xs text-white/55">{{ $item['caption'] }}</span>
                        </span>
                        <span class="grid h-8 w-8 place-items-center rounded-full bg-white/10 text-xs transition group-hover:bg-[#d8b06b] group-hover:text-[#17130d]">↗</span>
                    </a>
                @endforeach
            </nav>

            <div class="m-4 rounded-[2rem] border border-white/10 bg-white/[.06] p-4">
                <p class="text-xs font-black uppercase tracking-[.18em] text-[#d8b06b]">Workspace state</p>
                <p class="mt-2 text-2xl font-black">{{ $workspaceReady ? 'Active' : 'Setup needed' }}</p>
                <p class="mt-2 text-sm leading-7 text-white/60">{{ $activeOrganization?->name ?? 'ابدأ الـ onboarding لإنشاء مساحة العمل الأولى.' }}</p>
                @unless ($workspaceReady)
                    <a href="{{ route('customer.onboarding') }}" class="mt-4 inline-flex w-full items-center justify-center rounded-full bg-[#fffaf0] px-4 py-3 text-sm font-black text-[#17130d]">ابدأ الآن</a>
                @endunless
            </div>
        </aside>

        <main class="min-w-0 px-4 py-4 sm:px-6 lg:px-8 lg:py-6">
            <header id="overview" class="sticky top-3 z-30 mb-5 rounded-[2rem] border border-white/70 bg-[#fffaf0]/80 p-3 shadow-kabeeri-soft backdrop-blur-2xl">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-3">
                        <span class="grid h-12 w-12 place-items-center rounded-2xl bg-[#17130d] text-lg font-black text-[#fffaf0] lg:hidden">K</span>
                        <div>
                            <p class="text-xs font-black uppercase tracking-[.18em] text-kabeeri-clay">Customer Dashboard</p>
                            <h2 class="text-xl font-black sm:text-2xl">{{ $user->name }}، مركز التحكم جاهز.</h2>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('customer.start') }}" class="rounded-full border border-[#17130d]/10 bg-white/60 px-4 py-2 text-sm font-black">البداية</a>
                        <a href="{{ route('public.landing') }}" class="rounded-full border border-[#17130d]/10 bg-white/60 px-4 py-2 text-sm font-black">المنصة</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-full bg-[#17130d] px-4 py-2 text-sm font-black text-[#fffaf0]">خروج</button>
                        </form>
                    </div>
                </div>

                <details class="mt-3 lg:hidden">
                    <summary class="cursor-pointer rounded-2xl bg-[#17130d] px-4 py-3 text-sm font-black text-[#fffaf0]">فتح Sidebar Control</summary>
                    <nav class="mt-3 grid gap-2 sm:grid-cols-2" aria-label="Mobile dashboard sidebar">
                        @foreach ($sidebarItems as $item)
                            <a href="{{ $item['target'] }}" class="rounded-2xl border border-[#17130d]/10 bg-white/70 px-4 py-3 text-sm font-black">{{ $item['label'] }} <span class="block text-xs font-bold text-[#756a5e]">{{ $item['caption'] }}</span></a>
                        @endforeach
                    </nav>
                </details>
            </header>

            @if (session('status'))
                <div class="mb-5 rounded-[1.5rem] border border-emerald-700/20 bg-emerald-100/80 px-5 py-4 text-sm font-black text-emerald-800 shadow-kabeeri-soft">{{ session('status') }}</div>
            @endif

            <section class="relative overflow-hidden rounded-[2.5rem] border border-white/70 bg-[#17130d] p-6 text-[#fffaf0] shadow-kabeeri-strong sm:p-8 lg:p-10">
                <div class="absolute -left-20 -top-24 h-80 w-80 rounded-full bg-[#c98a2e]/25 blur-3xl"></div>
                <div class="absolute -bottom-28 right-1/3 h-72 w-72 rounded-full bg-[#315f46]/35 blur-3xl"></div>
                <div class="relative grid gap-8 xl:grid-cols-[1.1fr_.9fr] xl:items-end">
                    <div>
                        <span class="inline-flex rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-black uppercase tracking-[.18em] text-[#d8b06b]">Control Center</span>
                        <h1 class="mt-5 max-w-4xl text-4xl font-black leading-[1.05] tracking-[-.04em] sm:text-6xl">لوحة تشغيل احترافية لإدارة التطبيق والمسار والقدرات.</h1>
                        <p class="mt-5 max-w-3xl text-lg leading-9 text-white/70">هذه ليست صفحة مؤشرات فقط. هنا العميل يفهم أين هو الآن، ما التطبيقات المفعلة، ما الثيم المثبت، وما الخطوة التالية لبناء منصة كاملة بدون دخول لوحة الأدمن.</p>
                        <div class="mt-6 flex flex-wrap gap-2">
                            @if ($workspaceReady)
                                <a href="#apps" class="rounded-full bg-[#fffaf0] px-5 py-3 text-sm font-black text-[#17130d]">افتح التطبيقات</a>
                            @else
                                <a href="{{ route('customer.onboarding') }}" class="rounded-full bg-[#fffaf0] px-5 py-3 text-sm font-black text-[#17130d]">ابدأ Guided Onboarding</a>
                            @endif
                            <a href="#capabilities" class="rounded-full border border-white/20 bg-white/10 px-5 py-3 text-sm font-black text-white">تحديث القدرات</a>
                        </div>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-3 xl:grid-cols-1">
                        <div class="rounded-[1.75rem] border border-white/10 bg-white/[.08] p-5 backdrop-blur">
                            <p class="text-sm text-white/55">Workspace</p>
                            <strong class="mt-2 block text-3xl font-black">{{ $organizations->count() }}</strong>
                        </div>
                        <div class="rounded-[1.75rem] border border-white/10 bg-white/[.08] p-5 backdrop-blur">
                            <p class="text-sm text-white/55">Active Apps</p>
                            <strong class="mt-2 block text-3xl font-black">{{ $sites->count() }}</strong>
                        </div>
                        <div class="rounded-[1.75rem] border border-white/10 bg-white/[.08] p-5 backdrop-blur">
                            <p class="text-sm text-white/55">Capabilities</p>
                            <strong class="mt-2 block text-3xl font-black">{{ count($capabilityValues) }}</strong>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-[2rem] border border-white/70 bg-[#fffaf0]/82 p-5 shadow-kabeeri-soft backdrop-blur">
                    <p class="text-sm font-bold text-[#756a5e]">الحالة</p>
                    <strong class="mt-2 block text-2xl font-black">{{ $workspaceReady ? 'Workspace Active' : 'Needs Onboarding' }}</strong>
                </article>
                <article class="rounded-[2rem] border border-white/70 bg-[#fffaf0]/82 p-5 shadow-kabeeri-soft backdrop-blur">
                    <p class="text-sm font-bold text-[#756a5e]">الثيمات المثبتة</p>
                    <strong class="mt-2 block text-2xl font-black">{{ $activeThemeCount }}</strong>
                </article>
                <article class="rounded-[2rem] border border-white/70 bg-[#fffaf0]/82 p-5 shadow-kabeeri-soft backdrop-blur">
                    <p class="text-sm font-bold text-[#756a5e]">Builder Help</p>
                    <strong class="mt-2 block text-2xl font-black">{{ $needsBuilderHelp ? 'Requested' : 'Optional' }}</strong>
                </article>
                <article class="rounded-[2rem] border border-white/70 bg-[#fffaf0]/82 p-5 shadow-kabeeri-soft backdrop-blur">
                    <p class="text-sm font-bold text-[#756a5e]">مسارات الحساب</p>
                    <strong class="mt-2 block text-2xl font-black">{{ $capabilityNames->take(2)->implode(' / ') ?: 'Customer Owner' }}</strong>
                </article>
            </section>

            <section class="mt-5 grid gap-5 xl:grid-cols-[minmax(0,1.15fr)_minmax(360px,.85fr)]">
                <div id="apps" class="rounded-[2.25rem] border border-white/70 bg-[#fffaf0]/84 p-5 shadow-kabeeri-soft backdrop-blur sm:p-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <span class="kbr-pill">My Apps</span>
                            <h2 class="mt-3 text-3xl font-black tracking-[-.035em]">التطبيقات والثيمات</h2>
                            <p class="mt-2 leading-8 text-[#756a5e]">افتح التطبيق، راجع الثيم، وتأكد من حالة التفعيل.</p>
                        </div>
                        <a href="{{ route('customer.onboarding') }}" class="rounded-full border border-[#17130d]/10 bg-white/70 px-4 py-2 text-sm font-black">تطبيق جديد لاحقًا</a>
                    </div>

                    <div class="mt-5 overflow-hidden rounded-[1.75rem] border border-[#17130d]/10 bg-white/55">
                        @forelse ($sites as $site)
                            <a href="{{ route('customer.apps.show', $site) }}" class="grid gap-3 border-b border-[#17130d]/10 p-4 transition last:border-b-0 hover:bg-[#f2ddb8]/55 md:grid-cols-[minmax(0,1fr)_11rem_9rem] md:items-center">
                                <span>
                                    <strong class="block text-lg font-black">{{ $site->name }}</strong>
                                    <small class="mt-1 block text-sm text-[#756a5e]">{{ $site->metadata['v16_app_type'] ?? $site->site_type }} / {{ $site->slug }}</small>
                                </span>
                                <span class="rounded-full bg-[#315f46]/10 px-3 py-2 text-sm font-black text-[#315f46]">{{ $site->theme?->name ?? 'No theme' }}</span>
                                <span class="rounded-full bg-[#c98a2e]/15 px-3 py-2 text-center text-sm font-black text-[#7b4b13]">{{ $site->metadata['theme_install_status'] ?? $site->status }}</span>
                            </a>
                        @empty
                            <div class="p-6 text-center">
                                <p class="text-lg font-black">لا يوجد تطبيق بعد</p>
                                <p class="mt-2 text-[#756a5e]">ابدأ الـ onboarding لإنشاء أول موقع أو متجر.</p>
                                <a href="{{ route('customer.onboarding') }}" class="mt-4 inline-flex rounded-full bg-[#17130d] px-5 py-3 text-sm font-black text-[#fffaf0]">ابدأ الآن</a>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-[2.25rem] border border-[#17130d]/10 bg-[#17130d] p-5 text-[#fffaf0] shadow-kabeeri-strong sm:p-6">
                    <span class="inline-flex rounded-full bg-white/10 px-3 py-2 text-xs font-black uppercase tracking-[.16em] text-[#d8b06b]">Next Actions</span>
                    <h2 class="mt-3 text-3xl font-black tracking-[-.035em]">الخطوات التالية</h2>
                    <div class="mt-5 space-y-3">
                        @foreach ($nextActions as $action)
                            <div class="rounded-[1.5rem] border border-white/10 bg-white/[.06] p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <strong class="font-black">{{ $action['title'] }}</strong>
                                    <span class="rounded-full bg-[#d8b06b] px-3 py-1 text-xs font-black text-[#17130d]">{{ $action['state'] }}</span>
                                </div>
                                <p class="mt-2 leading-7 text-white/62">{{ $action['text'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="capabilities" class="mt-5 grid gap-5 xl:grid-cols-[minmax(360px,.88fr)_minmax(0,1.12fr)]">
                <div id="builder-help" class="rounded-[2.25rem] border border-white/70 bg-[#fffaf0]/84 p-5 shadow-kabeeri-soft backdrop-blur sm:p-6">
                    <span class="kbr-pill">Builder Help</span>
                    <h2 class="mt-3 text-3xl font-black tracking-[-.035em]">مساعدة البناء والتنفيذ</h2>
                    <p class="mt-3 leading-8 text-[#756a5e]">Kabeeri Builder مختلف عن مطور الثيمات. هذا مسار لشخص يساعد العميل في بناء التطبيق، إعداد الشركة، وتجهيز المحتوى.</p>
                    <div class="mt-5 rounded-[1.75rem] border border-[#17130d]/10 bg-white/60 p-4">
                        <p class="text-sm font-black text-[#756a5e]">الحالة الحالية</p>
                        <strong class="mt-2 block text-2xl font-black">{{ $needsBuilderHelp ? 'العميل طلب مساعدة Builder' : 'لم يتم طلب مساعدة بعد' }}</strong>
                        @if ($builderNote)
                            <p class="mt-3 leading-8 text-[#756a5e]">{{ $builderNote }}</p>
                        @endif
                    </div>
                </div>

                <div class="rounded-[2.25rem] border border-white/70 bg-[#fffaf0]/84 p-5 shadow-kabeeri-soft backdrop-blur sm:p-6">
                    <span class="kbr-pill">Capabilities</span>
                    <h2 class="mt-3 text-3xl font-black tracking-[-.035em]">تطوير دورك داخل المنصة</h2>
                    <form class="mt-5" method="POST" action="{{ route('customer.capabilities.update') }}">
                        @csrf
                        <div class="grid gap-3 md:grid-cols-2">
                            @foreach ($capabilities as $key => $capability)
                                <label class="flex min-h-28 gap-3 rounded-[1.5rem] border border-[#17130d]/10 bg-white/60 p-4 transition hover:border-[#315f46]/45 hover:bg-white/85">
                                    <input class="mt-1 h-5 w-5 rounded border-[#17130d]/20 text-[#315f46]" type="checkbox" name="capabilities[]" value="{{ $key }}" @checked(in_array($key, $capabilityValues, true)) @disabled($key === 'customer_owner')>
                                    <span>
                                        <strong class="block font-black">{{ $capability['label'] }}</strong>
                                        <span class="mt-1 block text-sm leading-7 text-[#756a5e]">{{ $capability['description'] }}</span>
                                    </span>
                                </label>
                                @if ($key === 'customer_owner')
                                    <input type="hidden" name="capabilities[]" value="customer_owner">
                                @endif
                            @endforeach
                        </div>
                        <div class="mt-4">
                            <label class="mb-2 block text-sm font-black" for="capability_note">ملاحظة للملف أو Builder</label>
                            <textarea id="capability_note" name="capability_note" class="min-h-32 w-full rounded-[1.5rem] border border-[#17130d]/10 bg-white/70 p-4 leading-8 outline-none transition focus:border-[#c98a2e]">{{ $profile->metadata['capability_note'] ?? '' }}</textarea>
                        </div>
                        <button class="mt-4 rounded-full bg-[#17130d] px-6 py-3 text-sm font-black text-[#fffaf0]" type="submit">تحديث القدرات</button>
                    </form>
                </div>
            </section>

            <section id="marketplace" class="mt-5 rounded-[2.25rem] border border-white/70 bg-[#fffaf0]/84 p-5 shadow-kabeeri-soft backdrop-blur sm:p-6">
                <div class="flex flex-col gap-3 xl:flex-row xl:items-end xl:justify-between">
                    <div>
                        <span class="kbr-pill">Marketplace Roadmap</span>
                        <h2 class="mt-3 text-3xl font-black tracking-[-.035em]">إضافات وثيمات وخدمات يمكن تفعيلها لاحقًا</h2>
                    </div>
                    <p class="max-w-2xl leading-8 text-[#756a5e]">هذا القسم يشرح للعميل ما الذي سيحصل عليه لاحقًا: إضافات التجارة، CRM، الظهور في Mall، الفريق، والـ Builder marketplace.</p>
                </div>
                <div class="mt-5 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($dashboard['cards'] as $card)
                        <article class="rounded-[1.75rem] border border-[#17130d]/10 bg-white/60 p-5">
                            <span class="rounded-full bg-[#c98a2e]/15 px-3 py-1 text-xs font-black text-[#7b4b13]">{{ $card['key'] }}</span>
                            <h3 class="mt-4 text-xl font-black">{{ $card['label'] }}</h3>
                            <p class="mt-2 leading-8 text-[#756a5e]">{{ $card['text'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section id="account" class="mt-5 rounded-[2.25rem] border border-white/70 bg-[#fffaf0]/84 p-5 shadow-kabeeri-soft backdrop-blur sm:p-6">
                <div class="grid gap-4 lg:grid-cols-3">
                    <div>
                        <span class="kbr-pill">Account</span>
                        <h2 class="mt-3 text-3xl font-black tracking-[-.035em]">بيانات الحساب</h2>
                    </div>
                    <div class="rounded-[1.5rem] border border-[#17130d]/10 bg-white/60 p-4">
                        <p class="text-sm font-black text-[#756a5e]">الاسم</p>
                        <strong class="mt-2 block text-xl font-black">{{ $user->name }}</strong>
                    </div>
                    <div class="rounded-[1.5rem] border border-[#17130d]/10 bg-white/60 p-4">
                        <p class="text-sm font-black text-[#756a5e]">البريد</p>
                        <strong class="mt-2 block break-all text-xl font-black">{{ $user->email }}</strong>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
