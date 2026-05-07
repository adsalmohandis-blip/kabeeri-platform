@php
    $title = $title ?? __('kabeeri.ui.apps_dashboard');
    $subtitle = $subtitle ?? __('kabeeri.brand.name');
@endphp

<header id="overview" class="mb-5 rounded-[1.7rem] border border-[#17130d]/10 bg-[#fffaf0]/92 px-4 py-3 shadow-[0_18px_55px_rgba(23,19,13,.08)] backdrop-blur-xl">
    <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
        <div class="flex min-w-0 items-center gap-3">
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-[#17130d] text-sm font-black text-[#fffaf0] lg:hidden">{{ __('kabeeri.brand.mark') }}</span>
            <div class="min-w-0">
                <p class="text-[10px] font-black uppercase tracking-[.18em] text-[#c98a2e]">{{ $subtitle }}</p>
                <h1 class="mt-1 truncate text-lg font-black tracking-[-.02em] sm:text-xl">{{ $title }}</h1>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <details class="w-full sm:w-auto lg:hidden">
                <summary class="cursor-pointer rounded-full bg-[#17130d] px-4 py-2 text-center text-xs font-black text-[#fffaf0]">{{ __('kabeeri.ui.dashboard_nav') }}</summary>
                <nav class="mt-2 grid gap-1 rounded-3xl border border-[#17130d]/10 bg-[#fffaf0] p-2 shadow-[0_18px_55px_rgba(23,19,13,.12)] sm:grid-cols-2" aria-label="{{ __('kabeeri.ui.dashboard_nav') }}">
                    <a href="{{ route('customer.workspace') }}" class="flex items-center gap-2 rounded-2xl bg-white/70 px-3 py-2 text-xs font-black text-[#17130d]"><x-kabeeri-icon name="chart" />{{ __('kabeeri.ui.dashboard_overview') }}</a>
                    <a href="{{ route('customer.apps.index') }}" class="flex items-center gap-2 rounded-2xl bg-white/70 px-3 py-2 text-xs font-black text-[#17130d]"><x-kabeeri-icon name="apps" />{{ __('kabeeri.ui.apps_manage') }}</a>
                    <a href="{{ route('customer.apps.create') }}" class="flex items-center gap-2 rounded-2xl bg-white/70 px-3 py-2 text-xs font-black text-[#17130d]"><x-kabeeri-icon name="plus" />{{ __('kabeeri.ui.new_app') }}</a>
                    <a href="{{ route('customer.apps.trash') }}" class="flex items-center gap-2 rounded-2xl bg-white/70 px-3 py-2 text-xs font-black text-[#17130d]"><x-kabeeri-icon name="trash" />{{ __('kabeeri.ui.trash') }}</a>
                </nav>
            </details>
            <a href="{{ route('customer.start') }}" class="inline-flex items-center rounded-full border border-[#17130d]/10 bg-white/70 px-3 py-2 text-xs font-black text-[#17130d]"><x-kabeeri-icon name="home" />{{ __('kabeeri.ui.home') }}</a>
            @include('components.language-switcher', ['context' => 'customer'])
            @include('components.theme-switcher', ['context' => 'customer'])
            @include('components.font-switcher', ['context' => 'customer'])
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex items-center rounded-full bg-[#17130d] px-3 py-2 text-xs font-black text-[#fffaf0]"><x-kabeeri-icon name="logout" />{{ __('kabeeri.ui.logout') }}</button>
            </form>
        </div>
    </div>
</header>

@if (session('status'))
    <div class="mb-5 rounded-2xl border border-[#c98a2e]/25 bg-[#fffaf0] px-4 py-3 text-xs font-black text-[#17130d] shadow-[0_12px_34px_rgba(23,19,13,.08)]">{{ session('status') }}</div>
@endif
