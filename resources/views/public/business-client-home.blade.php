<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Localization\KabeeriLocale::direction() }}" data-kbr-theme="{{ $kabeeriUi['theme'] ?? 'light' }}" data-kbr-font="{{ $kabeeriUi['font'] ?? 'ibm-plex-sans-arabic' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('kabeeri.brand.name') }} | {{ __('kabeeri.ui.start_now') }}</title>
    <meta name="description" content="{{ __('kabeeri.ui.hero_lead') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=almarai:400,700,800|ibm-plex-sans-arabic:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#f1eadc] text-kabeeri-ink antialiased">
    <div class="pointer-events-none fixed inset-0 -z-10 bg-[radial-gradient(circle_at_90%_8%,rgba(17,17,17,.18),transparent_26rem),radial-gradient(circle_at_8%_18%,rgba(17,17,17,.30),transparent_25rem),linear-gradient(135deg,#f1eadc_0%,#f1eadc_58%,#111111_100%)]"></div>
    <div class="pointer-events-none fixed inset-0 -z-10 opacity-20 [background-image:linear-gradient(rgba(17,17,17,.06)_1px,transparent_1px),linear-gradient(90deg,rgba(17,17,17,.045)_1px,transparent_1px)] [background-size:44px_44px] [mask-image:linear-gradient(to_bottom,#000,transparent_82%)]"></div>

    <div class="mx-auto flex min-h-screen w-[min(1180px,calc(100%-24px))] flex-col px-3 py-4 sm:px-5 lg:py-6">
        <header class="flex flex-col gap-3 rounded-3xl border border-[#111111]/10 bg-[#f1eadc]/82 p-3 backdrop-blur-2xl lg:flex-row lg:items-center lg:justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-2xl bg-[#111111] text-sm font-black text-[#f1eadc]">{{ __('kabeeri.brand.mark') }}</span>
                <span>
                    <strong class="block text-base font-black">{{ __('kabeeri.brand.name') }}</strong>
                    <small class="block text-xs font-bold text-[#111111]">{{ __('kabeeri.ui.start_now') }}</small>
                </span>
            </a>
            <nav class="flex flex-wrap gap-2" aria-label="{{ __('kabeeri.ui.public_home') }}">
                <a class="inline-flex items-center rounded-full border border-[#111111]/10 bg-white/60 px-3 py-1.5 text-xs font-black" href="{{ route('customer.start') }}"><x-kabeeri-icon name="rocket" />{{ __('kabeeri.ui.start_now') }}</a>
                <a class="inline-flex items-center rounded-full border border-[#111111]/10 bg-white/60 px-3 py-1.5 text-xs font-black" href="{{ route('login') }}"><x-kabeeri-icon name="login" />{{ __('kabeeri.ui.login') }}</a>
                <a class="inline-flex items-center rounded-full bg-[#111111] px-3 py-1.5 text-xs font-black text-[#f1eadc]" href="{{ route('register') }}"><x-kabeeri-icon name="user-plus" />{{ __('kabeeri.ui.create_account') }}</a>
                @include('components.language-switcher', ['context' => 'platform_public'])
                @include('components.theme-switcher', ['context' => 'platform_public'])
            </nav>
        </header>

        <main class="grid flex-1 gap-5 py-6 lg:grid-cols-[minmax(0,1fr)_20rem] lg:items-center">
            <section class="rounded-[2rem] border border-[#111111]/10 bg-[#f1eadc]/86 p-5 backdrop-blur sm:p-6 lg:p-7">
                <span class="inline-flex items-center rounded-full bg-[#111111]/10 px-3 py-1.5 text-xs font-black tracking-[.08em] text-[#111111]"><x-kabeeri-icon name="store" />{{ __('kabeeri.ui.hero_badge') }}</span>
                <h1 class="mt-5 max-w-3xl text-2xl font-black leading-[1.12] tracking-[-.03em] sm:text-3xl">{{ __('kabeeri.ui.hero_title') }}</h1>
                <p class="mt-4 max-w-2xl text-sm leading-7 text-[#111111]">{{ __('kabeeri.ui.hero_lead') }}</p>
                <div class="mt-5 flex flex-wrap gap-2">
                    <a class="inline-flex items-center rounded-full bg-[#111111] px-4 py-2.5 text-xs font-black text-[#f1eadc]" href="{{ route('customer.start') }}"><x-kabeeri-icon name="rocket" />{{ __('kabeeri.ui.start_now') }}</a>
                    <a class="inline-flex items-center rounded-full border border-[#111111]/10 bg-white/70 px-4 py-2.5 text-xs font-black" href="{{ route('public.landing') }}"><x-kabeeri-icon name="info" />{{ __('kabeeri.ui.features') }}</a>
                    <a class="inline-flex items-center rounded-full border border-[#111111]/10 bg-white/70 px-4 py-2.5 text-xs font-black" href="{{ route('public.pricing') }}"><x-kabeeri-icon name="pricing" />{{ __('kabeeri.ui.subscriptions') }}</a>
                </div>
            </section>

            <aside class="space-y-2.5">
                <article class="rounded-3xl border border-[#111111]/10 bg-[#111111] p-4 text-[#f1eadc]">
                    <p class="text-xs font-black tracking-[.08em] text-[#111111]">{{ __('kabeeri.ui.your_steps') }}</p>
                    <ol class="mt-3 space-y-2 text-sm font-black">
                        <li class="flex items-center gap-2"><span class="grid h-6 w-6 place-items-center rounded-full bg-[#111111] text-[11px] text-[#111111]"><x-kabeeri-icon name="map" style="margin-inline-end:0" /></span><span>{{ __('kabeeri.ui.choose_path') }}</span></li>
                        <li class="flex items-center gap-2"><span class="grid h-6 w-6 place-items-center rounded-full bg-[#111111] text-[11px] text-[#111111]"><x-kabeeri-icon name="theme" style="margin-inline-end:0" /></span><span>{{ __('kabeeri.ui.install_app_theme') }}</span></li>
                        <li class="flex items-center gap-2"><span class="grid h-6 w-6 place-items-center rounded-full bg-[#111111] text-[11px] text-[#111111]"><x-kabeeri-icon name="apps" style="margin-inline-end:0" /></span><span>{{ __('kabeeri.ui.open_dashboard') }}</span></li>
                    </ol>
                </article>
            </aside>
        </main>
    </div>
</body>
</html>
