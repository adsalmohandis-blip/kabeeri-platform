<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Dashboard - KABEERI</title>
    @include('customer.v16-style')
</head>
<body>
@php
    $user = $dashboard['user'];
    $profile = $dashboard['profile'];
    $organizations = $dashboard['organizations'];
    $activeOrganization = $dashboard['active_organization'];
    $sites = $dashboard['sites'];
    $capabilityValues = $profile->metadata['capabilities'] ?? ['customer_owner'];
@endphp
<div class="shell">
    <header class="top">
        <a class="brand" href="{{ route('customer.workspace') }}">
            <span class="mark">K</span>
            <span><strong>Customer Dashboard</strong><small>{{ $user->name }}</small></span>
        </a>
        <nav class="nav">
            <a href="{{ route('customer.start') }}">البداية</a>
            <a href="{{ route('public.landing') }}">المنصة</a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">خروج</button></form>
        </nav>
    </header>

    @if (session('status'))
        <p class="notice">{{ session('status') }}</p>
    @endif

    <main class="hero">
        <section>
            <span class="kicker">Workspace overview</span>
            <h1>لوحة تشغيل العميل بعد التفعيل.</h1>
            <p class="lead">هنا يرى العميل تطبيقاته، الثيم المثبت، الخطوات التالية، ويقدر يضيف مسارات مثل مطور، مسوق، أو Kabeeri Builder بدون الدخول إلى صلاحيات الأدمن.</p>
            @unless ($activeOrganization)
                <div class="nav"><a class="button primary" href="{{ route('customer.onboarding') }}">ابدأ Guided Onboarding</a></div>
            @endunless
        </section>
        <aside class="card dark">
            <h2>{{ $activeOrganization?->name ?? 'No workspace yet' }}</h2>
            <p>Organizations: {{ $organizations->count() }} | Apps: {{ $sites->count() }} | Capabilities: {{ count($capabilityValues) }}</p>
            <div class="grid two" style="margin-top:14px">
                <div class="metric"><strong>{{ $sites->count() }}</strong><span>Apps</span></div>
                <div class="metric"><strong>{{ $organizations->count() }}</strong><span>Workspaces</span></div>
            </div>
        </aside>
    </main>

    <section class="grid three section">
        @foreach ($dashboard['cards'] as $card)
            <article class="card">
                <span class="tag">{{ $card['key'] }}</span>
                <h3>{{ $card['label'] }}</h3>
                <p>{{ $card['text'] }}</p>
            </article>
        @endforeach
    </section>

    <section class="grid two section">
        <div class="card">
            <span class="tag">My apps</span>
            <h2 style="margin-top:10px">التطبيقات المفعلة</h2>
            <div class="list">
                @forelse ($sites as $site)
                    <a href="{{ route('customer.apps.show', $site) }}">
                        <span>{{ $site->name }}</span>
                        <small>{{ $site->theme?->name ?? 'No theme' }} / {{ $site->metadata['theme_install_status'] ?? $site->status }}</small>
                    </a>
                @empty
                    <div><span>لا يوجد تطبيق بعد</span><small>ابدأ من onboarding</small></div>
                @endforelse
            </div>
        </div>

        <div class="card">
            <span class="tag">Capabilities</span>
            <h2 style="margin-top:10px">تطوير دورك داخل المنصة</h2>
            <form method="POST" action="{{ route('customer.capabilities.update') }}">
                @csrf
                <div class="checks">
                    @foreach ($dashboard['capabilities'] as $key => $capability)
                        <label class="check">
                            <input type="checkbox" name="capabilities[]" value="{{ $key }}" @checked(in_array($key, $capabilityValues, true)) @disabled($key === 'customer_owner')>
                            <span><strong>{{ $capability['label'] }}</strong><br><span class="muted small">{{ $capability['description'] }}</span></span>
                        </label>
                        @if ($key === 'customer_owner')
                            <input type="hidden" name="capabilities[]" value="customer_owner">
                        @endif
                    @endforeach
                </div>
                <div class="field">
                    <label for="capability_note">ملاحظة</label>
                    <textarea id="capability_note" name="capability_note">{{ $profile->metadata['capability_note'] ?? '' }}</textarea>
                </div>
                <button class="primary" type="submit">تحديث القدرات</button>
            </form>
        </div>
    </section>
</div>
</body>
</html>
