@php
    $isArabic = app()->getLocale() === 'ar';
    $copy = fn (string $arabic, string $english): string => $isArabic ? $arabic : $english;
    $brand = __('kabeeri.brand.name');
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
            'icon' => 'M4 19V5 M4 19h16 M8 16v-4 M12 16V8 M16 16v-6',
        ],
        [
            'label' => $copy('الباك إند', 'Backend'),
            'value' => ($backend['percent'] ?? 0).'%',
            'note' => $fmt($backend['done'] ?? 0).' / '.$fmt($backend['total'] ?? 0),
            'ready' => ($backend['pending'] ?? 1) === 0 && ($backend['blocked'] ?? 1) === 0,
            'icon' => 'M5 5h14v14H5V5Z M8 9h8 M8 13h8 M8 17h5',
        ],
        [
            'label' => $copy('واجهات الاستخدام', 'User interfaces'),
            'value' => ($ui['percent'] ?? 0).'%',
            'note' => $fmt($ui['done'] ?? 0).' / '.$fmt($ui['total'] ?? 0),
            'ready' => ($ui['pending'] ?? 1) === 0 && ($ui['blocked'] ?? 1) === 0,
            'icon' => 'M4 5h16v10H4V5Z M9 19h6 M12 15v4',
        ],
        [
            'label' => $copy('جاهزية النشر', 'Release readiness'),
            'value' => $releaseReady ? $copy('جاهز', 'Ready') : $copy('مراجعة', 'Review'),
            'note' => $productionReady ? $copy('إنتاج بعد الاختبار', 'Production after staging') : $copy('يلزم تأكيد نهائي', 'Final verification needed'),
            'ready' => $releaseReady,
            'icon' => 'M5 15c2.5-6 6.5-10 14-10-1 7.5-4 11.5-10 14l-4-4Z M9 19c-1.5.8-3 1-5 1 .1-2 .3-3.5 1-5',
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
    <style>
        .kbr-dev-status {
            --kbr-dev-ink: #17130d;
            --kbr-dev-paper: #fffaf0;
            --kbr-dev-gold: #c98a2e;
            --kbr-dev-muted: rgba(23, 19, 13, .68);
            --kbr-dev-line: rgba(23, 19, 13, .12);
            --kbr-dev-card: rgba(255, 250, 240, .94);
            --kbr-dev-soft: rgba(255, 250, 240, .68);
            display: grid;
            gap: 1rem;
            width: 100%;
            color: var(--kbr-dev-ink);
            font-family: var(--kbr-admin-font-family, "IBM Plex Sans Arabic", "Almarai", sans-serif);
        }

        .kbr-dev-status *, .kbr-dev-status *::before, .kbr-dev-status *::after {
            box-sizing: border-box;
        }

        .kbr-dev-status svg {
            width: 1.05rem;
            height: 1.05rem;
            flex: none;
        }

        .kbr-dev-hero,
        .kbr-dev-card,
        .kbr-dev-stat,
        .kbr-dev-panel,
        .kbr-dev-data-card {
            border: 1px solid var(--kbr-dev-line);
            border-radius: 1.35rem;
            background: var(--kbr-dev-card);
            box-shadow: 0 18px 45px rgba(23, 19, 13, .08);
        }

        .kbr-dev-hero {
            overflow: hidden;
            color: var(--kbr-dev-paper);
            background:
                radial-gradient(circle at 12% 12%, rgba(201, 138, 46, .28), transparent 22rem),
                linear-gradient(135deg, #17130d, #211a11);
        }

        .kbr-dev-hero-grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: minmax(0, 1fr) minmax(16rem, 22rem);
            align-items: end;
            padding: clamp(1.25rem, 2.5vw, 2rem);
        }

        .kbr-dev-badge,
        .kbr-dev-pill {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            min-height: 1.75rem;
            width: fit-content;
            border-radius: 999px;
            padding: .35rem .7rem;
            font-size: .72rem;
            font-weight: 900;
        }

        .kbr-dev-badge {
            border: 1px solid rgba(201, 138, 46, .42);
            color: #f4c46b;
            background: rgba(201, 138, 46, .12);
        }

        .kbr-dev-title {
            margin: .85rem 0 0;
            max-width: 58rem;
            font-size: clamp(1.65rem, 3vw, 2.85rem);
            font-weight: 950;
            line-height: 1.05;
            letter-spacing: -.04em;
        }

        .kbr-dev-lead,
        .kbr-dev-muted {
            color: var(--kbr-dev-muted);
            line-height: 1.75;
        }

        .kbr-dev-hero .kbr-dev-lead,
        .kbr-dev-hero .kbr-dev-muted {
            color: rgba(255, 250, 240, .74);
        }

        .kbr-dev-release-card {
            border: 1px solid rgba(255, 250, 240, .13);
            border-radius: 1.1rem;
            background: rgba(255, 250, 240, .07);
            padding: 1rem;
        }

        .kbr-dev-release-card strong {
            display: block;
            margin-top: .45rem;
            font-size: 1.35rem;
            line-height: 1.2;
        }

        .kbr-dev-grid-4,
        .kbr-dev-grid-3,
        .kbr-dev-grid-2,
        .kbr-dev-main-grid,
        .kbr-dev-split-grid {
            display: grid;
            gap: .85rem;
        }

        .kbr-dev-grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .kbr-dev-grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .kbr-dev-grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .kbr-dev-main-grid { grid-template-columns: minmax(0, 1fr) minmax(18rem, 24rem); }
        .kbr-dev-split-grid { grid-template-columns: minmax(0, 1.12fr) minmax(20rem, .88fr); }

        .kbr-dev-stat,
        .kbr-dev-card,
        .kbr-dev-panel,
        .kbr-dev-data-card {
            padding: 1rem;
        }

        .kbr-dev-stat-head,
        .kbr-dev-section-head,
        .kbr-dev-row,
        .kbr-dev-mini-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
        }

        .kbr-dev-section-head {
            align-items: flex-start;
            margin-bottom: .9rem;
        }

        .kbr-dev-kicker {
            margin: 0;
            color: var(--kbr-dev-gold);
            font-size: .72rem;
            font-weight: 900;
        }

        .kbr-dev-heading {
            margin: .2rem 0 0;
            color: var(--kbr-dev-ink);
            font-size: 1.15rem;
            font-weight: 950;
            line-height: 1.25;
            letter-spacing: -.025em;
        }

        .kbr-dev-stat-value {
            margin: .35rem 0 0;
            color: var(--kbr-dev-ink);
            font-size: 1.6rem;
            font-weight: 950;
        }

        .kbr-dev-icon-box {
            display: inline-grid;
            place-items: center;
            width: 2.35rem;
            height: 2.35rem;
            border-radius: 999px;
            color: var(--kbr-dev-paper);
            background: var(--kbr-dev-ink);
        }

        .kbr-dev-icon-box.is-review {
            color: var(--kbr-dev-ink);
            background: var(--kbr-dev-paper);
            box-shadow: inset 0 0 0 1px rgba(201, 138, 46, .34);
        }

        .kbr-dev-shortcuts {
            display: grid;
            gap: .55rem;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .kbr-dev-shortcut,
        .kbr-dev-button {
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            gap: .7rem;
            min-height: 2.75rem;
            border: 1px solid var(--kbr-dev-line);
            border-radius: 1rem;
            background: var(--kbr-dev-paper);
            color: var(--kbr-dev-ink);
            padding: .7rem .85rem;
            font-size: .82rem;
            font-weight: 900;
            text-decoration: none;
            transition: transform .18s ease, border-color .18s ease, background .18s ease;
        }

        .kbr-dev-shortcut:hover,
        .kbr-dev-button:hover {
            transform: translateY(-1px);
            border-color: rgba(201, 138, 46, .55);
            background: #fff;
        }

        .kbr-dev-button {
            min-height: 2.35rem;
            background: var(--kbr-dev-ink);
            color: var(--kbr-dev-paper);
        }

        .kbr-dev-list {
            display: grid;
            gap: .55rem;
            margin-top: .85rem;
        }

        .kbr-dev-row,
        .kbr-dev-mini-row {
            min-height: 2.65rem;
            border-radius: 1rem;
            background: var(--kbr-dev-paper);
            padding: .65rem .85rem;
            font-size: .82rem;
        }

        .kbr-dev-row strong,
        .kbr-dev-mini-row strong {
            color: var(--kbr-dev-ink);
            font-weight: 950;
        }

        .kbr-dev-progress-card {
            border-radius: 1rem;
            background: var(--kbr-dev-paper);
            padding: .85rem;
        }

        .kbr-dev-progress-top {
            display: flex;
            justify-content: space-between;
            gap: .7rem;
            font-size: .82rem;
        }

        .kbr-dev-bar {
            overflow: hidden;
            height: .5rem;
            margin-top: .7rem;
            border-radius: 999px;
            background: rgba(23, 19, 13, .1);
        }

        .kbr-dev-bar span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #17130d, #c98a2e);
        }

        .kbr-dev-warn {
            margin: .55rem 0 0;
            color: #8a5a16;
            font-size: .75rem;
            font-weight: 900;
        }

        .kbr-dev-status-text {
            color: #8a5a16;
            font-weight: 950;
        }

        .kbr-dev-status-text.is-ready {
            color: var(--kbr-dev-ink);
        }

        .kbr-dev-table-name {
            overflow: hidden;
            color: var(--kbr-dev-muted);
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        html.fi[data-kbr-theme="dark"] .kbr-dev-status {
            --kbr-dev-ink: #fffaf0;
            --kbr-dev-paper: #211a11;
            --kbr-dev-muted: rgba(255, 250, 240, .68);
            --kbr-dev-line: rgba(255, 250, 240, .14);
            --kbr-dev-card: #211a11;
            color: var(--kbr-dev-ink);
        }

        html.fi[data-kbr-theme="dark"] .kbr-dev-card,
        html.fi[data-kbr-theme="dark"] .kbr-dev-stat,
        html.fi[data-kbr-theme="dark"] .kbr-dev-panel,
        html.fi[data-kbr-theme="dark"] .kbr-dev-data-card {
            box-shadow: none;
        }

        html.fi[data-kbr-theme="dark"] .kbr-dev-shortcut:hover,
        html.fi[data-kbr-theme="dark"] .kbr-dev-button:hover {
            background: rgba(255, 250, 240, .08);
        }

        html.fi[data-kbr-theme="dark"] .kbr-dev-icon-box {
            color: #17130d;
            background: #c98a2e;
        }

        html.fi[data-kbr-theme="dark"] .kbr-dev-icon-box.is-review,
        html.fi[data-kbr-theme="dark"] .kbr-dev-button {
            color: #17130d;
            background: #fffaf0;
        }

        html.fi[data-kbr-theme="dark"] .kbr-dev-status-text,
        html.fi[data-kbr-theme="dark"] .kbr-dev-warn {
            color: #f4c46b;
        }

        html.fi[data-kbr-theme="dark"] .kbr-dev-status-text.is-ready {
            color: #fffaf0;
        }

        @media (max-width: 1180px) {
            .kbr-dev-grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .kbr-dev-main-grid,
            .kbr-dev-split-grid,
            .kbr-dev-hero-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 760px) {
            .kbr-dev-grid-4,
            .kbr-dev-grid-3,
            .kbr-dev-grid-2,
            .kbr-dev-shortcuts { grid-template-columns: 1fr; }
            .kbr-dev-section-head,
            .kbr-dev-row,
            .kbr-dev-mini-row { align-items: flex-start; flex-direction: column; }
        }
    </style>

    <div class="kbr-dev-status kabeeri-admin-surface" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
        <section class="kbr-dev-hero">
            <div class="kbr-dev-hero-grid">
                <div>
                    <span class="kbr-dev-badge">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 13h4l2-6 4 12 2-6h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        {{ $brand }}
                    </span>
                    <h1 class="kbr-dev-title">{{ $copy('لوحة حالة التطوير', 'Development status') }}</h1>
                    <p class="kbr-dev-lead">
                        {{ $copy('ملخص داخلي منظم لحالة التنفيذ، البيانات، التتبع، وجاهزية النشر داخل لوحة الأدمن.', 'A focused internal view of implementation, data, tracking, and release readiness inside the admin dashboard.') }}
                    </p>
                </div>

                <aside class="kbr-dev-release-card">
                    <p class="kbr-dev-kicker">{{ $copy('إشارة الإصدار', 'Release signal') }}</p>
                    <strong>{{ $releaseReady ? $copy('جاهز للمراجعة', 'Ready for review') : $copy('يحتاج متابعة', 'Needs follow-up') }}</strong>
                    <p class="kbr-dev-muted">{{ $productionReady ? $copy('جاهز بعد اختبار بيئة التجربة.', 'Ready after staging verification.') : $copy('يلزم تأكيد المالك وبيئة التجربة قبل الإنتاج.', 'Owner and staging verification are still required.') }}</p>
                </aside>
            </div>
        </section>

        <section class="kbr-dev-grid-4" aria-label="{{ $copy('مؤشرات الحالة', 'Status metrics') }}">
            @foreach ($stats as $stat)
                <article class="kbr-dev-stat">
                    <div class="kbr-dev-stat-head">
                        <div>
                            <p class="kbr-dev-muted">{{ $stat['label'] }}</p>
                            <p class="kbr-dev-stat-value">{{ $stat['value'] }}</p>
                        </div>
                        <span class="kbr-dev-icon-box {{ $stat['ready'] ? '' : 'is-review' }}">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="{{ $stat['icon'] }}" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        </span>
                    </div>
                    <p class="kbr-dev-muted">{{ $stat['note'] }}</p>
                </article>
            @endforeach
        </section>

        <section class="kbr-dev-main-grid">
            <article class="kbr-dev-panel">
                <div class="kbr-dev-section-head">
                    <div>
                        <p class="kbr-dev-kicker">{{ $copy('المسارات الداخلية', 'Internal paths') }}</p>
                        <h2 class="kbr-dev-heading">{{ $copy('اختصارات لوحة الأدمن', 'Admin shortcuts') }}</h2>
                    </div>
                    <a class="kbr-dev-button" href="{{ route('filament.admin.pages.development-status') }}">{{ $copy('الصفحة الحالية', 'Current page') }}</a>
                </div>

                <div class="kbr-dev-shortcuts">
                    @foreach ($adminLinks as $link)
                        <a class="kbr-dev-shortcut" href="{{ route($link['route']) }}">
                            <span>{{ $link['label'] }}</span>
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="{{ $isArabic ? 'M15 6l-6 6 6 6' : 'M9 6l6 6-6 6' }}" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        </a>
                    @endforeach
                </div>
            </article>

            <aside class="kbr-dev-card">
                <p class="kbr-dev-kicker">{{ $copy('ملخص سريع', 'Quick summary') }}</p>
                <h2 class="kbr-dev-heading">{{ $copy('مخزون النظام', 'System inventory') }}</h2>
                <div class="kbr-dev-list">
                    @foreach (array_slice($inventory, 0, 5) as $item)
                        <div class="kbr-dev-row">
                            <span class="kbr-dev-muted">{{ $inventoryLabel($item['label'] ?? '') }}</span>
                            <strong>{{ $fmt($item['value'] ?? 0) }}</strong>
                        </div>
                    @endforeach
                </div>
            </aside>
        </section>

        <section class="kbr-dev-split-grid">
            <article class="kbr-dev-panel">
                <div class="kbr-dev-section-head">
                    <div>
                        <p class="kbr-dev-kicker">{{ $copy('التقدم الحقيقي', 'Real progress') }}</p>
                        <h2 class="kbr-dev-heading">{{ $copy('حالة النسخ', 'Version status') }}</h2>
                    </div>
                    <span class="kbr-dev-pill" style="background:#17130d;color:#fffaf0">{{ $summary['percent'] ?? 0 }}%</span>
                </div>

                <div class="kbr-dev-list">
                    @foreach ($versions as $version)
                        <div class="kbr-dev-progress-card">
                            <div class="kbr-dev-progress-top">
                                <strong>{{ $version['name'] }}</strong>
                                <span class="kbr-dev-muted">{{ $fmt($version['done'] ?? 0) }} / {{ $fmt($version['total'] ?? 0) }}</span>
                            </div>
                            <div class="kbr-dev-bar"><span style="width: {{ $percent($version['percent'] ?? 0) }}%"></span></div>
                            @if (($version['pending'] ?? 0) > 0 || ($version['blocked'] ?? 0) > 0)
                                <p class="kbr-dev-warn">{{ $copy('متبقي', 'Remaining') }}: {{ $fmt(($version['pending'] ?? 0) + ($version['blocked'] ?? 0)) }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </article>

            <div class="kbr-dev-list" style="margin-top:0">
                <article class="kbr-dev-card">
                    <p class="kbr-dev-kicker">{{ $copy('آخر الحركة', 'Latest movement') }}</p>
                    <h2 class="kbr-dev-heading">{{ $copy('سجل التتبع', 'Tracker log') }}</h2>
                    <div class="kbr-dev-list">
                        @forelse ($history as $event)
                            <div class="kbr-dev-row">
                                <strong>{{ $event['version'] }} {{ $event['task_id'] }}</strong>
                                <span class="kbr-dev-muted">{{ $statusLabel($event['status'] ?? '') }}</span>
                            </div>
                        @empty
                            <p class="kbr-dev-row kbr-dev-muted">{{ $copy('لا يوجد سجل حديث.', 'No recent log entries.') }}</p>
                        @endforelse
                    </div>
                </article>

                <article class="kbr-dev-card">
                    <p class="kbr-dev-kicker">{{ $copy('بوابة النشر', 'Publish gate') }}</p>
                    <h2 class="kbr-dev-heading">{{ $copy('قائمة التأكيد', 'Verification checklist') }}</h2>
                    <div class="kbr-dev-list">
                        @foreach (array_slice($checklist, 0, 5) as $item)
                            <div class="kbr-dev-row">
                                <span class="kbr-dev-muted">{{ $checklistTitle($item['title'] ?? '') }}</span>
                                <strong class="kbr-dev-status-text {{ ($item['status'] ?? null) === 'ready' ? 'is-ready' : '' }}">{{ $statusLabel($item['status'] ?? '') }}</strong>
                            </div>
                        @endforeach
                    </div>
                </article>
            </div>
        </section>

        <section class="kbr-dev-panel">
            <div class="kbr-dev-section-head">
                <div>
                    <p class="kbr-dev-kicker">{{ $copy('قاعدة البيانات', 'Database') }}</p>
                    <h2 class="kbr-dev-heading">{{ $copy('أهم مجموعات الجداول', 'Key table groups') }}</h2>
                </div>
                <a class="kbr-dev-shortcut" href="{{ route('filament.admin.pages.database-status') }}" style="min-height:2.35rem">{{ $copy('التفاصيل', 'Details') }}</a>
            </div>

            <div class="kbr-dev-grid-4">
                @foreach ($databaseGroups as $index => $group)
                    <article class="kbr-dev-data-card">
                        <div class="kbr-dev-row" style="background:transparent;padding:0;min-height:auto">
                            <h3 class="kbr-dev-heading">{{ $copy('مجموعة بيانات', 'Data group') }} {{ $index + 1 }}</h3>
                            <span class="kbr-dev-pill">{{ $fmt($group['total'] ?? 0) }}</span>
                        </div>
                        <div class="kbr-dev-list">
                            @foreach (array_slice($group['items'] ?? [], 0, 5) as $table)
                                <div class="kbr-dev-mini-row">
                                    <span class="kbr-dev-table-name">{{ $table['table'] }}</span>
                                    <strong>{{ $fmt($table['count'] ?? 0) }}</strong>
                                </div>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </div>
</x-filament-panels::page>
