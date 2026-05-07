@php
    $activeNav = 'themes';
@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Localization\KabeeriLocale::direction() }}" data-kbr-theme="{{ $kabeeriUi['theme'] ?? 'light' }}" data-kbr-font="{{ $kabeeriUi['font'] ?? 'ibm-plex-sans-arabic' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('kabeeri.ui.switch_theme') }} | {{ __('kabeeri.brand.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#fffaf0] text-[#17130d] antialiased">
    <div class="kbr-customer-shell grid min-h-screen w-full lg:grid-cols-[16rem_minmax(0,1fr)]">
        @include('customer.partials.dashboard-sidebar', ['dashboard' => $dashboard, 'activeNav' => $activeNav])

        <main class="min-w-0 px-4 py-4 sm:px-6 lg:px-8 lg:py-6">
            @include('customer.partials.dashboard-header', ['title' => __('kabeeri.ui.switch_theme'), 'subtitle' => $site->name])

            <section class="rounded-[1.7rem] border border-[#17130d]/10 bg-[#fffaf0]/92 p-4 shadow-[0_18px_55px_rgba(23,19,13,.08)]">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <span class="inline-flex items-center rounded-full bg-[#17130d]/10 px-3 py-1 text-[11px] font-black text-[#17130d]"><x-kabeeri-icon name="theme" />{{ __('kabeeri.ui.current_theme') }}</span>
                        <h2 class="mt-2 text-base font-black">{{ $site->theme ? __('kabeeri.customer.themes.'.$site->theme->slug.'.name') : __('kabeeri.ui.no_theme') }}</h2>
                    </div>
                    <a href="{{ route('customer.apps.show', ['username' => $site->username]) }}" class="inline-flex items-center justify-center rounded-full bg-white px-4 py-2 text-xs font-black text-[#17130d] ring-1 ring-[#17130d]/10"><x-kabeeri-icon name="apps" />{{ __('kabeeri.ui.app_detail') }}</a>
                </div>

                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($themes as $theme)
                        @php($isCurrent = $site->theme?->slug === $theme->slug)
                        <article class="rounded-3xl border border-[#17130d]/10 bg-white/70 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <strong class="block text-sm font-black">{{ __('kabeeri.customer.themes.'.$theme->slug.'.name') }}</strong>
                                    <span class="mt-1 block text-xs font-bold text-[#17130d]/60">{{ __('kabeeri.customer.themes.'.$theme->slug.'.category') }}</span>
                                </div>
                                <span class="rounded-full {{ $isCurrent ? 'bg-[#17130d] text-[#fffaf0]' : 'bg-[#fffaf0] text-[#17130d]' }} px-3 py-1 text-[11px] font-black ring-1 ring-[#17130d]/10">{{ $isCurrent ? __('kabeeri.ui.active') : __('kabeeri.ui.compatible') }}</span>
                            </div>

                            <form method="POST" action="{{ route('customer.apps.themes.update', ['username' => $site->username]) }}" class="mt-4">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="theme_slug" value="{{ $theme->slug }}">
                                <button class="w-full rounded-full {{ $isCurrent ? 'bg-white text-[#17130d] ring-1 ring-[#17130d]/10' : 'bg-[#17130d] text-[#fffaf0]' }} px-4 py-2.5 text-xs font-black" type="submit" @disabled($isCurrent)>
                                    {{ $isCurrent ? __('kabeeri.ui.current_theme') : __('kabeeri.ui.switch_theme') }}
                                </button>
                            </form>
                        </article>
                    @endforeach
                </div>
            </section>
        </main>
    </div>
</body>
</html>
