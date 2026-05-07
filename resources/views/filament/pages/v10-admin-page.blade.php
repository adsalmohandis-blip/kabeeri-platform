@php
    $page = $data['page'];
    $pageConfig = $data['page_config'];
    $fmt = fn ($value) => number_format((int) $value);
    $statusClass = fn ($ready) => $ready ? 'bg-[#fffaf0] text-[#17130d] ring-[#c98a2e]/30' : 'bg-[#fffaf0] text-[#c98a2e] ring-[#c98a2e]/30';
@endphp

<x-filament-panels::page>
    <div class="space-y-6">
        <section class="overflow-hidden rounded-3xl bg-gradient-to-br from-[#17130d] via-[#17130d] to-[#c98a2e] p-6 text-white shadow-xl">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <p class="text-sm font-bold uppercase tracking-[0.22em] text-white/70">KABEERI V10 Admin Command</p>
                    <h1 class="mt-3 text-3xl font-black tracking-tight sm:text-5xl">{{ $pageConfig['label'] ?? 'Admin Command' }}</h1>
                    <p class="mt-4 text-base leading-8 text-white/78">{{ $pageConfig['purpose'] ?? 'Internal admin workspace for system clarity.' }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-white/60">Release signal</p>
                    <p class="mt-2 text-2xl font-black">{{ $data['release']['ready'] ? 'Ready' : 'Needs review' }}</p>
                    <p class="mt-1 text-sm text-white/70">{{ $data['system']['status'] }}</p>
                </div>
            </div>
        </section>

        <section class="grid gap-3 md:grid-cols-3 xl:grid-cols-6">
            @foreach ($data['config']['pages'] as $key => $adminPage)
                <a
                    class="rounded-2xl border border-[#17130d]/10 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-white/10 dark:bg-[#17130d]"
                    href="{{ route($adminPage['route']) }}"
                >
                    <span class="text-xs font-bold uppercase text-[#17130d]/60">{{ implode(', ', $adminPage['task_ids']) }}</span>
                    <strong class="mt-2 block text-sm text-[#17130d] dark:text-white">{{ $adminPage['label'] }}</strong>
                </a>
            @endforeach
        </section>

        @if ($page === 'system_check')
            <section class="grid gap-4 lg:grid-cols-3">
                @foreach ($data['system']['checks'] as $check)
                    <article class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1 {{ $statusClass($check['ok']) }}">
                            {{ $check['ok'] ? 'Ready' : 'Review' }}
                        </span>
                        <h2 class="mt-4 text-lg font-black text-[#17130d] dark:text-white">{{ $check['label'] }}</h2>
                        <p class="mt-2 text-sm text-[#17130d]/60">{{ $check['value'] }}</p>
                        <p class="mt-3 text-sm leading-6 text-[#17130d]/70 dark:text-[#fffaf0]/70">{{ $check['note'] }}</p>
                    </article>
                @endforeach
            </section>

            <section class="grid gap-4 md:grid-cols-5">
                @foreach ($data['system']['inventory'] as $item)
                    <article class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                        <p class="text-xs font-bold uppercase text-[#17130d]/60">{{ $item['label'] }}</p>
                        <p class="mt-2 text-3xl font-black text-[#17130d] dark:text-white">{{ $fmt($item['value']) }}</p>
                    </article>
                @endforeach
            </section>
        @endif

        @if ($page === 'task_tracker')
            <section class="grid gap-4 md:grid-cols-4">
                @foreach (['total' => 'Total', 'done' => 'Done', 'pending' => 'Pending', 'in_progress' => 'In progress'] as $key => $label)
                    <article class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                        <p class="text-xs font-bold uppercase text-[#17130d]/60">{{ $label }}</p>
                        <p class="mt-2 text-3xl font-black text-[#17130d] dark:text-white">{{ $fmt($data['tasks']['summary'][$key] ?? 0) }}</p>
                    </article>
                @endforeach
            </section>

            <section class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                <h2 class="text-xl font-black text-[#17130d] dark:text-white">Version Truth</h2>
                <div class="mt-4 space-y-3">
                    @foreach ($data['tasks']['versions'] as $version)
                        <div class="grid gap-3 rounded-2xl bg-[#fffaf0]/80 p-4 dark:bg-white/5 md:grid-cols-[110px_1fr_90px_120px] md:items-center">
                            <strong>{{ $version['name'] }}</strong>
                            <div class="h-2 overflow-hidden rounded-full bg-[#17130d]/15 dark:bg-white/10">
                                <div class="h-full rounded-full bg-gradient-to-r from-[#17130d] to-[#c98a2e]" style="width: {{ $version['percent'] }}%"></div>
                            </div>
                            <span class="font-bold">{{ $version['percent'] }}%</span>
                            <span class="text-sm text-[#17130d]/60">{{ $version['pending'] }} pending</span>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                <h2 class="text-xl font-black text-[#17130d] dark:text-white">Latest Tracker Events</h2>
                <div class="mt-4 space-y-3">
                    @foreach ($data['tasks']['latest_history'] as $event)
                        <div class="rounded-2xl bg-[#fffaf0]/80 p-4 dark:bg-white/5">
                            <strong>{{ $event['version'] }} {{ $event['task_id'] }} · {{ $event['status'] }}</strong>
                            <p class="mt-1 text-sm text-[#17130d]/60">{{ $event['notes'] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($page === 'database_status')
            <section class="grid gap-4 md:grid-cols-4">
                <article class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                    <p class="text-xs font-bold uppercase text-[#17130d]/60">Connection</p>
                    <p class="mt-2 text-2xl font-black">{{ $data['database']['connection'] }}</p>
                </article>
                <article class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                    <p class="text-xs font-bold uppercase text-[#17130d]/60">Migration files</p>
                    <p class="mt-2 text-2xl font-black">{{ $fmt($data['database']['migration_files']) }}</p>
                </article>
                <article class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                    <p class="text-xs font-bold uppercase text-[#17130d]/60">Applied</p>
                    <p class="mt-2 text-2xl font-black">{{ $fmt($data['database']['applied_migrations']) }}</p>
                </article>
                <article class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                    <p class="text-xs font-bold uppercase text-[#17130d]/60">Estimate pending</p>
                    <p class="mt-2 text-2xl font-black">{{ $fmt($data['database']['pending_migration_estimate']) }}</p>
                </article>
            </section>

            <section class="grid gap-4 lg:grid-cols-2">
                @foreach ($data['database']['groups'] as $group)
                    <article class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                        <h2 class="text-lg font-black">{{ $group['label'] }}</h2>
                        <div class="mt-4 grid gap-2 sm:grid-cols-2">
                            @foreach ($group['tables'] as $table)
                                <div class="flex items-center justify-between rounded-2xl bg-[#fffaf0]/80 px-3 py-2 text-sm dark:bg-white/5">
                                    <span>{{ $table['name'] }}</span>
                                    <strong>{{ $table['exists'] ? $fmt($table['count']) : 'missing' }}</strong>
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
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1 {{ $statusClass($module['health'] === 'ready') }}">
                                    {{ $module['health'] }}
                                </span>
                                <h2 class="mt-3 text-xl font-black">{{ $module['label'] }}</h2>
                            </div>
                            <p class="text-sm text-[#17130d]/60">{{ $module['existing_tables'] }} / {{ $module['table_count'] }} tables</p>
                        </div>
                        <p class="mt-3 text-sm leading-6 text-[#17130d]/70 dark:text-[#fffaf0]/70">{{ $module['next_action'] }}</p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <span class="rounded-full bg-[#fffaf0] px-3 py-1 text-xs font-bold dark:bg-white/10">{{ $fmt($module['records']) }} records</span>
                            @foreach ($module['routes'] as $routeItem)
                                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $routeItem['exists'] ? 'bg-[#fffaf0] text-[#17130d]' : 'bg-[#fffaf0] text-[#c98a2e]' }}">{{ $routeItem['name'] }}</span>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </section>
        @endif

        @if ($page === 'release_readiness')
            <section class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                <h2 class="text-xl font-black text-[#17130d] dark:text-white">Go / No-Go Gates</h2>
                <div class="mt-4 grid gap-3 lg:grid-cols-2">
                    @foreach ($data['release']['gates'] as $gate)
                        <div class="rounded-2xl bg-[#fffaf0]/80 p-4 dark:bg-white/5">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1 {{ $statusClass($gate['ready']) }}">{{ $gate['ready'] ? 'Ready' : 'Review' }}</span>
                            <h3 class="mt-3 font-black">{{ $gate['label'] }}</h3>
                            <p class="mt-1 text-sm text-[#17130d]/60">{{ $gate['source'] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                <h2 class="text-xl font-black">Required Commands</h2>
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
                                <p class="text-xs font-bold uppercase text-[#17130d]/60">{{ $workspace['context'] }}</p>
                                <h2 class="mt-2 text-xl font-black">{{ $workspace['label'] }}</h2>
                            </div>
                            @if ($workspace['route_exists'])
                                <a class="rounded-full bg-[#17130d] px-4 py-2 text-xs font-bold text-white" href="{{ route($workspace['primary_route']) }}">Open</a>
                            @endif
                        </div>
                        <p class="mt-3 text-sm leading-6 text-[#17130d]/70 dark:text-[#fffaf0]/70">{{ $workspace['empty_state'] }}</p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($workspace['primary_jobs'] as $job)
                                <span class="rounded-full bg-[#fffaf0] px-3 py-1 text-xs font-bold dark:bg-white/10">{{ $job }}</span>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </section>
        @endif

        <section class="grid gap-4 lg:grid-cols-2">
            <article class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                <h2 class="text-xl font-black">Quick Actions</h2>
                <div class="mt-4 space-y-2">
                    @foreach ($data['quick_actions'] as $action)
                        <div class="flex items-center justify-between gap-3 rounded-2xl bg-[#fffaf0]/80 p-3 dark:bg-white/5">
                            <div>
                                <strong>{{ $action['label'] }}</strong>
                                @if ($action['blocked'])
                                    <p class="text-xs text-[#c98a2e]">Requires {{ $action['permission'] }}</p>
                                @endif
                            </div>
                            @if ($action['route_exists'])
                                <a class="rounded-full bg-white px-3 py-1 text-xs font-bold ring-1 ring-[#17130d]/10 dark:bg-[#17130d] dark:ring-white/10" href="{{ route($action['route']) }}">Open</a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="rounded-3xl border border-[#17130d]/10 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#17130d]">
                <h2 class="text-xl font-black">Permission-Aware Navigation</h2>
                <p class="mt-2 text-sm leading-6 text-[#17130d]/70 dark:text-[#fffaf0]/70">{{ $data['permission_navigation']['principle'] }}</p>
                <div class="mt-4 space-y-2">
                    @foreach ($data['permission_navigation']['blocked_actions'] as $blocked)
                        <div class="rounded-2xl bg-[#fffaf0] p-3 text-sm text-[#17130d] ring-1 ring-[#c98a2e]/20">
                            <strong>{{ $blocked['action'] }}</strong>
                            <p class="mt-1">{{ $blocked['permission'] }} · {{ $blocked['reason'] }}</p>
                        </div>
                    @endforeach
                </div>
            </article>
        </section>
    </div>
</x-filament-panels::page>
