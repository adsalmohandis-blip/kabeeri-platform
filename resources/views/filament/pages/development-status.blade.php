@php
    $isArabic = app()->getLocale() === 'ar';
    $copy = fn (string $arabic, string $english): string => $isArabic ? $arabic : $english;
    $brand = $isArabic ? 'كبيري' : 'KABEERI';
    $fmt = fn ($value): string => number_format((int) $value);
    $percent = fn ($value): int => max(0, min(100, (int) $value));

    $summary = $dashboard['task_summary'] ?? [];
    $backend = $dashboard['backend_summary'] ?? [];
    $ui = $dashboard['ui_summary'] ?? [];
    $release = $dashboard['release_status'] ?? [];
    $versions = $dashboard['versions'] ?? [];
    $history = array_slice($dashboard['latest_history'] ?? [], 0, 6);
    $inventory = $dashboard['system_inventory'] ?? [];
    $databaseGroups = array_slice($dashboard['database_groups'] ?? [], 0, 4);
    $checklist = $dashboard['production_checklist'] ?? [];

    $releaseReady = (bool) ($release['staging_ready'] ?? false);
    $productionReady = (bool) ($release['production_ready'] ?? false);

    $stats = [
        [
            'label' => $copy('إجمالي التقدم', 'Total progress'),
            'value' => ($summary['percent'] ?? 0).'%',
            'note' => $fmt($summary['done'] ?? 0).' / '.$fmt($summary['total'] ?? 0),
            'ready' => ($summary['pending'] ?? 1) === 0 && ($summary['blocked'] ?? 1) === 0,
        ],
        [
            'label' => $copy('الباك إند', 'Backend'),
            'value' => ($backend['percent'] ?? 0).'%',
            'note' => $fmt($backend['done'] ?? 0).' / '.$fmt($backend['total'] ?? 0),
            'ready' => ($backend['pending'] ?? 1) === 0 && ($backend['blocked'] ?? 1) === 0,
        ],
        [
            'label' => $copy('واجهات الاستخدام', 'User interfaces'),
            'value' => ($ui['percent'] ?? 0).'%',
            'note' => $fmt($ui['done'] ?? 0).' / '.$fmt($ui['total'] ?? 0),
            'ready' => ($ui['pending'] ?? 1) === 0 && ($ui['blocked'] ?? 1) === 0,
        ],
        [
            'label' => $copy('جاهزية النشر', 'Release readiness'),
            'value' => $releaseReady ? $copy('جاهز', 'Ready') : $copy('مراجعة', 'Review'),
            'note' => $productionReady ? $copy('إنتاج بعد الاختبار', 'Production after staging') : $copy('يلزم تأكيد نهائي', 'Final verification needed'),
            'ready' => $releaseReady,
        ],
    ];

    $adminLinks = [
        ['label' => $copy('فحص النظام', 'System check'), 'route' => 'filament.admin.pages.system-check'],
        ['label' => $copy('تتبع التاسكات', 'Task tracker'), 'route' => 'filament.admin.pages.task-tracker-status'],
        ['label' => $copy('حالة البيانات', 'Database status'), 'route' => 'filament.admin.pages.database-status'],
        ['label' => $copy('صحة الوحدات', 'Module health'), 'route' => 'filament.admin.pages.module-health'],
        ['label' => $copy('جاهزية الإصدار', 'Release readiness'), 'route' => 'filament.admin.pages.release-readiness'],
        ['label' => $copy('مساحات الأدمن', 'Admin workspaces'), 'route' => 'filament.admin.pages.admin-workspaces'],
    ];

    $inventoryLabel = fn (string $label): string => match ($label) {
        'Migrations' => $copy('الهجرات', 'Migrations'),
        'Models' => $copy('النماذج', 'Models'),
        'Filament Resources' => $copy('موارد الأدمن', 'Admin resources'),
        'Feature Tests' => $copy('اختبارات السلوك', 'Feature tests'),
        'Docs' => $copy('التوثيق', 'Docs'),
        default => $label,
    };

    $checklistTitle = fn (string $title): string => match ($title) {
        'Task Tracker' => $copy('تتبع التاسكات', 'Task tracker'),
        'Automated Tests' => $copy('الاختبارات الآلية', 'Automated tests'),
        'Next.js Runtime' => $copy('تشغيل الواجهة العامة', 'Public runtime'),
        'Owner Verification' => $copy('تأكيد المالك', 'Owner verification'),
        'Staging Environment' => $copy('بيئة التجربة', 'Staging environment'),
        'Production Secrets' => $copy('أسرار الإنتاج', 'Production secrets'),
        default => $title,
    };

    $statusLabel = fn (?string $status): string => match ($status) {
        'ready', 'codex_done', 'verified' => $copy('جاهز', 'Ready'),
        'review' => $copy('مراجعة', 'Review'),
        'pending' => $copy('متبقي', 'Pending'),
        'in_progress' => $copy('قيد التنفيذ', 'In progress'),
        'blocked' => $copy('متوقف', 'Blocked'),
        default => (string) $status,
    };
@endphp

<x-filament-panels::page>
    <div class="space-y-6" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
        <section class="overflow-hidden rounded-3xl border border-[#17130d]/10 bg-[#17130d] text-[#fffaf0] shadow-sm">
            <div class="grid gap-6 p-6 lg:grid-cols-[1fr_280px] lg:items-end">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 rounded-full border border-[#c98a2e]/40 bg-[#c98a2e]/10 px-3 py-1 text-xs font-bold text-[#f4c46b]">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 13h4l2-6 4 12 2-6h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>{{ $brand }}</span>
                    </div>
                    <h1 class="mt-4 text-2xl font-black tracking-tight sm:text-4xl">{{ $copy('لوحة حالة التطوير', 'Development status') }}</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-7 text-[#fffaf0]/75">
                        {{ $copy('ملخص داخلي واضح لحالة التنفيذ، البيانات، التتبع، وجاهزية النشر من داخل لوحة الأدمن.', 'A focused internal view of implementation, data, tracking, and release readiness inside the admin dashboard.') }}
                    </p>
                </div>

                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                    <p class="text-xs font-bold text-[#f4c46b]">{{ $copy('إشارة الإصدار', 'Release signal') }}</p>
                    <p class="mt-2 text-2xl font-black">{{ $releaseReady ? $copy('جاهز للمراجعة', 'Ready for review') : $copy('يحتاج متابعة', 'Needs follow-up') }}</p>
                    <p class="mt-1 text-sm text-[#fffaf0]/65">{{ $productionReady ? $copy('جاهز بعد اختبار بيئة التجربة.', 'Ready after staging verification.') : $copy('يلزم تأكيد المالك وبيئة التجربة قبل الإنتاج.', 'Owner and staging verification are still required.') }}</p>
                </div>
            </div>
        </section>

        <section class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($stats as $stat)
                <article class="rounded-2xl border border-[#17130d]/10 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-bold text-[#17130d]/55 dark:text-[#fffaf0]/55">{{ $stat['label'] }}</p>
                            <p class="mt-2 text-2xl font-black text-[#17130d] dark:text-[#fffaf0]">{{ $stat['value'] }}</p>
                        </div>
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-full {{ $stat['ready'] ? 'bg-[#17130d] text-[#fffaf0]' : 'bg-[#fffaf0] text-[#17130d] ring-1 ring-[#c98a2e]/30' }}">
                            @if ($stat['ready'])
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            @else
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="1.8" />
                                </svg>
                            @endif
                        </span>
                    </div>
                    <p class="mt-3 text-sm text-[#17130d]/60 dark:text-[#fffaf0]/60">{{ $stat['note'] }}</p>
                </article>
            @endforeach
        </section>

        <section class="grid gap-4 lg:grid-cols-[1fr_360px]">
            <article class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold text-[#c98a2e]">{{ $copy('المسارات الداخلية', 'Internal paths') }}</p>
                        <h2 class="mt-1 text-xl font-black text-[#17130d] dark:text-[#fffaf0]">{{ $copy('اختصارات لوحة الأدمن', 'Admin shortcuts') }}</h2>
                    </div>
                    <a class="inline-flex items-center gap-2 rounded-full bg-[#17130d] px-4 py-2 text-xs font-bold text-[#fffaf0] ring-1 ring-[#17130d]/10 transition hover:bg-[#2a2116] dark:bg-[#fffaf0] dark:text-[#17130d]" href="{{ route('filament.admin.pages.development-status') }}">
                        <span>{{ $copy('الصفحة الحالية', 'Current page') }}</span>
                    </a>
                </div>

                <div class="mt-4 grid gap-2 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($adminLinks as $link)
                        <a class="group flex items-center justify-between gap-3 rounded-2xl border border-[#17130d]/10 bg-[#fffaf0] px-4 py-3 text-sm font-bold text-[#17130d] transition hover:border-[#c98a2e]/60 hover:bg-white dark:border-white/10 dark:bg-white/5 dark:text-[#fffaf0]" href="{{ route($link['route']) }}">
                            <span>{{ $link['label'] }}</span>
                            <svg class="h-4 w-4 transition group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                    @endforeach
                </div>
            </article>

            <article class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                <p class="text-xs font-bold text-[#c98a2e]">{{ $copy('ملخص سريع', 'Quick summary') }}</p>
                <h2 class="mt-1 text-xl font-black text-[#17130d] dark:text-[#fffaf0]">{{ $copy('مخزون النظام', 'System inventory') }}</h2>
                <div class="mt-4 space-y-2">
                    @foreach (array_slice($inventory, 0, 5) as $item)
                        <div class="flex items-center justify-between gap-3 rounded-2xl bg-[#fffaf0] px-4 py-3 text-sm dark:bg-white/5">
                            <span class="text-[#17130d]/65 dark:text-[#fffaf0]/65">{{ $inventoryLabel($item['label'] ?? '') }}</span>
                            <strong class="text-[#17130d] dark:text-[#fffaf0]">{{ $fmt($item['value'] ?? 0) }}</strong>
                        </div>
                    @endforeach
                </div>
            </article>
        </section>

        <section class="grid gap-4 xl:grid-cols-[1.15fr_.85fr]">
            <article class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold text-[#c98a2e]">{{ $copy('التقدم الحقيقي', 'Real progress') }}</p>
                        <h2 class="mt-1 text-xl font-black text-[#17130d] dark:text-[#fffaf0]">{{ $copy('حالة النسخ', 'Version status') }}</h2>
                    </div>
                    <span class="rounded-full bg-[#17130d] px-3 py-1 text-xs font-bold text-[#fffaf0] dark:bg-[#fffaf0] dark:text-[#17130d]">{{ $summary['percent'] ?? 0 }}%</span>
                </div>

                <div class="mt-4 space-y-3">
                    @foreach ($versions as $version)
                        <div class="rounded-2xl bg-[#fffaf0] p-4 dark:bg-white/5">
                            <div class="flex flex-wrap items-center justify-between gap-2 text-sm">
                                <strong class="text-[#17130d] dark:text-[#fffaf0]">{{ $version['name'] }}</strong>
                                <span class="text-[#17130d]/55 dark:text-[#fffaf0]/55">{{ $fmt($version['done'] ?? 0) }} / {{ $fmt($version['total'] ?? 0) }}</span>
                            </div>
                            <div class="mt-3 h-2 overflow-hidden rounded-full bg-[#17130d]/10 dark:bg-white/10">
                                <div class="h-full rounded-full bg-gradient-to-r from-[#17130d] to-[#c98a2e]" style="width: {{ $percent($version['percent'] ?? 0) }}%"></div>
                            </div>
                            @if (($version['pending'] ?? 0) > 0 || ($version['blocked'] ?? 0) > 0)
                                <p class="mt-2 text-xs font-bold text-[#8a5a16] dark:text-[#f4c46b]">
                                    {{ $copy('متبقي', 'Remaining') }}: {{ $fmt(($version['pending'] ?? 0) + ($version['blocked'] ?? 0)) }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </article>

            <div class="space-y-4">
                <article class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                    <p class="text-xs font-bold text-[#c98a2e]">{{ $copy('آخر الحركة', 'Latest movement') }}</p>
                    <h2 class="mt-1 text-xl font-black text-[#17130d] dark:text-[#fffaf0]">{{ $copy('سجل التتبع', 'Tracker log') }}</h2>
                    <div class="mt-4 space-y-2">
                        @forelse ($history as $event)
                            <div class="rounded-2xl bg-[#fffaf0] px-4 py-3 text-sm dark:bg-white/5">
                                <strong class="text-[#17130d] dark:text-[#fffaf0]">{{ $event['version'] }} {{ $event['task_id'] }}</strong>
                                <span class="mx-2 text-[#c98a2e]">-</span>
                                <span class="text-[#17130d]/65 dark:text-[#fffaf0]/65">{{ $statusLabel($event['status'] ?? '') }}</span>
                            </div>
                        @empty
                            <p class="rounded-2xl bg-[#fffaf0] px-4 py-3 text-sm text-[#17130d]/65 dark:bg-white/5 dark:text-[#fffaf0]/65">
                                {{ $copy('لا يوجد سجل حديث.', 'No recent log entries.') }}
                            </p>
                        @endforelse
                    </div>
                </article>

                <article class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                    <p class="text-xs font-bold text-[#c98a2e]">{{ $copy('بوابة النشر', 'Publish gate') }}</p>
                    <h2 class="mt-1 text-xl font-black text-[#17130d] dark:text-[#fffaf0]">{{ $copy('قائمة التأكيد', 'Verification checklist') }}</h2>
                    <div class="mt-4 space-y-2">
                        @foreach (array_slice($checklist, 0, 5) as $item)
                            <div class="flex items-center justify-between gap-3 rounded-2xl bg-[#fffaf0] px-4 py-3 text-sm dark:bg-white/5">
                                <span class="text-[#17130d]/70 dark:text-[#fffaf0]/70">{{ $checklistTitle($item['title'] ?? '') }}</span>
                                <strong class="{{ ($item['status'] ?? null) === 'ready' ? 'text-[#17130d] dark:text-[#fffaf0]' : 'text-[#8a5a16] dark:text-[#f4c46b]' }}">{{ $statusLabel($item['status'] ?? '') }}</strong>
                            </div>
                        @endforeach
                    </div>
                </article>
            </div>
        </section>

        <section class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-bold text-[#c98a2e]">{{ $copy('قاعدة البيانات', 'Database') }}</p>
                    <h2 class="mt-1 text-xl font-black text-[#17130d] dark:text-[#fffaf0]">{{ $copy('أهم مجموعات الجداول', 'Key table groups') }}</h2>
                </div>
                <a class="rounded-full bg-[#fffaf0] px-4 py-2 text-xs font-bold text-[#17130d] ring-1 ring-[#17130d]/10 transition hover:bg-white dark:bg-white/10 dark:text-[#fffaf0] dark:ring-white/10" href="{{ route('filament.admin.pages.database-status') }}">
                    {{ $copy('التفاصيل', 'Details') }}
                </a>
            </div>

            <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($databaseGroups as $index => $group)
                    <article class="rounded-2xl bg-[#fffaf0] p-4 dark:bg-white/5">
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="text-sm font-black text-[#17130d] dark:text-[#fffaf0]">{{ $copy('مجموعة بيانات', 'Data group') }} {{ $index + 1 }}</h3>
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-[#17130d] ring-1 ring-[#17130d]/10 dark:bg-[#17130d] dark:text-[#fffaf0] dark:ring-white/10">{{ $fmt($group['total'] ?? 0) }}</span>
                        </div>
                        <div class="mt-3 space-y-2">
                            @foreach (array_slice($group['items'] ?? [], 0, 5) as $table)
                                <div class="flex items-center justify-between gap-2 text-xs">
                                    <span class="text-[#17130d]/60 dark:text-[#fffaf0]/60">{{ $table['table'] }}</span>
                                    <strong class="text-[#17130d] dark:text-[#fffaf0]">{{ $fmt($table['count'] ?? 0) }}</strong>
                                </div>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </div>
</x-filament-panels::page>
