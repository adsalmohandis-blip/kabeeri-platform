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
    $filamentResources = collect($inventory)->firstWhere('label', 'Filament Resources')['value'] ?? 0;
    $fmt = fn ($value) => number_format((int) $value);
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KABEERI | لوحة بيانات المنصة الكاملة</title>
    <meta name="description" content="لوحة KABEERI الرئيسية تعرض حالة النظام، التاسكات، قاعدة البيانات، المسارات العامة، الخطط، والثيمات والبلجنز للمطورين والعملاء.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=almarai:400,700,800|ibm-plex-sans-arabic:400,500,600,700" rel="stylesheet" />
    <style>
        :root{
            --ink:#17130d;--muted:#756a5e;--paper:#fffaf0;--panel:rgba(255,250,240,.78);--panel2:rgba(255,255,255,.52);--line:rgba(23,19,13,.13);
            --night:#20170f;--green:#3f5e47;--green2:#78956f;--gold:#c98a2e;--gold2:#f3c45f;--clay:#9a5539;--red:#9b3f30;--blue:#2f5f73;
            --shadow:0 30px 90px rgba(69,43,16,.18);--soft:0 18px 50px rgba(69,43,16,.10);--radius:32px;
        }
        *{box-sizing:border-box}html{scroll-behavior:smooth}body{margin:0;min-height:100vh;color:var(--ink);font-family:"IBM Plex Sans Arabic","Almarai",sans-serif;background:radial-gradient(circle at 8% 8%,rgba(120,149,111,.55),transparent 24rem),radial-gradient(circle at 91% 9%,rgba(243,196,95,.58),transparent 26rem),radial-gradient(circle at 72% 92%,rgba(154,85,57,.24),transparent 32rem),linear-gradient(135deg,#fffaf0 0%,#f2ddb8 51%,#d8ad69 100%);background-attachment:fixed}
        body:before{content:"";position:fixed;inset:0;z-index:-1;opacity:.18;background-image:linear-gradient(rgba(23,19,13,.07) 1px,transparent 1px),linear-gradient(90deg,rgba(23,19,13,.06) 1px,transparent 1px);background-size:42px 42px;mask-image:linear-gradient(to bottom,#000,transparent 85%)}a{color:inherit;text-decoration:none}.shell{width:min(1400px,calc(100% - 34px));margin:0 auto;padding:20px 0 62px}
        .topbar{position:sticky;top:12px;z-index:20;display:flex;align-items:center;justify-content:space-between;gap:16px;padding:12px;border:1px solid var(--line);border-radius:28px;background:rgba(255,250,240,.72);backdrop-filter:blur(18px);box-shadow:0 14px 42px rgba(69,43,16,.08)}.brand{display:flex;align-items:center;gap:12px}.mark{width:56px;height:56px;display:grid;place-items:center;border-radius:20px;color:var(--paper);background:conic-gradient(from 210deg,#20170f,#86502f,#d7983f,#3f5e47,#20170f);font-family:Almarai,sans-serif;font-weight:800;font-size:22px}.brand strong{display:block;font-family:Almarai,sans-serif;font-size:18px}.brand span{display:block;color:var(--muted);font-size:13px}.topnav{display:flex;flex-wrap:wrap;gap:8px;justify-content:flex-end}.nav-link{display:inline-flex;align-items:center;justify-content:center;min-height:40px;padding:0 13px;border:1px solid rgba(23,19,13,.11);border-radius:999px;background:rgba(255,255,255,.45);font-size:13px;font-weight:800}.nav-link:hover{background:var(--paper);transform:translateY(-1px)}
        .hero{position:relative;overflow:hidden;margin-top:20px;border:1px solid var(--line);border-radius:46px;background:linear-gradient(145deg,rgba(255,250,240,.92),rgba(255,239,205,.74));box-shadow:var(--shadow)}.hero:before{content:"";position:absolute;width:430px;height:430px;inset-inline-start:-160px;top:-170px;border-radius:999px;background:repeating-linear-gradient(45deg,rgba(63,94,71,.16) 0 11px,transparent 11px 25px);animation:floatA 19s ease-in-out infinite alternate}.hero-grid{position:relative;display:grid;grid-template-columns:minmax(0,1.08fr) minmax(390px,.92fr);gap:24px;padding:clamp(26px,5vw,64px)}.kicker{display:inline-flex;align-items:center;gap:10px;width:fit-content;padding:10px 14px;border:1px solid rgba(23,19,13,.13);border-radius:999px;background:rgba(255,255,255,.55);color:#713924;font-weight:800;font-size:13px}.dot{width:10px;height:10px;border-radius:999px;background:#3f8f45;box-shadow:0 0 0 7px rgba(63,143,69,.14)}
        h1{max-width:920px;margin:22px 0 0;font-family:Almarai,sans-serif;font-size:clamp(42px,7vw,92px);line-height:.99;letter-spacing:-2.2px}.lead{max-width:840px;margin:22px 0 0;color:var(--muted);font-size:clamp(17px,1.75vw,22px);line-height:1.95}.actions{display:flex;flex-wrap:wrap;gap:12px;margin-top:30px}.button{display:inline-flex;align-items:center;justify-content:center;min-height:52px;padding:0 22px;border:1px solid rgba(23,19,13,.15);border-radius:18px;font-weight:800;transition:transform .18s ease,box-shadow .18s ease}.button:hover{transform:translateY(-2px);box-shadow:0 16px 36px rgba(69,43,16,.14)}.primary{color:var(--paper);background:var(--night)}.secondary{background:rgba(255,255,255,.60)}.ghost{background:rgba(63,94,71,.12);color:var(--green)}
        .command-card{padding:24px;border-radius:32px;color:var(--paper);background:linear-gradient(160deg,rgba(32,23,15,.97),rgba(72,55,32,.94) 58%,rgba(63,94,71,.92));box-shadow:0 24px 64px rgba(32,23,15,.22)}.command-card h2{margin:0;font-family:Almarai,sans-serif;font-size:30px}.command-card p{margin:10px 0 18px;color:rgba(255,250,240,.75);line-height:1.8}.hero-metrics{display:grid;grid-template-columns:repeat(2,1fr);gap:10px}.hero-metric{padding:17px;border-radius:22px;background:rgba(255,250,240,.09);border:1px solid rgba(255,250,240,.13)}.hero-metric span{display:block;color:rgba(255,250,240,.66);font-size:12px;font-weight:800;text-transform:uppercase}.hero-metric strong{display:block;margin-top:7px;font-family:Almarai,sans-serif;font-size:34px}.hero-metric small{color:rgba(255,250,240,.70)}
        .section{margin-top:38px}.section-title{display:flex;justify-content:space-between;align-items:end;gap:18px;margin-bottom:16px}.section-title h2{margin:0;font-family:Almarai,sans-serif;font-size:clamp(30px,3.3vw,48px);letter-spacing:-.9px}.section-title p{max-width:700px;margin:0;color:var(--muted);line-height:1.85}.eyebrow{display:inline-flex;margin-bottom:12px;padding:8px 12px;border-radius:999px;background:rgba(154,85,57,.12);color:#713924;font-size:13px;font-weight:800}.grid-2{display:grid;grid-template-columns:repeat(2,1fr);gap:16px}.grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}.grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}.card{border:1px solid var(--line);border-radius:var(--radius);background:var(--panel);box-shadow:var(--soft);padding:24px}.card h3{margin:0 0 10px;font-family:Almarai,sans-serif;font-size:25px}.card p{margin:0;color:var(--muted);line-height:1.8}.tag{display:inline-flex;margin-bottom:14px;padding:8px 11px;border-radius:999px;background:rgba(63,94,71,.12);color:var(--green);font-size:12px;font-weight:800}.dark{color:var(--paper);background:linear-gradient(145deg,#20170f,#3f5e47)}.dark p,.dark li,.dark small{color:rgba(255,250,240,.76)}
        .stat-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:12px}.stat{padding:20px;border:1px solid var(--line);border-radius:26px;background:rgba(255,250,240,.68);box-shadow:var(--soft)}.stat span{display:block;color:var(--muted);font-size:12px;font-weight:800}.stat strong{display:block;margin-top:8px;font-family:Almarai,sans-serif;font-size:30px}.stat small{display:block;margin-top:4px;color:var(--muted)}.stat.good strong{color:var(--green)}.stat.warn strong{color:var(--clay)}.stat.danger strong{color:var(--red)}
        .version-list{display:grid;gap:10px}.version-row{display:grid;grid-template-columns:82px 1fr 92px 110px;gap:12px;align-items:center;padding:13px 14px;border-radius:18px;background:rgba(255,255,255,.48);border:1px solid rgba(23,19,13,.07)}.version-row strong{font-family:Almarai,sans-serif}.version-row small{color:var(--muted)}.bar{height:10px;border-radius:999px;background:rgba(23,19,13,.09);overflow:hidden}.bar span{display:block;height:100%;border-radius:inherit;background:linear-gradient(90deg,var(--green),var(--gold2))}.pill{display:inline-flex;justify-content:center;align-items:center;min-height:32px;padding:0 10px;border-radius:999px;background:rgba(63,94,71,.12);color:var(--green);font-weight:800;font-size:12px}.pill.pending{background:rgba(154,85,57,.13);color:#713924}.pill.blocked{background:rgba(155,63,48,.13);color:var(--red)}
        .data-group{display:flex;flex-direction:column;gap:12px}.group-head{display:flex;justify-content:space-between;gap:12px;align-items:center}.group-head strong{font-family:Almarai,sans-serif;font-size:22px}.group-head span{color:var(--muted)}.table-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:8px}.table-chip{display:flex;justify-content:space-between;gap:10px;padding:10px 11px;border-radius:14px;background:rgba(255,255,255,.48);font-size:13px}.table-chip b{font-family:Almarai,sans-serif}.route-list,.history-list,.doc-list{display:grid;gap:10px;margin-top:14px}.route-list a,.doc-list div,.history-item{display:flex;justify-content:space-between;gap:14px;align-items:center;padding:12px 14px;border-radius:16px;background:rgba(255,255,255,.48);border:1px solid rgba(23,19,13,.07);font-weight:800}.history-item{display:block;font-weight:500}.history-item strong{font-weight:800}.history-item p{margin:6px 0 0;font-size:13px}.dark .route-list a{background:rgba(255,250,240,.10);border-color:rgba(255,250,240,.12)}
        .plan{position:relative;overflow:hidden}.plan:after{content:attr(data-code);position:absolute;inset-inline-end:18px;bottom:-6px;color:rgba(23,19,13,.05);font-family:Almarai,sans-serif;font-size:72px;font-weight:800;text-transform:uppercase}.price{font-family:Almarai,sans-serif;font-size:32px;margin:12px 0}.price small{font-size:13px;color:var(--muted)}.dark .price small{color:rgba(255,250,240,.7)}.step{position:relative;overflow:hidden;min-height:220px}.step:after{content:attr(data-step);position:absolute;inset-inline-end:18px;bottom:6px;color:rgba(23,19,13,.06);font-family:Almarai,sans-serif;font-size:88px;font-weight:800}.step small{color:var(--clay);font-weight:800}.chips{display:flex;flex-wrap:wrap;gap:8px;margin-top:14px}.chip{padding:8px 10px;border-radius:999px;background:rgba(255,255,255,.55);font-size:12px;font-weight:800}.next-box{margin-top:10px;padding:12px;border-radius:16px;background:rgba(154,85,57,.09);color:#713924;font-size:13px;line-height:1.7}.final{margin-top:38px;padding:clamp(26px,5vw,50px);border-radius:40px;border:1px solid var(--line);background:linear-gradient(145deg,rgba(255,250,240,.9),rgba(255,239,205,.78));box-shadow:var(--shadow);text-align:center}.final h2{margin:0;font-family:Almarai,sans-serif;font-size:clamp(32px,5vw,62px);line-height:1.14}.final p{max-width:850px;margin:18px auto 26px;color:var(--muted);line-height:1.9;font-size:18px}
        @keyframes floatA{from{transform:translate3d(0,0,0) rotate(0)}to{transform:translate3d(42px,30px,0) rotate(10deg)}}@media(max-width:1180px){.hero-grid,.grid-2{grid-template-columns:1fr}.stat-grid{grid-template-columns:repeat(3,1fr)}.grid-3,.grid-4{grid-template-columns:repeat(2,1fr)}}@media(max-width:760px){.shell{width:min(100% - 22px,1400px);padding-top:12px}.topbar{position:static;flex-direction:column;align-items:stretch;border-radius:22px}.topnav{justify-content:stretch}.nav-link{flex:1}.hero{border-radius:30px}.hero-grid{grid-template-columns:1fr;padding:24px}h1{letter-spacing:-1.1px}.hero-metrics,.stat-grid,.grid-3,.grid-4,.table-grid{grid-template-columns:1fr}.section-title{flex-direction:column;align-items:start}.version-row{grid-template-columns:64px 1fr}.version-row .pill,.version-row small{text-align:right;justify-content:flex-start}.route-list a,.doc-list div{align-items:flex-start;flex-direction:column}}
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <a class="brand" href="/">
                <div class="mark">K</div>
                <div>
                    <strong>KABEERI Command Center</strong>
                    <span>لوحة بيانات حية للنظام، الجمهور، المطورين، الثيمات، البلجنز، والتاسكات</span>
                </div>
            </a>
            <nav class="topnav" aria-label="روابط لوحة البيانات">
                <a class="nav-link" href="#admin">الأدمن</a>
                <a class="nav-link" href="#task-tracker">التاسكات</a>
                <a class="nav-link" href="#database">قاعدة البيانات</a>
                <a class="nav-link" href="#public">الجمهور</a>
                <a class="nav-link" href="#plans">الخطط</a>
                <a class="nav-link" href="#developers">المطورين</a>
                <a class="nav-link" href="/admin">Filament</a>
            </nav>
        </header>

        <main class="hero">
            <div class="hero-grid">
                <section>
                    <div class="kicker"><span class="dot"></span> تم تحديث اللوحة بكل البيانات المتاحة من المشروع</div>
                    <h1>لوحة كابيري الرئيسية: صورة واحدة صادقة للنظام كله.</h1>
                    <p class="lead">هذه الصفحة تجمع حالة الباك إند، التاسكات، جداول البيانات، موارد Filament، الخطط، المسارات العامة، ووثائق التطوير في واجهة واحدة تفهمك أين نحن الآن وما الذي تبقى قبل بناء UI الإصدارات القادمة.</p>
                    <div class="actions">
                        <a class="button primary" href="#task-tracker">راجع حالة التنفيذ</a>
                        <a class="button secondary" href="#database">افتح خريطة البيانات</a>
                        <a class="button ghost" href="/admin">ادخل لوحة الإدارة</a>
                    </div>
                </section>

                <aside class="command-card">
                    <h2>حالة النظام المختصرة</h2>
                    <p>الأرقام التالية تُقرأ من ملفات التاسك تراكر والكود والجداول المتاحة، وليست أرقامًا مكتوبة يدويًا داخل الصفحة.</p>
                    <div class="hero-metrics">
                        <div class="hero-metric"><span>كل التاسكات</span><strong>{{ $summary['percent'] }}%</strong><small>{{ $fmt($summary['done']) }} / {{ $fmt($summary['total']) }}</small></div>
                        <div class="hero-metric"><span>الباك إند والامتدادات</span><strong>{{ $backend['percent'] }}%</strong><small>{{ $fmt($backend['pending']) }} pending</small></div>
                        <div class="hero-metric"><span>UI Roadmap</span><strong>{{ $ui['percent'] }}%</strong><small>{{ $fmt($ui['pending']) }} pending</small></div>
                        <div class="hero-metric"><span>Filament Resources</span><strong>{{ $fmt($filamentResources) }}</strong><small>شاشة إدارة</small></div>
                    </div>
                </aside>
            </div>
        </main>

        <section class="section" id="admin">
            <div class="section-title">
                <div>
                    <span class="eyebrow">قسم الأدمن الداخلي</span>
                    <h2>فحص سريع قبل أي نشر أو عرض للعميل.</h2>
                </div>
                <p>هذا الجزء مخصص لك كمطور/مالك النظام: أين وصل التنفيذ؟ هل الداتا موجودة؟ هل UI القادم واضح؟ وما الروابط التي تحتاجها للتحقق السريع؟</p>
            </div>
            <div class="stat-grid">
                <div class="stat good"><span>Codex Done</span><strong>{{ $fmt($summary['codex_done']) }}</strong><small>تاسك منفذ بواسطة Codex</small></div>
                <div class="stat"><span>Verified</span><strong>{{ $fmt($summary['verified']) }}</strong><small>تأكيد المالك</small></div>
                <div class="stat warn"><span>Pending</span><strong>{{ $fmt($summary['pending']) }}</strong><small>معظمها UI V9-V14</small></div>
                <div class="stat"><span>In Progress</span><strong>{{ $fmt($summary['in_progress']) }}</strong><small>قيد التنفيذ الآن</small></div>
                <div class="stat danger"><span>Blocked</span><strong>{{ $fmt($summary['blocked']) }}</strong><small>معطلة</small></div>
                <div class="stat good"><span>Backend Ready</span><strong>{{ $backend['percent'] }}%</strong><small>V1-V8 + Freemium + EXT</small></div>
            </div>

            <div class="grid-2" style="margin-top:16px;">
                <article class="card dark">
                    <span class="tag">Quick Access</span>
                    <h3>روابط الأدمن والتشغيل</h3>
                    <p>مسارات مباشرة لأهم أماكن الفحص والانتقال داخل المشروع.</p>
                    <div class="route-list">
                        @foreach ($adminRoutes as $route)
                            <a href="{{ $route['url'] }}"><span>{{ $route['name'] }}</span><small>{{ $route['desc'] }}</small></a>
                        @endforeach
                    </div>
                </article>
                <article class="card">
                    <span class="tag">Code Inventory</span>
                    <h3>مخزون الكود والوثائق</h3>
                    <div class="table-grid">
                        @foreach ($inventory as $item)
                            <div class="table-chip"><span>{{ $item['label'] }}</span><b>{{ $fmt($item['value']) }}</b></div>
                        @endforeach
                    </div>
                    <div class="doc-list">
                        @foreach ($docs as $doc)
                            <div><span>{{ $doc['name'] }}</span><small>{{ $doc['path'] }}</small></div>
                        @endforeach
                    </div>
                </article>
            </div>
        </section>

        <section class="section" id="task-tracker">
            <div class="section-title">
                <div>
                    <span class="eyebrow">Task Tracker Truth</span>
                    <h2>كل الإصدارات والتاسكات في مكان واحد.</h2>
                </div>
                <p>V1 إلى V8 مع Freemium وEXT_UPDATE مكتملين كـ Codex Done. V9 إلى V14 هي خريطة UI/UX القادمة وما زالت pending.</p>
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
                    <h3>آخر تحديثات التراكر</h3>
                    <p>آخر أحداث task_history.jsonl بعد تنفيذ الباك إند الناقص.</p>
                    <div class="history-list">
                        @forelse ($history as $item)
                            <div class="history-item">
                                <strong>{{ $item['version'] }} {{ $item['task_id'] }} · {{ $item['status'] }}</strong>
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
                    <h2>خريطة جداول البيانات حسب مجال المنصة.</h2>
                </div>
                <p>الأعداد هنا من قاعدة البيانات الحالية. لو شغلت seed أو دخلت بيانات جديدة، الأرقام ستتغير مباشرة في الصفحة.</p>
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
                    <h2>ما الذي يفهمه العميل من المنصة؟</h2>
                </div>
                <p>المنصة ليست مجرد موقع. هي مسارات لأصحاب الأعمال، المؤسسات، المطورين، والمسوقين مع Mall وMarketplace وFreemium وامتدادات قابلة للبيع.</p>
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
                    <h3>السطح العام وروابط المعاينة</h3>
                    <p>هذه هي الواجهات والـ APIs العامة التي يستطيع العميل أو التطبيق الخارجي رؤيتها.</p>
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
                    <h2>الخطط والحدود التي تحكم النمو.</h2>
                </div>
                <p>هذه البيانات تأتي من جدول plans إن كان seeded، أو من defaults الرسمية في الكود. كل خطة مرتبطة بعدد entitlements قابل للفحص والتنفيذ.</p>
            </div>
            <div class="grid-4">
                @foreach ($plans as $plan)
                    <article class="card plan {{ $plan['code'] === 'business' ? 'dark' : '' }}" data-code="{{ $plan['code'] }}">
                        <span class="tag">{{ $plan['tier'] }}</span>
                        <h3>{{ $plan['name'] }}</h3>
                        <div class="price">
                            @if ($plan['price'] > 0)
                                {{ number_format($plan['price'] / 100) }} <small>{{ $plan['currency'] }} / {{ $plan['interval'] }}</small>
                            @else
                                {{ $plan['code'] === 'enterprise' ? 'Custom' : 'Free' }}
                            @endif
                        </div>
                        <p>{{ $fmt($plan['entitlements']) }} entitlement مفعل لهذه الخطة.</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section grid-2" id="developers">
            <article class="card dark">
                <span class="tag">Themes, Plugins, Marketplace</span>
                <h3>مسار المطورين داخل كابيري.</h3>
                <p>المطور يبني ثيم أو بلجن أو Connector، يقدمه للمراجعة، ثم يبيعه أو يستخدمه كخدمة داخل اقتصاد المنصة.</p>
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
                <span class="tag">What Remains</span>
                <h3>نقطة البدء القادمة واضحة.</h3>
                <p>بعد اكتمال الباك إند الناقص، الخطوة المنطقية هي بدء V9 ثم V10-V14 لبناء UI كامل ومنظم بدل صفحات منفصلة. الداشبورد الآن يعرض هذا بوضوح: الباك إند جاهز بدرجة عالية، وواجهة المستخدم القادمة ما زالت تنتظر التنفيذ.</p>
                <div class="chips">
                    <span class="chip">V9 UI Foundation</span>
                    <span class="chip">V10 Admin Dashboards</span>
                    <span class="chip">V11 Public UI</span>
                    <span class="chip">V12 Marketplace</span>
                    <span class="chip">V13 Portals</span>
                    <span class="chip">V14 QA</span>
                </div>
            </article>
        </section>

        <section class="final">
            <h2>الداشبورد الآن لا يبيع الوهم: يعرض المنصة كما هي فعلا.</h2>
            <p>إذا أردنا نشر المنصة للعميل، فالصفحة تشرح القيمة. وإذا أردنا العمل كفريق تطوير، فهي تكشف حالة التنفيذ والبيانات والتاسكات التالية. كابيري أصبحت عندها بوصلة واحدة: ماذا يعمل الآن، وما الذي سنبنيه بعده.</p>
            <div class="actions" style="justify-content:center;">
                <a class="button primary" href="/admin">فتح لوحة الإدارة</a>
                <a class="button secondary" href="#task-tracker">مراجعة V9-V14</a>
                <a class="button ghost" href="/mall">معاينة المول</a>
            </div>
        </section>
    </div>
</body>
</html>
