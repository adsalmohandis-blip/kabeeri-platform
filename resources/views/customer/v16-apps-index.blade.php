@php
    $activeNav = 'apps';
@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Localization\KabeeriLocale::direction() }}" data-kbr-theme="{{ $kabeeriUi['theme'] ?? 'light' }}" data-kbr-font="{{ $kabeeriUi['font'] ?? 'ibm-plex-sans-arabic' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('kabeeri.ui.apps_manage') }} | {{ __('kabeeri.brand.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f1eadc] text-[#111111] antialiased">
    <div class="kbr-customer-shell grid min-h-screen w-full lg:grid-cols-[16rem_minmax(0,1fr)]">
        @include('customer.partials.dashboard-sidebar', ['dashboard' => $dashboard, 'activeNav' => $activeNav])

        <main class="min-w-0 px-4 py-4 sm:px-6 lg:px-8 lg:py-6">
            @include('customer.partials.dashboard-header', ['title' => __('kabeeri.ui.apps_manage')])

            <section class="rounded-[1.7rem] border border-[#111111]/10 bg-[#f1eadc]/92 p-4 shadow-[0_18px_55px_rgba(17,17,17,.08)]">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <span class="inline-flex items-center rounded-full bg-[#111111]/10 px-3 py-1 text-[11px] font-black text-[#111111]"><x-kabeeri-icon name="apps" />{{ __('kabeeri.ui.apps') }}</span>
                        <h2 class="mt-2 text-base font-black">{{ __('kabeeri.ui.apps_manage') }}</h2>
                    </div>
                    <a href="{{ route('customer.apps.create') }}" class="inline-flex items-center justify-center rounded-full bg-[#111111] px-4 py-2 text-xs font-black text-[#f1eadc]"><x-kabeeri-icon name="plus" />{{ __('kabeeri.ui.new_app') }}</a>
                </div>

                <div class="divide-y divide-[#111111]/10 overflow-hidden rounded-3xl border border-[#111111]/10 bg-white/70">
                    @forelse ($sites as $site)
                        @php($appType = $site->metadata['v16_app_type'] ?? $site->site_type)
                        <article class="grid gap-3 px-4 py-4 md:grid-cols-[minmax(0,1fr)_auto] md:items-center">
                            <div class="min-w-0">
                                <strong class="block truncate text-sm font-black">{{ $site->name }}</strong>
                                <span class="mt-1 block text-xs font-bold text-[#111111]/60">{{ __('kabeeri.ui.username') }}: {{ $site->username }} / {{ __('kabeeri.customer.app_types.'.$appType.'.label') }}</span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <a class="rounded-full bg-[#111111] px-3 py-2 text-xs font-black text-[#f1eadc]" href="{{ route('customer.apps.show', ['username' => $site->username]) }}">{{ __('kabeeri.ui.app_detail') }}</a>
                                <a class="rounded-full bg-white px-3 py-2 text-xs font-black text-[#111111] ring-1 ring-[#111111]/10" href="{{ route('customer.apps.edit', ['username' => $site->username]) }}">{{ __('kabeeri.ui.edit_app') }}</a>
                                <a class="rounded-full bg-white px-3 py-2 text-xs font-black text-[#111111] ring-1 ring-[#111111]/10" href="{{ route('customer.apps.themes', ['username' => $site->username]) }}">{{ __('kabeeri.ui.switch_theme') }}</a>
                                <a class="rounded-full bg-white px-3 py-2 text-xs font-black text-[#111111] ring-1 ring-[#111111]/10" href="{{ route('customer.apps.plugins', ['username' => $site->username]) }}">{{ __('kabeeri.ui.plugins') }}</a>
                            </div>
                        </article>
                    @empty
                        <div class="px-5 py-8 text-center">
                            <p class="text-sm font-black">{{ __('kabeeri.ui.no_app') }}</p>
                            <a href="{{ route('customer.apps.create') }}" class="mt-4 inline-flex rounded-full bg-[#111111] px-4 py-2 text-xs font-black text-[#f1eadc]">{{ __('kabeeri.ui.create_app') }}</a>
                        </div>
                    @endforelse
                </div>
            </section>
        </main>
    </div>
</body>
</html>
