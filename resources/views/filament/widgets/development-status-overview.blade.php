@php
    use App\Support\Ui\AdminLocaleCopy;

    $user = filament()->auth()->user();
    $summary = $dashboard['task_summary'] ?? [];
    $release = $dashboard['release_status'] ?? [];
    $releaseReady = (bool) ($release['staging_ready'] ?? false);
    $fmt = fn ($value): string => number_format((int) $value);
@endphp

<x-filament-widgets::widget>
    <section
        class="kabeeri-admin-widget overflow-hidden rounded-3xl border border-[#000000]/10 bg-[#fafafa] shadow-sm dark:border-[#fafafa]/10 dark:bg-[#000000]"
        data-admin-icon-scale="compact"
        dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
    >
        <div class="grid gap-5 p-5 lg:grid-cols-[1fr_auto] lg:items-center">
            <div class="flex items-start gap-4">
                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-[#000000] text-[#f0f0f0] dark:bg-[#f0f0f0] dark:text-[#000000]">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 13h4l2-6 4 12 2-6h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
                <div>
                    <p class="text-xs font-bold text-[#000000]">{{ AdminLocaleCopy::currentBrand() }}</p>
                    <h2 class="mt-1 text-xl font-black text-[#000000] dark:text-[#f0f0f0]">{{ AdminLocaleCopy::label('Development status') }}</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-[#000000]/65 dark:text-[#f0f0f0]/65">
                        {{ AdminLocaleCopy::label('Compact internal view') }}
                    </p>
                    @if ($user)
                        <p class="mt-2 text-xs font-bold text-[#000000]/50 dark:text-[#f0f0f0]/50">
                            {{ AdminLocaleCopy::label('Current admin') }}: {{ filament()->getUserName($user) }}
                        </p>
                    @endif
                </div>
            </div>

            <div class="grid gap-2 sm:grid-cols-3 lg:min-w-[420px]">
                <div class="rounded-2xl bg-[#f0f0f0] px-4 py-3 dark:bg-[#fafafa]/5">
                    <p class="text-xs font-bold text-[#000000]/55 dark:text-[#f0f0f0]/55">{{ AdminLocaleCopy::label('Progress') }}</p>
                    <p class="mt-1 text-xl font-black text-[#000000] dark:text-[#f0f0f0]">{{ $summary['percent'] ?? 0 }}%</p>
                </div>
                <div class="rounded-2xl bg-[#f0f0f0] px-4 py-3 dark:bg-[#fafafa]/5">
                    <p class="text-xs font-bold text-[#000000]/55 dark:text-[#f0f0f0]/55">{{ AdminLocaleCopy::label('Done') }}</p>
                    <p class="mt-1 text-xl font-black text-[#000000] dark:text-[#f0f0f0]">{{ $fmt($summary['done'] ?? 0) }}</p>
                </div>
                <div class="rounded-2xl bg-[#f0f0f0] px-4 py-3 dark:bg-[#fafafa]/5">
                    <p class="text-xs font-bold text-[#000000]/55 dark:text-[#f0f0f0]/55">{{ AdminLocaleCopy::label('Release') }}</p>
                    <p class="mt-1 text-xl font-black {{ $releaseReady ? 'text-[#000000] dark:text-[#f0f0f0]' : 'text-[#000000] dark:text-[#f0f0f0]' }}">
                        {{ $releaseReady ? AdminLocaleCopy::label('Ready') : AdminLocaleCopy::label('Review') }}
                    </p>
                </div>
            </div>

            <div class="lg:col-span-2">
                <a class="inline-flex items-center gap-2 rounded-full bg-[#000000] px-4 py-2 text-xs font-bold text-[#f0f0f0] transition hover:bg-[#000000] dark:bg-[#f0f0f0] dark:text-[#000000]" href="{{ route('filament.admin.pages.development-status') }}">
                    <span>{{ AdminLocaleCopy::label('Open development status') }}</span>
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </a>
            </div>
        </div>
    </section>
</x-filament-widgets::widget>
