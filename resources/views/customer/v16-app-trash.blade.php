<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Localization\KabeeriLocale::direction() }}" data-kbr-theme="{{ $kabeeriUi['theme'] ?? 'light' }}" data-kbr-font="{{ $kabeeriUi['font'] ?? 'ibm-plex-sans-arabic' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('kabeeri.ui.trash') }} | {{ __('kabeeri.brand.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f1eadc] text-[#111111] antialiased">
    <div class="kbr-customer-shell grid min-h-screen w-full lg:grid-cols-[16rem_minmax(0,1fr)]">
        @include('customer.partials.dashboard-sidebar', ['dashboard' => $dashboard, 'activeNav' => 'trash'])

        <main class="min-w-0 px-4 py-4 sm:px-6 lg:px-8 lg:py-6">
            @include('customer.partials.dashboard-header', ['title' => __('kabeeri.ui.trash')])

            <section class="rounded-[1.7rem] border border-[#111111]/10 bg-[#f1eadc]/92 p-4 shadow-[0_18px_55px_rgba(17,17,17,.08)]">
                <div class="divide-y divide-[#111111]/10 overflow-hidden rounded-3xl border border-[#111111]/10 bg-white/70">
                    @forelse ($trashedSites as $site)
                        <article class="grid gap-3 px-4 py-4 md:grid-cols-[minmax(0,1fr)_auto] md:items-center">
                            <div>
                                <strong class="block text-sm font-black">{{ $site->name }}</strong>
                                <span class="mt-1 block text-xs font-bold text-[#111111]/60">{{ __('kabeeri.ui.scheduled_delete') }}: {{ $site->metadata['trash_scheduled_delete_at'] ?? '-' }}</span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <form method="POST" action="{{ route('customer.apps.restore', ['username' => $site->username]) }}">
                                    @csrf
                                    <button class="rounded-full bg-[#111111] px-3 py-2 text-xs font-black text-[#f1eadc]" type="submit">{{ __('kabeeri.ui.restore') }}</button>
                                </form>
                                <form method="POST" action="{{ route('customer.apps.schedule-delete', ['username' => $site->username]) }}" class="flex gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="retention_days" class="rounded-full border border-[#111111]/10 bg-[#f1eadc] px-3 py-2 text-xs font-black">
                                        <option value="30">30</option>
                                        <option value="60">60</option>
                                        <option value="90">90</option>
                                    </select>
                                    <button class="rounded-full bg-white px-3 py-2 text-xs font-black text-[#111111] ring-1 ring-[#111111]/10" type="submit">{{ __('kabeeri.ui.schedule_delete') }}</button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="px-5 py-8 text-center text-sm font-black">{{ __('kabeeri.ui.trash_empty') }}</div>
                    @endforelse
                </div>
            </section>
        </main>
    </div>
</body>
</html>
