<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $site->name }} - Customer App</title>
    @include('customer.v16-style')
</head>
<body>
<div class="shell">
    <header class="top">
        <a class="brand" href="{{ route('customer.workspace') }}">
            <span class="mark">K</span>
            <span><strong>{{ $site->name }}</strong><small>Customer App Detail</small></span>
        </a>
        <nav class="nav">
            <a href="{{ route('customer.workspace') }}">Customer Dashboard</a>
            <a href="{{ route('customer.start') }}">البداية</a>
        </nav>
    </header>

    <main class="hero">
        <section>
            <span class="kicker">App is active</span>
            <h1>{{ $site->name }}</h1>
            <p class="lead">التطبيق مفعل بثيم {{ $site->theme?->name ?? 'بدون ثيم' }}. هذه الصفحة تجمع حالة التثبيت، إعدادات الثيم، والمحتوى الأولي الذي تم إنشاؤه للعميل.</p>
            <div class="grid four">
                <div class="metric"><strong>{{ $site->username }}</strong><span>Username</span></div>
                <div class="metric"><strong>{{ $site->site_type }}</strong><span>Site type</span></div>
                <div class="metric"><strong>{{ $site->metadata['v16_app_type'] ?? 'website' }}</strong><span>App type</span></div>
                <div class="metric"><strong>{{ $site->metadata['theme_install_status'] ?? 'active' }}</strong><span>Theme status</span></div>
            </div>
        </section>
        <aside class="card dark">
            <h2>{{ $site->theme?->name ?? 'No theme' }}</h2>
            <p>{{ $site->theme?->category }} - {{ $site->theme?->publisher }} - Score {{ $site->theme?->performance_score }}</p>
            <div class="list" style="margin-top:14px">
                <div><span>Installed at</span><small>{{ $site->metadata['theme_installed_at'] ?? 'n/a' }}</small></div>
                <div><span>Customer path</span><small>{{ $site->metadata['v16_customer_path'] ?? 'business_owner' }}</small></div>
            </div>
        </aside>
    </main>

    <section class="grid two section">
        <div class="card">
            <span class="tag">Theme settings</span>
            <h2 style="margin-top:10px">إعدادات الثيم</h2>
            <div class="list">
                @forelse ($site->themeSettings as $setting)
                    <div><span>{{ $setting->key }}</span><small>{{ json_encode($setting->value['value'] ?? $setting->value, JSON_UNESCAPED_UNICODE) }}</small></div>
                @empty
                    <div><span>No settings</span><small>Theme settings will appear here.</small></div>
                @endforelse
            </div>
        </div>
        <div class="card">
            <span class="tag">Starter content</span>
            <h2 style="margin-top:10px">المحتوى الأولي</h2>
            <div class="list">
                @forelse ($site->contentEntries as $entry)
                    <div><span>{{ $entry->title }}</span><small>{{ $entry->slug }} / {{ $entry->status }}</small></div>
                @empty
                    <div><span>No content</span><small>Demo content will appear here.</small></div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="card section">
        <span class="tag">Compatible themes</span>
        <h2 style="margin-top:10px">ثيمات أخرى مناسبة لنفس نوع التطبيق</h2>
        <div class="grid three" style="margin-top:14px">
            @foreach ($themes as $theme)
                <article class="card theme" data-score="{{ $theme->performance_score ?? 90 }}">
                    <span class="tag">{{ implode(' / ', $theme->app_types ?? []) }}</span>
                    <h3>{{ $theme->name }}</h3>
                    <p>{{ $theme->category }} - {{ $theme->publisher }} - {{ $theme->price_type }}</p>
                </article>
            @endforeach
        </div>
    </section>
</div>
</body>
</html>
