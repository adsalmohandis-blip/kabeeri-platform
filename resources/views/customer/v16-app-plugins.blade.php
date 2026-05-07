@php
    $activeNav = 'plugins';
    $installedBySlug = $site->installedPackages->filter(fn ($installed) => filled($installed->package))->keyBy(fn ($installed) => $installed->package->slug);
@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Localization\KabeeriLocale::direction() }}" data-kbr-theme="{{ $kabeeriUi['theme'] ?? 'light' }}" data-kbr-font="{{ $kabeeriUi['font'] ?? 'ibm-plex-sans-arabic' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('kabeeri.ui.plugins') }} | {{ __('kabeeri.brand.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#fffaf0] text-[#17130d] antialiased">
    <div class="kbr-customer-shell mx-auto grid min-h-screen w-full max-w-[1440px] lg:grid-cols-[16rem_minmax(0,1fr)]">
        @include('customer.partials.dashboard-sidebar', ['dashboard' => $dashboard, 'activeNav' => $activeNav])

        <main class="min-w-0 px-4 py-4 sm:px-6 lg:px-8 lg:py-6">
            @include('customer.partials.dashboard-header', ['title' => __('kabeeri.ui.plugins'), 'subtitle' => $site->name])

            <section class="rounded-[1.7rem] border border-[#17130d]/10 bg-[#fffaf0]/92 p-4 shadow-[0_18px_55px_rgba(23,19,13,.08)]">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <span class="inline-flex items-center rounded-full bg-[#17130d]/10 px-3 py-1 text-[11px] font-black text-[#17130d]"><x-kabeeri-icon name="plugin" />{{ __('kabeeri.ui.plugin_catalog') }}</span>
                        <h2 class="mt-2 text-base font-black">{{ __('kabeeri.ui.plugins') }}</h2>
                    </div>
                    <a href="{{ route('customer.apps.themes', ['username' => $site->username]) }}" class="inline-flex items-center justify-center rounded-full bg-white px-4 py-2 text-xs font-black text-[#17130d] ring-1 ring-[#17130d]/10"><x-kabeeri-icon name="theme" />{{ __('kabeeri.ui.switch_theme') }}</a>
                </div>

                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($plugins as $plugin)
                        @php
                            $installed = $installedBySlug->get($plugin->slug);
                            $isActive = $installed?->status === 'active';
                        @endphp
                        <article class="rounded-3xl border border-[#17130d]/10 bg-white/70 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <strong class="block text-sm font-black">{{ $plugin->name }}</strong>
                                    <span class="mt-1 block text-xs font-bold text-[#17130d]/60">{{ $isActive ? __('kabeeri.ui.active') : ($installed ? __('kabeeri.ui.inactive') : __('kabeeri.ui.not_installed')) }}</span>
                                </div>
                                <span class="grid h-9 w-9 place-items-center rounded-2xl {{ $isActive ? 'bg-[#17130d] text-[#fffaf0]' : 'bg-[#fffaf0] text-[#17130d]' }}"><x-kabeeri-icon name="plugin" style="margin-inline-end:0" /></span>
                            </div>

                            <div class="mt-4">
                                @if (! $installed)
                                    <form method="POST" action="{{ route('customer.apps.plugins.install', ['username' => $site->username, 'package' => $plugin->slug]) }}">
                                        @csrf
                                        <button class="w-full rounded-full bg-[#17130d] px-4 py-2.5 text-xs font-black text-[#fffaf0]" type="submit">{{ __('kabeeri.ui.install_plugin') }}</button>
                                    </form>
                                @elseif ($isActive)
                                    <form method="POST" action="{{ route('customer.apps.plugins.deactivate', ['username' => $site->username, 'package' => $plugin->slug]) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="w-full rounded-full bg-white px-4 py-2.5 text-xs font-black text-[#17130d] ring-1 ring-[#17130d]/10" type="submit">{{ __('kabeeri.ui.deactivate_plugin') }}</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('customer.apps.plugins.activate', ['username' => $site->username, 'package' => $plugin->slug]) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="w-full rounded-full bg-[#17130d] px-4 py-2.5 text-xs font-black text-[#fffaf0]" type="submit">{{ __('kabeeri.ui.activate_plugin') }}</button>
                                    </form>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        </main>
    </div>
</body>
</html>
