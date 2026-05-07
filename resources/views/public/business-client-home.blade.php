<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Localization\KabeeriLocale::direction() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('kabeeri.brand.name') }} Business Client | Start your app</title>
    <meta name="description" content="{{ __('kabeeri.brand.name') }} helps business owners launch a website, store, or service app, choose a theme, and enter a guided customer dashboard.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=almarai:400,700,800|ibm-plex-sans-arabic:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#fffaf0] text-kabeeri-ink antialiased">
    <div class="pointer-events-none fixed inset-0 -z-10 bg-[radial-gradient(circle_at_90%_8%,rgba(23,19,13,.18),transparent_26rem),radial-gradient(circle_at_8%_18%,rgba(201,138,46,.30),transparent_25rem),linear-gradient(135deg,#fffaf0_0%,#fffaf0_58%,#17130d_100%)]"></div>
    <div class="pointer-events-none fixed inset-0 -z-10 opacity-20 [background-image:linear-gradient(rgba(23,19,13,.06)_1px,transparent_1px),linear-gradient(90deg,rgba(23,19,13,.045)_1px,transparent_1px)] [background-size:44px_44px] [mask-image:linear-gradient(to_bottom,#000,transparent_82%)]"></div>

    <div class="mx-auto flex min-h-screen w-[min(1180px,calc(100%-24px))] flex-col px-3 py-4 sm:px-5 lg:py-6">
        <header class="flex flex-col gap-3 rounded-3xl border border-[#17130d]/10 bg-[#fffaf0]/82 p-3 backdrop-blur-2xl lg:flex-row lg:items-center lg:justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-2xl bg-[#17130d] text-sm font-black text-[#fffaf0]">K</span>
                <span>
                    <strong class="block text-base font-black">{{ __('kabeeri.brand.name') }} Business Client</strong>
                    <small class="block text-xs font-bold text-[#17130d]">ابدأ مشروعك من مسار واضح</small>
                </span>
            </a>
            <nav class="flex flex-wrap gap-2" aria-label="{{ __('kabeeri.brand.name') }} public entry navigation">
                <a class="inline-flex items-center rounded-full border border-[#17130d]/10 bg-white/60 px-3 py-1.5 text-xs font-black" href="{{ route('customer.start') }}"><x-kabeeri-icon name="rocket" />ابدأ كعميل</a>
                <a class="inline-flex items-center rounded-full border border-[#17130d]/10 bg-white/60 px-3 py-1.5 text-xs font-black" href="{{ route('login') }}"><x-kabeeri-icon name="login" />دخول</a>
                <a class="inline-flex items-center rounded-full bg-[#17130d] px-3 py-1.5 text-xs font-black text-[#fffaf0]" href="{{ route('register') }}"><x-kabeeri-icon name="user-plus" />إنشاء حساب</a>
                @include('components.language-switcher', ['context' => 'visitor'])
            </nav>
        </header>

        <main class="grid flex-1 gap-5 py-6 lg:grid-cols-[minmax(0,1fr)_20rem] lg:items-center">
            <section class="rounded-[2rem] border border-[#17130d]/10 bg-[#fffaf0]/86 p-5 backdrop-blur sm:p-6 lg:p-7">
                <span class="inline-flex items-center rounded-full bg-[#17130d]/10 px-3 py-1.5 text-xs font-black uppercase tracking-[.16em] text-[#17130d]"><x-kabeeri-icon name="store" />Business / Client First</span>
                <h1 class="mt-5 max-w-3xl text-2xl font-black leading-[1.12] tracking-[-.03em] sm:text-3xl">ابدأ موقعك أو متجرك أو تطبيق خدماتك من مسار واضح.</h1>
                <p class="mt-4 max-w-2xl text-sm leading-7 text-[#17130d]">اختر نوع مشروعك، ثبت الثيم، ثم ادخل لوحة إدارة التطبيقات.</p>
                <div class="mt-5 flex flex-wrap gap-2">
                    <a class="inline-flex items-center rounded-full bg-[#17130d] px-4 py-2.5 text-xs font-black text-[#fffaf0]" href="{{ route('customer.start') }}"><x-kabeeri-icon name="rocket" />ابدأ مسار العميل</a>
                    <a class="inline-flex items-center rounded-full border border-[#17130d]/10 bg-white/70 px-4 py-2.5 text-xs font-black" href="{{ route('public.landing') }}"><x-kabeeri-icon name="info" />اعرف المنصة</a>
                    <a class="inline-flex items-center rounded-full border border-[#17130d]/10 bg-white/70 px-4 py-2.5 text-xs font-black" href="{{ route('public.pricing') }}"><x-kabeeri-icon name="pricing" />الاشتراكات</a>
                </div>
            </section>

            <aside class="space-y-2.5">
                <article class="rounded-3xl border border-[#17130d]/10 bg-[#17130d] p-4 text-[#fffaf0]">
                    <p class="text-xs font-black uppercase tracking-[.16em] text-[#c98a2e]">What happens next</p>
                    <ol class="mt-3 space-y-2 text-sm font-black">
                        <li class="flex items-center gap-2"><span class="grid h-6 w-6 place-items-center rounded-full bg-[#c98a2e] text-[11px] text-[#17130d]"><x-kabeeri-icon name="map" style="margin-inline-end:0" /></span><span>اختر مسارك</span></li>
                        <li class="flex items-center gap-2"><span class="grid h-6 w-6 place-items-center rounded-full bg-[#c98a2e] text-[11px] text-[#17130d]"><x-kabeeri-icon name="theme" style="margin-inline-end:0" /></span><span>ثبت التطبيق والثيم</span></li>
                        <li class="flex items-center gap-2"><span class="grid h-6 w-6 place-items-center rounded-full bg-[#c98a2e] text-[11px] text-[#17130d]"><x-kabeeri-icon name="apps" style="margin-inline-end:0" /></span><span>افتح لوحة إدارة التطبيقات</span></li>
                    </ol>
                </article>

                <article class="rounded-3xl border border-[#17130d]/10 bg-[#fffaf0]/86 p-4 backdrop-blur">
                    <p class="text-xs font-black uppercase tracking-[.16em] text-[#17130d]">For internal team</p>
                    <h2 class="mt-2 text-base font-black">Command Center انتقل لمسار خاص.</h2>
                    <a class="mt-3 inline-flex items-center rounded-full border border-[#17130d]/10 bg-white/70 px-3 py-1.5 text-xs font-black" href="{{ route('system.command-center') }}"><x-kabeeri-icon name="command" />فتح Internal Command Center</a>
                </article>
            </aside>
        </main>
    </div>
</body>
</html>
