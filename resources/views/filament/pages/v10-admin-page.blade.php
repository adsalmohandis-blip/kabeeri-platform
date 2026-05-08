@php
    use App\Support\Ui\AdminLocaleCopy;

    $page = $data['page'];
    $pageConfig = $data['page_config'];
    $fmt = fn ($value): string => number_format((int) $value);
    $label = fn ($value, string $fallback = 'تفاصيل داخلية'): string => AdminLocaleCopy::visible((string) $value, $fallback);
    $statusClass = fn ($ready): string => $ready
        ? 'bg-[#17130d] text-[#f3e5ab] dark:bg-[#f3e5ab] dark:text-[#17130d]'
        : 'bg-[#f3e5ab] text-[#8a5a16] ring-1 ring-[#c98a2e]/30 dark:bg-white/10 dark:text-[#f4c46b]';
@endphp

<x-filament-panels::page>
    <div class="kabeeri-admin-surface space-y-6" data-admin-icon-scale="compact" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <section class="overflow-hidden rounded-3xl bg-[#17130d] p-6 text-[#f3e5ab] shadow-sm">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <p class="text-xs font-bold tracking-[0.16em] text-[#c98a2e]">{{ AdminLocaleCopy::currentBrand() }} {{ AdminLocaleCopy::label('V10 Admin Command') }}</p>
                    <h1 class="mt-3 text-2xl font-black tracking-tight sm:text-4xl">{{ $label($pageConfig['label'] ?? 'Admin Command', AdminLocaleCopy::label('V10 Admin Command')) }}</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-7 text-[#f3e5ab]/72">{{ $label($pageConfig['purpose'] ?? '') }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                    <p class="text-xs font-bold text-[#c98a2e]">{{ AdminLocaleCopy::label('Release readiness') }}</p>
                    <p class="mt-2 text-2xl font-black">{{ ($data['release']['ready'] ?? false) ? AdminLocaleCopy::label('Ready') : AdminLocaleCopy::label('Review') }}</p>
                    <p class="mt-1 text-sm text-[#f3e5ab]/65">{{ AdminLocaleCopy::label($data['system']['status'] ?? 'needs_attention') }}</p>
                </div>
            </div>
        </section>

        <section class="grid gap-3 md:grid-cols-3 xl:grid-cols-6">
            @foreach ($data['config']['pages'] as $adminPage)
                <a class="rounded-2xl border border-[#17130d]/10 bg-white p-4 text-[#17130d] shadow-sm transition hover:border-[#c98a2e]/50 dark:border-white/10 dark:bg-[#17130d] dark:text-[#f3e5ab]" href="{{ route($adminPage['route']) }}">
                    <span class="text-xs font-bold text-[#17130d]/50 dark:text-[#f3e5ab]/50">{{ implode(', ', $adminPage['task_ids']) }}</span>
                    <strong class="mt-2 block text-sm">{{ $label($adminPage['label']) }}</strong>
                </a>
            @endforeach
        </section>

        @if ($page === 'system_check')
            <section class="grid gap-4 lg:grid-cols-3">
                @foreach ($data['system']['checks'] as $check)
                    <article class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $statusClass($check['ok']) }}">{{ $check['ok'] ? AdminLocaleCopy::label('Ready') : AdminLocaleCopy::label('Review') }}</span>
                        <h2 class="mt-4 text-lg font-black text-[#17130d] dark:text-[#f3e5ab]">{{ $label($check['label']) }}</h2>
                        <p class="mt-2 text-sm text-[#17130d]/60 dark:text-[#f3e5ab]/60">{{ $label($check['value'], AdminLocaleCopy::label('Ready')) }}</p>
                        <p class="mt-3 text-sm leading-6 text-[#17130d]/70 dark:text-[#f3e5ab]/70">{{ $label($check['note']) }}</p>
                    </article>
                @endforeach
            </section>

            <section class="grid gap-4 md:grid-cols-5">
                @foreach ($data['system']['inventory'] as $item)
                    <article class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                        <p class="text-xs font-bold text-[#17130d]/60 dark:text-[#f3e5ab]/60">{{ $label($item['label']) }}</p>
                        <p class="mt-2 text-3xl font-black text-[#17130d] dark:text-[#f3e5ab]">{{ $fmt($item['value']) }}</p>
                    </article>
                @endforeach
            </section>
        @endif

        @if ($page === 'task_tracker')
            <section class="grid gap-4 md:grid-cols-4">
                @foreach (['total' => 'Total', 'done' => 'Done', 'pending' => 'Pending', 'in_progress' => 'In progress'] as $key => $copy)
                    <article class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                        <p class="text-xs font-bold text-[#17130d]/60 dark:text-[#f3e5ab]/60">{{ AdminLocaleCopy::label($copy) }}</p>
                        <p class="mt-2 text-3xl font-black text-[#17130d] dark:text-[#f3e5ab]">{{ $fmt($data['tasks']['summary'][$key] ?? 0) }}</p>
                    </article>
                @endforeach
            </section>

            <section class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                <h2 class="text-xl font-black text-[#17130d] dark:text-[#f3e5ab]">{{ AdminLocaleCopy::label('Version Truth') }}</h2>
                <div class="mt-4 space-y-3">
                    @foreach ($data['tasks']['versions'] as $version)
                        <div class="grid gap-3 rounded-2xl bg-[#f3e5ab] p-4 dark:bg-white/5 md:grid-cols-[110px_1fr_90px_120px] md:items-center">
                            <strong>{{ $version['name'] }}</strong>
                            <div class="h-2 overflow-hidden rounded-full bg-[#17130d]/15 dark:bg-white/10">
                                <div class="h-full rounded-full bg-gradient-to-r from-[#17130d] to-[#c98a2e]" style="width: {{ $version['percent'] }}%"></div>
                            </div>
                            <span class="font-bold">{{ $version['percent'] }}%</span>
                            <span class="text-sm text-[#17130d]/60 dark:text-[#f3e5ab]/60">{{ $version['pending'] }} {{ AdminLocaleCopy::label('pending') }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                <h2 class="text-xl font-black text-[#17130d] dark:text-[#f3e5ab]">{{ AdminLocaleCopy::label('Latest Tracker Events') }}</h2>
                <div class="mt-4 space-y-3">
                    @foreach ($data['tasks']['latest_history'] as $event)
                        <div class="rounded-2xl bg-[#f3e5ab] p-4 dark:bg-white/5">
                            <strong>{{ $event['version'] }} {{ $event['task_id'] }} - {{ AdminLocaleCopy::label($event['status']) }}</strong>
                            <p class="mt-1 text-sm text-[#17130d]/60 dark:text-[#f3e5ab]/60">{{ $event['notes'] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($page === 'database_status')
            <section class="grid gap-4 md:grid-cols-4">
                @foreach ([
                    'connection' => ['Connection', $data['database']['connection']],
                    'migration_files' => ['Migration files', $fmt($data['database']['migration_files'])],
                    'applied_migrations' => ['Applied', $fmt($data['database']['applied_migrations'])],
                    'pending_migration_estimate' => ['Estimate pending', $fmt($data['database']['pending_migration_estimate'])],
                ] as [$copy, $value])
                    <article class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                        <p class="text-xs font-bold text-[#17130d]/60 dark:text-[#f3e5ab]/60">{{ AdminLocaleCopy::label($copy) }}</p>
                        <p class="mt-2 text-2xl font-black text-[#17130d] dark:text-[#f3e5ab]">{{ $value }}</p>
                    </article>
                @endforeach
            </section>

            <section class="grid gap-4 lg:grid-cols-2">
                @foreach ($data['database']['groups'] as $group)
                    <article class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                        <h2 class="text-lg font-black text-[#17130d] dark:text-[#f3e5ab]">{{ $label($group['label']) }}</h2>
                        <div class="mt-4 grid gap-2 sm:grid-cols-2">
                            @foreach ($group['tables'] as $table)
                                <div class="flex items-center justify-between rounded-2xl bg-[#f3e5ab] px-3 py-2 text-sm dark:bg-white/5">
                                    <span>{{ $table['name'] }}</span>
                                    <strong>{{ $table['exists'] ? $fmt($table['count']) : AdminLocaleCopy::label('missing') }}</strong>
                                </div>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </section>
        @endif

        @if ($page === 'module_health')
            <section class="grid gap-4 lg:grid-cols-2">
                @foreach ($data['modules'] as $module)
                    <article class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $statusClass($module['health'] === 'ready') }}">{{ AdminLocaleCopy::label($module['health']) }}</span>
                                <h2 class="mt-3 text-xl font-black text-[#17130d] dark:text-[#f3e5ab]">{{ $label($module['label']) }}</h2>
                            </div>
                            <p class="text-sm text-[#17130d]/60 dark:text-[#f3e5ab]/60">{{ $module['existing_tables'] }} / {{ $module['table_count'] }} {{ AdminLocaleCopy::label('tables') }}</p>
                        </div>
                        <p class="mt-3 text-sm leading-6 text-[#17130d]/70 dark:text-[#f3e5ab]/70">{{ $label($module['next_action']) }}</p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <span class="rounded-full bg-[#f3e5ab] px-3 py-1 text-xs font-bold dark:bg-white/10">{{ $fmt($module['records']) }} {{ AdminLocaleCopy::label('records') }}</span>
                            @foreach ($module['routes'] as $routeItem)
                                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $routeItem['exists'] ? 'bg-[#f3e5ab] text-[#17130d]' : 'bg-[#f3e5ab] text-[#c98a2e]' }}">{{ $routeItem['name'] }}</span>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </section>
        @endif

        @if ($page === 'release_readiness')
            <section class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                <h2 class="text-xl font-black text-[#17130d] dark:text-[#f3e5ab]">{{ AdminLocaleCopy::label('Go / No-Go Gates') }}</h2>
                <div class="mt-4 grid gap-3 lg:grid-cols-2">
                    @foreach ($data['release']['gates'] as $gate)
                        <div class="rounded-2xl bg-[#f3e5ab] p-4 dark:bg-white/5">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $statusClass($gate['ready']) }}">{{ $gate['ready'] ? AdminLocaleCopy::label('Ready') : AdminLocaleCopy::label('Review') }}</span>
                            <h3 class="mt-3 font-black text-[#17130d] dark:text-[#f3e5ab]">{{ $label($gate['label']) }}</h3>
                            <p class="mt-1 text-sm text-[#17130d]/60 dark:text-[#f3e5ab]/60">{{ $gate['source'] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                <h2 class="text-xl font-black text-[#17130d] dark:text-[#f3e5ab]">{{ AdminLocaleCopy::label('Required Commands') }}</h2>
                <div class="mt-4 grid gap-2">
                    @foreach ($data['release']['commands'] as $command)
                        <code class="rounded-2xl bg-[#17130d] px-4 py-3 text-sm text-white">{{ $command }}</code>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($page === 'admin_workspaces')
            <section class="grid gap-4 lg:grid-cols-2">
                @foreach ($data['workspaces'] as $workspace)
                    <article class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-bold text-[#17130d]/60 dark:text-[#f3e5ab]/60">{{ $label($workspace['context']) }}</p>
                                <h2 class="mt-2 text-xl font-black text-[#17130d] dark:text-[#f3e5ab]">{{ $label($workspace['label']) }}</h2>
                            </div>
                            @if ($workspace['route_exists'])
                                <a class="rounded-full bg-[#17130d] px-4 py-2 text-xs font-bold text-white dark:bg-[#f3e5ab] dark:text-[#17130d]" href="{{ route($workspace['primary_route']) }}">{{ AdminLocaleCopy::label('Open') }}</a>
                            @endif
                        </div>
                        <p class="mt-3 text-sm leading-6 text-[#17130d]/70 dark:text-[#f3e5ab]/70">{{ $label($workspace['empty_state']) }}</p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($workspace['primary_jobs'] as $job)
                                <span class="rounded-full bg-[#f3e5ab] px-3 py-1 text-xs font-bold dark:bg-white/10">{{ $label($job) }}</span>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </section>
        @endif

        <section class="grid gap-4 lg:grid-cols-2">
            <article class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                <h2 class="text-xl font-black text-[#17130d] dark:text-[#f3e5ab]">{{ AdminLocaleCopy::label('Quick Actions') }}</h2>
                <div class="mt-4 space-y-2">
                    @foreach ($data['quick_actions'] as $action)
                        <div class="flex items-center justify-between gap-3 rounded-2xl bg-[#f3e5ab] p-3 dark:bg-white/5">
                            <div>
                                <strong class="text-[#17130d] dark:text-[#f3e5ab]">{{ $label($action['label']) }}</strong>
                                @if ($action['blocked'])
                                    <p class="text-xs text-[#c98a2e]">{{ AdminLocaleCopy::label('Requires') }} {{ $action['permission'] }}</p>
                                @endif
                            </div>
                            @if ($action['route_exists'])
                                <a class="rounded-full bg-white px-3 py-1 text-xs font-bold ring-1 ring-[#17130d]/10 dark:bg-[#17130d] dark:ring-white/10" href="{{ route($action['route']) }}">{{ AdminLocaleCopy::label('Open') }}</a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                <h2 class="text-xl font-black text-[#17130d] dark:text-[#f3e5ab]">{{ AdminLocaleCopy::label('Permission-Aware Navigation') }}</h2>
                <p class="mt-2 text-sm leading-6 text-[#17130d]/70 dark:text-[#f3e5ab]/70">{{ $label($data['permission_navigation']['principle'] ?? '') }}</p>
                <div class="mt-4 space-y-2">
                    @foreach ($data['permission_navigation']['blocked_actions'] as $blocked)
                        <div class="rounded-2xl bg-[#f3e5ab] p-3 text-sm text-[#17130d] ring-1 ring-[#c98a2e]/20">
                            <strong>{{ $label($blocked['action']) }}</strong>
                            <p class="mt-1">{{ $blocked['permission'] }} - {{ $label($blocked['reason']) }}</p>
                        </div>
                    @endforeach
                </div>
            </article>
        </section>
    </div>
</x-filament-panels::page>
