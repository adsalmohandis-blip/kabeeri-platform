@php
    $dashboard = $dashboard ?? \App\Support\RootDashboardData::make();
    $summary = $dashboard['task_summary'];
    $backend = $dashboard['backend_summary'];
    $ui = $dashboard['ui_summary'];
    $versions = $dashboard['versions'];
    $inventory = $dashboard['system_inventory'];
    $groups = $dashboard['database_groups'];
    $surface = $dashboard['public_surface'];
    $plans = $dashboard['plans'];
    $audiences = $dashboard['audiences'];
    $onboarding = $dashboard['onboarding'];
    $developerFlow = $dashboard['developer_flow'];
    $history = $dashboard['latest_history'];
    $adminRoutes = $dashboard['admin_routes'];
    $docs = $dashboard['docs'];
    $release = $dashboard['release_status'];
    $productionChecklist = $dashboard['production_checklist'];
    $verificationSteps = $dashboard['verification_steps'];
    $nextRuntime = $dashboard['next_runtime'];
    $filamentResources = collect($inventory)->firstWhere('label', 'Filament Resources')['value'] ?? 0;
    $fmt = fn ($value) => number_format((int) $value);
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Localization\KabeeriLocale::direction() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KABEERI Command Center | لوحة جاهزية المنصة</title>
    <meta name="description" content="لوحة KABEERI الرئيسية تعرض حالة التنفيذ، التراكر، قاعدة البيانات، Release Candidate، Next.js runtime، ومسارات الجمهور والمطورين.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=almarai:400,700,800|ibm-plex-sans-arabic:400,500,600,700" rel="stylesheet" />
    <style>
        :root{--ink:#17130d;--muted:#17130d;--paper:#fffaf0;--panel:rgba(255,250,240,.78);--panel-strong:rgba(255,250,240,.93);--line:rgba(23,19,13,.13);--night:#17130d;--forest:#17130d;--sage:#17130d;--gold:#c98a2e;--wheat:#fffaf0;--clay:#17130d;--sky:#17130d;--danger:#17130d;--good:#17130d;--shadow:0 30px 90px rgba(23,19,13,.16);--soft:0 18px 50px rgba(23,19,13,.09);--radius:30px}
        *{box-sizing:border-box}html{scroll-behavior:smooth}body{margin:0;min-height:100vh;color:var(--ink);font-family:"IBM Plex Sans Arabic","Almarai",sans-serif;background:radial-gradient(circle at 8% 8%,rgba(23,19,13,.48),transparent 25rem),radial-gradient(circle at 92% 4%,rgba(201,138,46,.38),transparent 28rem),radial-gradient(circle at 70% 98%,rgba(23,19,13,.18),transparent 34rem),linear-gradient(135deg,#fffaf0 0%,#fffaf0 52%,#c98a2e 100%);background-attachment:fixed}body:before{content:"";position:fixed;inset:0;z-index:-1;opacity:.18;background-image:linear-gradient(rgba(23,19,13,.07) 1px,transparent 1px),linear-gradient(90deg,rgba(23,19,13,.06) 1px,transparent 1px);background-size:42px 42px;mask-image:linear-gradient(to bottom,#000,transparent 84%)}a{color:inherit;text-decoration:none}.shell{width:min(1420px,calc(100% - 34px));margin:0 auto;padding:18px 0 64px}.topbar{position:sticky;top:12px;z-index:20;display:flex;align-items:center;justify-content:space-between;gap:14px;padding:12px;border:1px solid var(--line);border-radius:28px;background:rgba(255,250,240,.72);backdrop-filter:blur(18px);box-shadow:0 14px 42px rgba(23,19,13,.08)}.brand{display:flex;align-items:center;gap:12px}.mark{width:54px;height:54px;display:grid;place-items:center;border-radius:19px;color:var(--paper);background:conic-gradient(from 220deg,#17130d,#17130d,#c98a2e,#17130d,#17130d);font-family:Almarai,sans-serif;font-weight:800;font-size:22px}.brand strong{display:block;font-family:Almarai,sans-serif;font-size:18px}.brand span{display:block;color:var(--muted);font-size:13px}.topnav{display:flex;flex-wrap:wrap;gap:8px;justify-content:flex-end}.nav-link{display:inline-flex;align-items:center;justify-content:center;min-height:40px;padding:0 13px;border:1px solid rgba(23,19,13,.11);border-radius:999px;background:rgba(255,255,255,.45);font-size:13px;font-weight:800}.nav-link:hover{background:var(--paper);transform:translateY(-1px)}.hero{position:relative;overflow:hidden;margin-top:20px;border:1px solid var(--line);border-radius:46px;background:linear-gradient(145deg,rgba(255,250,240,.94),rgba(255,250,240,.74));box-shadow:var(--shadow)}.hero:before{content:"";position:absolute;width:430px;height:430px;inset-inline-start:-160px;top:-170px;border-radius:999px;background:repeating-linear-gradient(45deg,rgba(23,19,13,.15) 0 11px,transparent 11px 25px);animation:floatA 19s ease-in-out infinite alternate}.hero-grid{position:relative;display:grid;grid-template-columns:minmax(0,1.08fr) minmax(370px,.92fr);gap:24px;padding:clamp(26px,5vw,64px)}.kicker,.eyebrow{display:inline-flex;align-items:center;gap:10px;width:fit-content;padding:9px 13px;border:1px solid rgba(23,19,13,.13);border-radius:999px;background:rgba(255,255,255,.55);color:#17130d;font-weight:800;font-size:13px}.dot{width:10px;height:10px;border-radius:999px;background:var(--good);box-shadow:0 0 0 7px rgba(23,19,13,.14)}h1{max-width:980px;margin:22px 0 0;font-family:Almarai,sans-serif;font-size:clamp(40px,6.5vw,86px);line-height:1.02;letter-spacing:-2px}.lead{max-width:860px;margin:22px 0 0;color:var(--muted);font-size:clamp(17px,1.7vw,22px);line-height:1.95}.actions{display:flex;flex-wrap:wrap;gap:12px;margin-top:30px}.button{display:inline-flex;align-items:center;justify-content:center;min-height:52px;padding:0 22px;border:1px solid rgba(23,19,13,.15);border-radius:18px;font-weight:800;transition:transform .18s ease,box-shadow .18s ease}.button:hover{transform:translateY(-2px);box-shadow:0 16px 36px rgba(23,19,13,.14)}.primary{color:var(--paper);background:var(--night)}.secondary{background:rgba(255,255,255,.60)}.ghost{background:rgba(23,19,13,.12);color:var(--forest)}.command-card{padding:24px;border-radius:32px;color:var(--paper);background:linear-gradient(160deg,rgba(23,19,13,.97),rgba(23,19,13,.94) 58%,rgba(23,19,13,.92));box-shadow:0 24px 64px rgba(23,19,13,.22)}.command-card h2{margin:0;font-family:Almarai,sans-serif;font-size:30px}.command-card p{margin:10px 0 18px;color:rgba(255,250,240,.76);line-height:1.8}.hero-metrics{display:grid;grid-template-columns:repeat(2,1fr);gap:10px}.hero-metric{padding:17px;border-radius:22px;background:rgba(255,250,240,.09);border:1px solid rgba(255,250,240,.13)}.hero-metric span{display:block;color:rgba(255,250,240,.66);font-size:12px;font-weight:800;text-transform:uppercase}.hero-metric strong{display:block;margin-top:7px;font-family:Almarai,sans-serif;font-size:34px}.hero-metric small{color:rgba(255,250,240,.72)}.section{margin-top:38px}.section-title{display:flex;justify-content:space-between;align-items:end;gap:18px;margin-bottom:16px}.section-title h2{margin:0;font-family:Almarai,sans-serif;font-size:clamp(30px,3.2vw,48px);letter-spacing:-.9px}.section-title p{max-width:720px;margin:0;color:var(--muted);line-height:1.85}.grid-2{display:grid;grid-template-columns:repeat(2,1fr);gap:16px}.grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}.grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}.card{border:1px solid var(--line);border-radius:var(--radius);background:var(--panel);box-shadow:var(--soft);padding:24px}.card h3{margin:0 0 10px;font-family:Almarai,sans-serif;font-size:24px}.card p{margin:0;color:var(--muted);line-height:1.82}.tag{display:inline-flex;margin-bottom:14px;padding:8px 11px;border-radius:999px;background:rgba(23,19,13,.12);color:var(--forest);font-size:12px;font-weight:800}.dark{color:var(--paper);background:linear-gradient(145deg,#17130d,#17130d)}.dark p,.dark li,.dark small{color:rgba(255,250,240,.76)}.stat-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:12px}.stat{padding:20px;border:1px solid var(--line);border-radius:26px;background:rgba(255,250,240,.68);box-shadow:var(--soft)}.stat span{display:block;color:var(--muted);font-size:12px;font-weight:800}.stat strong{display:block;margin-top:8px;font-family:Almarai,sans-serif;font-size:30px}.stat small{display:block;margin-top:4px;color:var(--muted)}.good strong{color:var(--good)}.warn strong{color:var(--clay)}.danger strong{color:var(--danger)}.version-list{display:grid;gap:10px}.version-row{display:grid;grid-template-columns:82px 1fr 92px 110px;gap:12px;align-items:center;padding:13px 14px;border-radius:18px;background:rgba(255,255,255,.48);border:1px solid rgba(23,19,13,.07)}.version-row strong{font-family:Almarai,sans-serif}.version-row small{color:var(--muted)}.bar{height:10px;border-radius:999px;background:rgba(23,19,13,.09);overflow:hidden}.bar span{display:block;height:100%;border-radius:inherit;background:linear-gradient(90deg,var(--forest),var(--gold))}.pill{display:inline-flex;justify-content:center;align-items:center;min-height:32px;padding:0 10px;border-radius:999px;background:rgba(23,19,13,.13);color:var(--good);font-weight:800;font-size:12px}.pill.pending{background:rgba(201,138,46,.13);color:#17130d}.pill.review{background:rgba(201,138,46,.16);color:#17130d}.data-group{display:flex;flex-direction:column;gap:12px}.group-head{display:flex;justify-content:space-between;gap:12px;align-items:center}.group-head strong{font-family:Almarai,sans-serif;font-size:21px}.group-head span{color:var(--muted)}.table-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:8px}.table-chip{display:flex;justify-content:space-between;gap:10px;padding:10px 11px;border-radius:14px;background:rgba(255,255,255,.48);font-size:13px}.table-chip b{font-family:Almarai,sans-serif}.route-list,.history-list,.doc-list,.check-list{display:grid;gap:10px;margin-top:14px}.route-list a,.doc-list div,.history-item,.check-item{display:flex;justify-content:space-between;gap:14px;align-items:center;padding:12px 14px;border-radius:16px;background:rgba(255,255,255,.48);border:1px solid rgba(23,19,13,.07);font-weight:800}.history-item{display:block;font-weight:500}.history-item strong{font-weight:800}.history-item p{margin:6px 0 0;font-size:13px}.dark .route-list a,.dark .check-item{background:rgba(255,250,240,.10);border-color:rgba(255,250,240,.12)}.price{font-family:Almarai,sans-serif;font-size:32px;margin:12px 0}.price small{font-size:13px;color:var(--muted)}.step{position:relative;overflow:hidden;min-height:220px}.step:after{content:attr(data-step);position:absolute;inset-inline-end:18px;bottom:6px;color:rgba(23,19,13,.06);font-family:Almarai,sans-serif;font-size:88px;font-weight:800}.step small{color:var(--clay);font-weight:800}.chips{display:flex;flex-wrap:wrap;gap:8px;margin-top:14px}.chip{padding:8px 10px;border-radius:999px;background:rgba(255,255,255,.55);font-size:12px;font-weight:800}.next-box{margin-top:10px;padding:12px;border-radius:16px;background:rgba(201,138,46,.09);color:#17130d;font-size:13px;line-height:1.7}.final{margin-top:38px;padding:clamp(26px,5vw,50px);border-radius:40px;border:1px solid var(--line);background:linear-gradient(145deg,rgba(255,250,240,.92),rgba(255,250,240,.78));box-shadow:var(--shadow);text-align:center}.final h2{margin:0;font-family:Almarai,sans-serif;font-size:clamp(32px,5vw,60px);line-height:1.15}.final p{max-width:850px;margin:18px auto 26px;color:var(--muted);line-height:1.9;font-size:18px}@keyframes floatA{from{transform:translate3d(0,0,0) rotate(0)}to{transform:translate3d(42px,30px,0) rotate(10deg)}}@media(max-width:1180px){.hero-grid,.grid-2{grid-template-columns:1fr}.stat-grid{grid-template-columns:repeat(3,1fr)}.grid-3,.grid-4{grid-template-columns:repeat(2,1fr)}}@media(max-width:760px){.shell{width:min(100% - 22px,1420px);padding-top:12px}.topbar{position:static;flex-direction:column;align-items:stretch;border-radius:22px}.topnav{justify-content:stretch}.nav-link{flex:1}.hero{border-radius:30px}.hero-grid{grid-template-columns:1fr;padding:24px}h1{letter-spacing:-1.1px}.hero-metrics,.stat-grid,.grid-3,.grid-4,.table-grid{grid-template-columns:1fr}.section-title{flex-direction:column;align-items:start}.version-row{grid-template-columns:64px 1fr}.version-row .pill,.version-row small{text-align:right;justify-content:flex-start}.route-list a,.doc-list div,.check-item{align-items:flex-start;flex-direction:column}}
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <a class="brand" href="/">
                <div class="mark">K</div>
                <div>
                    <strong>KABEERI Command Center</strong>
                    <span>لوحة تشغيل واحدة للأدمن، الجمهور، التراكر، Release Candidate، وNext.js runtime</span>
                </div>
            </a>
            <nav class="topnav" aria-label="روابط لوحة البيانات">
                <a class="nav-link" href="#readiness">الجاهزية</a>
                <a class="nav-link" href="#task-tracker">Task Tracker Truth</a>
                <a class="nav-link" href="#database">Database</a>
                <a class="nav-link" href="#public">الجمهور</a>
                <a class="nav-link" href="#plans">Freemium</a>
                <a class="nav-link" href="#developers">Developers</a>
                <a class="nav-link" href="/admin">Filament</a>
                @include('components.language-switcher', ['context' => 'admin'])
            </nav>
        </header>

        <main class="hero">
            <div class="hero-grid">
                <section>
                    <div class="kicker"><span class="dot"></span>{{ $release['label'] }}</div>
                    <h1>لوحة كابيري الرئيسية محدثة لتعكس الحقيقة بعد V16.</h1>
                    <p class="lead">هذه الصفحة تفصل بوضوح بين قسم الأدمن الداخلي وقسم العرض الخارجي. تعرض حالة التنفيذ من V1 إلى V16، جاهزية V14/V15، مسار العميل الجديد، قاعدة البيانات، public routes، Next.js public runtime، وما تبقى قبل staging أو production.</p>
                    <div class="actions">
                        <a class="button primary" href="#readiness">راجع الجاهزية</a>
                        <a class="button secondary" href="/ui/release-candidate">افتح V14 RC</a>
                        <a class="button ghost" href="/api/public-web/manifest">افتح V15 Manifest</a>
                    </div>
                </section>

                <aside class="command-card">
                    <h2>حالة النظام الآن</h2>
                    <p>{{ $release['production_label'] }}. اللوحة جاهزة لتوجيه مراجعة staging، وليست وعدًا بنشر إنتاجي بدون مراجعة مالك.</p>
                    <div class="hero-metrics">
                        <div class="hero-metric"><span>كل التاسكات</span><strong>{{ $summary['percent'] }}%</strong><small>{{ $fmt($summary['done']) }} / {{ $fmt($summary['total']) }}</small></div>
                        <div class="hero-metric"><span>UI V9-V16</span><strong>{{ $ui['percent'] }}%</strong><small>{{ $fmt($ui['pending']) }} pending</small></div>
                        <div class="hero-metric"><span>V15 Runtime</span><strong>{{ $release['v15_ready'] ? 'Ready' : 'Review' }}</strong><small>{{ $nextRuntime['contract'] }}</small></div>
                        <div class="hero-metric"><span>Filament Resources</span><strong>{{ $fmt($filamentResources) }}</strong><small>Admin resources</small></div>
                    </div>
                </aside>
            </div>
        </main>

        <section class="section" id="readiness">
            <div class="section-title">
                <div>
                    <span class="eyebrow">قسم الأدمن الداخلي</span>
                    <h2>جاهزية Release Candidate مقابل جاهزية الإنتاج.</h2>
                </div>
                <p>المشروع جاهز كـ Release Candidate قوي، ومع V16 أصبح لديه مدخل عميل عام ومسار إنشاء تطبيق. الإنتاج النهائي يحتاج Owner Verification وبيئة staging/production مضبوطة.</p>
            </div>
            <div class="stat-grid">
                <div class="stat good"><span>V14 RC</span><strong>{{ $release['v14_ready'] ? 'Ready' : 'Review' }}</strong><small>UI quality gates</small></div>
                <div class="stat good"><span>V15 RC</span><strong>{{ $release['v15_ready'] ? 'Ready' : 'Review' }}</strong><small>Next public runtime</small></div>
                <div class="stat good"><span>Tracker</span><strong>{{ $release['tracker_done'] ? 'Done' : 'Open' }}</strong><small>V1-V16 status truth</small></div>
                <div class="stat warn"><span>Owner Verified</span><strong>{{ $summary['verified'] }}</strong><small>تحتاج اعتماد يدوي</small></div>
                <div class="stat {{ $release['production_ready'] ? 'good' : 'warn' }}"><span>Production</span><strong>{{ $release['production_ready'] ? 'Ready' : 'Not Yet' }}</strong><small>بعد staging</small></div>
                <div class="stat"><span>Backend</span><strong>{{ $backend['percent'] }}%</strong><small>V1-V8 + extensions</small></div>
            </div>

            <div class="grid-2" style="margin-top:16px;">
                <article class="card dark">
                    <span class="tag">Admin Quick Access</span>
                    <h3>روابط التشغيل والفحص</h3>
                    <p>الروابط التي تحتاجها قبل أي عرض للعميل أو نشر على staging.</p>
                    <div class="route-list">
                        @foreach ($adminRoutes as $route)
                            <a href="{{ $route['url'] }}"><span>{{ $route['name'] }}</span><small>{{ $route['desc'] }}</small></a>
                        @endforeach
                    </div>
                </article>
                <article class="card">
                    <span class="tag">Production Checklist</span>
                    <h3>ما المتبقي قبل النشر؟</h3>
                    <div class="check-list">
                        @foreach ($productionChecklist as $item)
                            <div class="check-item">
                                <span class="pill {{ $item['status'] === 'ready' ? '' : 'review' }}">{{ $item['status'] }}</span>
                                <strong>{{ $item['title'] }}</strong>
                                <small>{{ $item['note'] }}</small>
                            </div>
                        @endforeach
                    </div>
                </article>
            </div>
        </section>

        <section class="section" id="task-tracker">
            <div class="section-title">
                <div>
                    <span class="eyebrow">Task Tracker Truth</span>
                    <h2>كل Versions من V1 إلى V16 في مكان واحد.</h2>
                </div>
                <p>الأرقام هنا تُقرأ من ملفات التراكر، وليس من نص ثابت داخل الصفحة. لو التراكر اتغير، اللوحة تتغير معه.</p>
            </div>
            <div class="grid-2">
                <article class="card">
                    <h3>حالة Versions</h3>
                    <div class="version-list">
                        @foreach ($versions as $version)
                            <div class="version-row">
                                <strong>{{ $version['name'] }}</strong>
                                <div class="bar" title="{{ $version['percent'] }}%"><span style="width: {{ $version['percent'] }}%"></span></div>
                                <span class="pill {{ $version['pending'] ? 'pending' : '' }}">{{ $version['percent'] }}%</span>
                                <small>{{ $version['pending'] ? $fmt($version['pending']).' pending' : 'ready' }}</small>
                            </div>
                            @if ($version['next_tasks'])
                                <div class="next-box">
                                    <strong>التالي في {{ $version['name'] }}:</strong>
                                    {{ collect($version['next_tasks'])->map(fn ($task) => trim(($task['id'] ?? '').' '.($task['title'] ?? '')))->implode(' | ') }}
                                </div>
                            @endif
                        @endforeach
                    </div>
                </article>
                <article class="card">
                    <h3>آخر أحداث task_history.jsonl</h3>
                    <p>هذا القسم يساعدنا نعرف هل التنفيذ الحقيقي متوافق مع التسجيل.</p>
                    <div class="history-list">
                        @forelse ($history as $item)
                            <div class="history-item">
                                <strong>{{ $item['version'] }} {{ $item['task_id'] }} - {{ $item['status'] }}</strong>
                                <p>{{ $item['notes'] }}</p>
                            </div>
                        @empty
                            <div class="history-item"><strong>لا يوجد history بعد.</strong></div>
                        @endforelse
                    </div>
                </article>
            </div>
        </section>

        <section class="section" id="database">
            <div class="section-title">
                <div>
                    <span class="eyebrow">Database & Modules</span>
                    <h2>خريطة جداول البيانات والـ Modules.</h2>
                </div>
                <p>تُقرأ الأعداد من قاعدة البيانات الحالية. هذا القسم مخصص للأدمن لمعرفة أين توجد البيانات وأي موجات فعالة.</p>
            </div>
            <div class="stat-grid" style="margin-bottom:16px;">
                @foreach ($inventory as $item)
                    <div class="stat">
                        <span>{{ $item['label'] }}</span>
                        <strong>{{ $fmt($item['value']) }}</strong>
                        <small>{{ $item['note'] }}</small>
                    </div>
                @endforeach
            </div>
            <div class="grid-3">
                @foreach ($groups as $group)
                    <article class="card data-group">
                        <div class="group-head"><strong>{{ $group['title'] }}</strong><span>{{ $fmt($group['total']) }} سجل</span></div>
                        <div class="table-grid">
                            @foreach ($group['items'] as $item)
                                <div class="table-chip"><span>{{ $item['label'] }}</span><b>{{ $fmt($item['count']) }}</b></div>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section" id="public">
            <div class="section-title">
                <div>
                    <span class="eyebrow">قسم المستخدم الخارجي</span>
                    <h2>ماذا يفهم العميل والجمهور من المنصة؟</h2>
                </div>
                <p>اللوحة تشرح المسارات الأساسية: صاحب العمل، المؤسسة، المطور، الشريك، وMall visitor، مع فصل واضح بين Marketplace وMall.</p>
            </div>
            <div class="grid-4">
                @foreach ($audiences as $audience)
                    <article class="card">
                        <span class="tag">{{ $audience['tag'] }}</span>
                        <h3>{{ $audience['title'] }}</h3>
                        <p>{{ $audience['value'] }}</p>
                    </article>
                @endforeach
            </div>

            <div class="grid-2" style="margin-top:16px;">
                <article class="card">
                    <h3>مسار Onboarding</h3>
                    <div class="grid-2">
                        @foreach ($onboarding as $step)
                            <div class="card step" data-step="{{ $step['n'] }}" style="box-shadow:none;">
                                <small>خطوة {{ $step['n'] }}</small>
                                <h3>{{ $step['title'] }}</h3>
                                <p>{{ $step['text'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </article>
                <article class="card dark">
                    <h3>السطح العام والعقود</h3>
                    <p>الروابط العامة وAPI contracts التي تساعدنا نراجع تجربة العميل والواجهات الخارجية.</p>
                    <div class="route-list">
                        @foreach ($surface as $route)
                            <a href="{{ $route['url'] }}"><span>{{ $route['name'] }}</span><small>{{ $fmt($route['count']) }} {{ $route['label'] }}</small></a>
                        @endforeach
                    </div>
                </article>
            </div>
        </section>

        <section class="section" id="plans">
            <div class="section-title">
                <div>
                    <span class="eyebrow">Freemium & Entitlements</span>
                    <h2>الخطط والاشتراكات وحدود الاستخدام.</h2>
                </div>
                <p>تُقرأ من جدول plans إذا كان seeded، أو من FreemiumDefaults. هذا يوضح كيف تتحكم المنصة في التوسع التجاري.</p>
            </div>
            <div class="grid-4">
                @foreach ($plans as $plan)
                    <article class="card {{ $plan['code'] === 'business' ? 'dark' : '' }}">
                        <span class="tag">{{ $plan['tier'] }}</span>
                        <h3>{{ $plan['name'] }}</h3>
                        <div class="price">
                            @if ($plan['price'] > 0)
                                {{ number_format($plan['price'] / 100) }} <small>{{ $plan['currency'] }} / {{ $plan['interval'] }}</small>
                            @else
                                {{ $plan['code'] === 'enterprise' ? 'Custom' : 'Free' }}
                            @endif
                        </div>
                        <p>{{ $fmt($plan['entitlements']) }} Entitlements مفعلة لهذه الخطة.</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section grid-2" id="developers">
            <article class="card dark">
                <span class="tag">Themes, Plugins, Marketplace</span>
                <h3>مسار المطورين والـ Marketplace.</h3>
                <p>المطور يبني theme أو plugin أو connector، يقدمه للمراجعة، ثم ينشره أو يبيعه داخل اقتصاد المنصة.</p>
                <div class="grid-2" style="margin-top:14px;">
                    @foreach ($developerFlow as $flow)
                        <div class="card" style="box-shadow:none;background:rgba(255,250,240,.10);border-color:rgba(255,250,240,.12);">
                            <h3>{{ $flow['title'] }}</h3>
                            <p>{{ $flow['text'] }}</p>
                        </div>
                    @endforeach
                </div>
            </article>
            <article class="card">
                <span class="tag">Next.js Public Runtime</span>
                <h3>V15 يفتح مسار الواجهة العامة الجديدة.</h3>
                <p>المسار الحالي: {{ $nextRuntime['path'] }}. العقد: {{ $nextRuntime['contract'] }}. الرابط: {{ $nextRuntime['manifest_uri'] }}.</p>
                <div class="chips">
                    <span class="chip">Next.js App Router</span>
                    <span class="chip">React + TypeScript</span>
                    <span class="chip">Tailwind CSS</span>
                    <span class="chip">RTL-first</span>
                    <span class="chip">API-only contract</span>
                    <span class="chip">Marketplace vs Mall separated</span>
                    <span class="chip">V16 customer onboarding</span>
                </div>
                <div class="doc-list">
                    @foreach ($docs as $doc)
                        <div><span>{{ $doc['name'] }}</span><small>{{ $doc['path'] }}</small></div>
                    @endforeach
                </div>
            </article>
        </section>

        <section class="section grid-2" id="verify">
            <article class="card">
                <span class="tag">Owner Verification</span>
                <h3>كيف تعتمد المشروع بعد التحديث؟</h3>
                <div class="check-list">
                    @foreach ($verificationSteps as $step)
                        <div class="check-item">
                            <strong>{{ $step['title'] }}</strong>
                            <small>{{ $step['text'] }}</small>
                        </div>
                    @endforeach
                </div>
            </article>
            <article class="card dark">
                <span class="tag">Deployment Position</span>
                <h3>{{ $release['production_label'] }}</h3>
                <p>اللوحة الآن لا تخفي أي شيء: هي تقول إن الكود والاختبارات والتراكر جاهزين كـ Release Candidate، وتقول أيضًا إن الإنتاج النهائي يحتاج staging وverification وبيئة تشغيل مضبوطة.</p>
                <div class="actions">
                    <a class="button secondary" href="/ui/release-candidate">V14 RC</a>
                    <a class="button secondary" href="/api/public-web/manifest">V15 Manifest</a>
                </div>
            </article>
        </section>

        <section class="final">
            <h2>الداشبورد الآن جاهزة كمركز قرار، لا مجرد صفحة ترحيب.</h2>
            <p>الأدمن يرى حالة النظام والتراكر والبيانات والجاهزية. والجمهور يرى قيمة المنصة ومساراته. والمطور يرى أين يبدأ في الثيمات والبلجنز. الخطوة التالية المنطقية هي فتح الصفحة في المتصفح وعمل manual QA على الروابط الأساسية.</p>
            <div class="actions" style="justify-content:center;">
                <a class="button primary" href="/admin">فتح Filament Admin</a>
                <a class="button secondary" href="#task-tracker">مراجعة V1-V15</a>
                <a class="button ghost" href="/public">معاينة Public Landing</a>
            </div>
        </section>
    </div>
</body>
</html>
