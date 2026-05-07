<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KABEERI Business Client | Start your app</title>
    <meta name="description" content="KABEERI helps business owners launch a website, store, or service app, choose a theme, and enter a guided customer dashboard.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=almarai:400,700,800|ibm-plex-sans-arabic:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#fff7e8] text-kabeeri-ink antialiased">
    <div class="pointer-events-none fixed inset-0 -z-10 bg-[radial-gradient(circle_at_90%_8%,rgba(47,95,115,.18),transparent_26rem),radial-gradient(circle_at_8%_18%,rgba(201,138,46,.30),transparent_25rem),linear-gradient(135deg,#fffaf0_0%,#f0ddba_58%,#dce7d8_100%)]"></div>
    <div class="pointer-events-none fixed inset-0 -z-10 opacity-20 [background-image:linear-gradient(rgba(23,19,13,.06)_1px,transparent_1px),linear-gradient(90deg,rgba(23,19,13,.045)_1px,transparent_1px)] [background-size:44px_44px] [mask-image:linear-gradient(to_bottom,#000,transparent_82%)]"></div>

    <div class="mx-auto flex min-h-screen w-[min(1180px,calc(100%-24px))] flex-col px-3 py-4 sm:px-5 lg:py-6">
        <header class="flex flex-col gap-3 rounded-[1.75rem] border border-white/70 bg-[#fffaf0]/82 p-3 shadow-kabeeri-soft backdrop-blur-2xl lg:flex-row lg:items-center lg:justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="grid h-11 w-11 place-items-center rounded-2xl bg-[#17130d] text-base font-black text-[#fffaf0]">K</span>
                <span>
                    <strong class="block text-lg font-black">KABEERI Business Client</strong>
                    <small class="block text-xs font-bold text-[#756a5e]">واجهة البداية للعميل، وليست لوحة فحص تقنية</small>
                </span>
            </a>
            <nav class="flex flex-wrap gap-2" aria-label="KABEERI public entry navigation">
                <a class="rounded-full border border-[#17130d]/10 bg-white/60 px-4 py-2 text-sm font-black" href="{{ route('customer.start') }}">ابدأ كعميل</a>
                <a class="rounded-full border border-[#17130d]/10 bg-white/60 px-4 py-2 text-sm font-black" href="{{ route('login') }}">دخول</a>
                <a class="rounded-full bg-[#17130d] px-4 py-2 text-sm font-black text-[#fffaf0]" href="{{ route('register') }}">إنشاء حساب</a>
            </nav>
        </header>

        <main class="grid flex-1 gap-5 py-5 lg:grid-cols-[minmax(0,1fr)_23rem] lg:items-center">
            <section class="rounded-[2.25rem] border border-white/70 bg-[#fffaf0]/86 p-6 shadow-kabeeri-strong backdrop-blur sm:p-8 lg:p-10">
                <span class="inline-flex rounded-full bg-[#315f46]/10 px-3 py-1.5 text-xs font-black uppercase tracking-[.16em] text-[#315f46]">Business / Client First</span>
                <h1 class="mt-5 max-w-4xl text-4xl font-black leading-[1.04] tracking-[-.045em] sm:text-6xl">ابدأ موقعك أو متجرك أو تطبيق خدماتك من مسار واضح.</h1>
                <p class="mt-5 max-w-3xl text-base leading-8 text-[#756a5e] sm:text-lg">KABEERI لا يبدأ بعرض لوحة تقنية. نبدأ بسؤال العميل: ما نوع مشروعك؟ بعدها تختار المسار، نوع التطبيق، الثيم المناسب، ثم تدخل Dashboard خاصة بك لإدارة كل شيء خطوة بخطوة.</p>
                <div class="mt-6 flex flex-wrap gap-2">
                    <a class="rounded-full bg-[#17130d] px-5 py-3 text-sm font-black text-[#fffaf0]" href="{{ route('customer.start') }}">ابدأ مسار العميل</a>
                    <a class="rounded-full border border-[#17130d]/10 bg-white/70 px-5 py-3 text-sm font-black" href="{{ route('public.landing') }}">اعرف المنصة</a>
                    <a class="rounded-full border border-[#17130d]/10 bg-white/70 px-5 py-3 text-sm font-black" href="{{ route('public.pricing') }}">الاشتراكات</a>
                </div>
            </section>

            <aside class="space-y-3">
                <article class="rounded-[1.75rem] border border-white/70 bg-[#17130d] p-5 text-[#fffaf0] shadow-kabeeri-soft">
                    <p class="text-xs font-black uppercase tracking-[.16em] text-[#d8b06b]">What happens next</p>
                    <div class="mt-4 space-y-3">
                        <div class="rounded-2xl border border-white/10 bg-white/[.06] p-3">
                            <strong class="block text-sm font-black">1. اختر مسارك</strong>
                            <p class="mt-1 text-xs leading-6 text-white/62">Business owner، Store owner، Services، Creator، Marketer، أو تحتاج Builder.</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/[.06] p-3">
                            <strong class="block text-sm font-black">2. ثبت التطبيق والثيم</strong>
                            <p class="mt-1 text-xs leading-6 text-white/62">يتم إنشاء Workspace، App، Theme، وstarter content.</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/[.06] p-3">
                            <strong class="block text-sm font-black">3. ادخل داشبورد العميل</strong>
                            <p class="mt-1 text-xs leading-6 text-white/62">لوحة خاصة لإدارة التطبيق والقدرات وطلب Builder.</p>
                        </div>
                    </div>
                </article>

                <article class="rounded-[1.75rem] border border-white/70 bg-[#fffaf0]/86 p-5 shadow-kabeeri-soft backdrop-blur">
                    <p class="text-xs font-black uppercase tracking-[.16em] text-[#9a5539]">For internal team</p>
                    <h2 class="mt-2 text-xl font-black">Command Center انتقل لمسار خاص.</h2>
                    <p class="mt-2 text-sm leading-7 text-[#756a5e]">لوحة الفحص التقنية لم تعد أول صفحة عامة. يمكن الوصول لها بعد تسجيل الدخول من المسار الداخلي.</p>
                    <a class="mt-4 inline-flex rounded-full border border-[#17130d]/10 bg-white/70 px-4 py-2 text-xs font-black" href="{{ route('system.command-center') }}">فتح Internal Command Center</a>
                </article>
            </aside>
        </main>
    </div>
</body>
</html>
