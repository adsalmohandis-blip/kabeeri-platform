@php
    $config = $data['config'];
    $validation = $data['validation'];
    $release = $data['release'];
    $inventory = $data['inventory'];
    $coverage = $data['coverage'];
    $readyClass = fn ($ready) => $ready ? 'ready' : 'pending';
@endphp

<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Localization\KabeeriLocale::direction() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('kabeeri.brand.name') }} V14 UI Release Candidate</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=almarai:400,700,800|ibm-plex-sans-arabic:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root{--ink:#17130d;--soft:#17130d;--paper:#fffaf0;--forest:#17130d;--blue:#17130d;--gold:#c98a2e;--rose:#17130d;--mint:#17130d;--line:rgba(23,19,13,.13);--white-line:rgba(255,250,240,.18);--shadow:0 28px 90px rgba(23,19,13,.16)}
        *{box-sizing:border-box}body{margin:0;min-height:100vh;color:var(--ink);background:radial-gradient(circle at 80% 10%,rgba(23,19,13,.24),transparent 26rem),radial-gradient(circle at 8% 20%,rgba(201,138,46,.32),transparent 26rem),linear-gradient(135deg,#fffaf0 0%,#fffaf0 48%,#17130d 100%);font-family:"IBM Plex Sans Arabic","Almarai",sans-serif}body:before{content:"";position:fixed;inset:0;pointer-events:none;background-image:linear-gradient(rgba(23,19,13,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(23,19,13,.04) 1px,transparent 1px);background-size:36px 36px;mask-image:linear-gradient(to bottom,rgba(0,0,0,.85),transparent 82%)}a{color:inherit;text-decoration:none}.shell{width:min(100% - 32px,1360px);margin:0 auto;padding:18px 0 74px}.topbar{position:sticky;top:14px;z-index:20;display:flex;align-items:center;justify-content:space-between;gap:14px;padding:14px;border:1px solid rgba(255,250,240,.72);border-radius:999px;background:rgba(255,250,240,.76);box-shadow:0 18px 64px rgba(23,19,13,.12);backdrop-filter:blur(22px)}.brand{display:flex;align-items:center;gap:12px}.brand-mark{display:grid;place-items:center;width:48px;height:48px;border-radius:17px;color:var(--paper);background:linear-gradient(135deg,var(--forest),var(--blue));font-weight:900;letter-spacing:-.1em}.brand small{display:block;margin-top:2px;color:var(--soft);font-size:12px}.nav,.actions,.chips{display:flex;flex-wrap:wrap;gap:8px}.nav a,.button,.chip,.pill{display:inline-flex;align-items:center;justify-content:center;border-radius:999px;font-weight:900}.nav a{padding:10px 13px;color:rgba(23,19,13,.72);font-size:13px}.button{min-height:42px;padding:10px 16px;border:1px solid var(--line);background:rgba(255,250,240,.72);color:var(--forest)}.button.primary{border-color:transparent;color:var(--paper);background:linear-gradient(135deg,var(--forest),var(--blue))}.hero,.section,.card{border:1px solid var(--line);background:rgba(255,250,240,.72);box-shadow:0 14px 42px rgba(23,19,13,.08)}.hero{overflow:hidden;margin-top:22px;border-radius:46px;box-shadow:var(--shadow)}.hero-grid{display:grid;grid-template-columns:minmax(0,1.08fr) minmax(330px,.92fr);gap:24px;padding:34px}h1,h2,h3,.brand strong{letter-spacing:-.045em}h1{margin:18px 0 16px;font-size:clamp(30px,4.8vw,54px);line-height:.98}.hero p,.section-title p,.card p,.route-row span{line-height:1.82;color:var(--soft)}.eyebrow,.chip{min-height:30px;padding:6px 11px;border:1px solid rgba(23,19,13,.18);color:var(--forest);background:rgba(255,250,240,.76);font-size:12px}.section{margin-top:22px;padding:30px;border-radius:34px}.section.dark{color:var(--paper);border-color:var(--white-line);background:radial-gradient(circle at top left,rgba(201,138,46,.18),transparent 24rem),linear-gradient(135deg,#17130d,#17130d)}.dark p,.dark span{color:rgba(255,250,240,.72)}.section-title{display:flex;align-items:end;justify-content:space-between;gap:18px;margin-bottom:18px}.section-title h2{margin:8px 0 0;font-size:clamp(22px,2.8vw,32px);line-height:1.08}.grid-2,.grid-3,.grid-4{display:grid;gap:14px}.grid-2{grid-template-columns:repeat(2,minmax(0,1fr))}.grid-3{grid-template-columns:repeat(3,minmax(0,1fr))}.grid-4{grid-template-columns:repeat(4,minmax(0,1fr))}.card{padding:22px;border-radius:26px}.card h3{margin:11px 0 9px;font-size:21px}.metric{margin-top:12px;color:var(--forest);font-size:20px;font-weight:900}.pill{min-height:30px;padding:6px 11px;font-size:12px}.pill.ready{color:#17130d;background:rgba(23,19,13,.13);border:1px solid rgba(23,19,13,.2)}.pill.pending{color:#17130d;background:rgba(23,19,13,.13);border:1px solid rgba(23,19,13,.22)}.route-row{display:grid;grid-template-columns:170px minmax(0,1fr) 110px;gap:12px;align-items:center;padding:12px 14px;border-bottom:1px solid var(--line)}.route-row:last-child{border-bottom:0}.route-list{overflow:hidden;border:1px solid var(--line);border-radius:24px;background:rgba(255,250,240,.6)}code{direction:ltr;display:block;border-radius:16px;background:#17130d;color:#fffaf0;padding:12px 14px}.footer{display:flex;justify-content:space-between;gap:12px;margin-top:26px;padding:24px 4px 0;color:rgba(23,19,13,.62);font-size:13px}
        @media(max-width:1120px){.hero-grid,.grid-2{grid-template-columns:1fr}.grid-3,.grid-4{grid-template-columns:repeat(2,minmax(0,1fr))}.topbar{border-radius:30px;align-items:stretch;flex-direction:column}.brand,.actions,.nav{justify-content:center}}@media(max-width:720px){.shell{width:min(100% - 20px,1360px)}.hero-grid,.section{padding:24px}.section-title,.footer,.actions{align-items:stretch;flex-direction:column}.grid-3,.grid-4,.route-row{grid-template-columns:1fr}.nav a,.button{width:100%}}
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <a class="brand" href="{{ route('ui.release-candidate') }}">
                <span class="brand-mark">Kb</span>
                <span>
                    <strong>{{ __('kabeeri.brand.name') }} V14</strong>
                    <small>UI Release Candidate Center</small>
                </span>
            </a>

            <nav class="nav" aria-label="V14 navigation">
                <a href="{{ route('home') }}">Public Home</a>
                <a href="{{ route('system.command-center') }}">Command Center</a>
                <a href="{{ route('public.landing') }}">Public</a>
                <a href="{{ route('marketplace.home') }}">Marketplace</a>
                <a href="{{ route('mall.index') }}">Mall</a>
                <a href="/admin">Admin</a>
            </nav>

            <div class="actions">
                <a class="button primary" href="{{ route('mall.trust') }}">Trust QA</a>
                <a class="button" href="{{ route('developers.qa') }}">Theme/Plugin QA</a>
                @include('components.language-switcher', ['context' => 'admin'])
            </div>
        </header>

        <main>
            <section class="hero">
                <div class="hero-grid">
                    <div>
                        <span class="eyebrow">{{ __('kabeeri.brand.name') }} V14 UI Release Candidate</span>
                        <h1>Quality gate before the UI can be treated as release-ready.</h1>
                        <p>{{ $config['rules']['scope'] }} This report verifies route inventory, accessibility coverage, responsive coverage, security and permission UI, design-system coverage, Next.js separation, and Mall/Marketplace separation.</p>
                        <div class="actions" style="margin-top: 24px;">
                            <a class="button primary" href="#gates">Go / No-Go Gates</a>
                            <a class="button" href="#routes">Route Inventory</a>
                            <a class="button" href="#commands">Verification Commands</a>
                        </div>
                    </div>
                    <aside class="card">
                        <span class="chip">Release signal</span>
                        <h3>{{ $release['ready'] ? 'Ready' : 'Needs review' }}</h3>
                        <div class="metric">{{ $validation['route_count'] }}</div>
                        <p>configured UI routes checked. Missing: {{ count($validation['missing_routes']) }}.</p>
                        <span class="pill {{ $readyClass($validation['task_tracker_synced']) }}">Task tracker {{ $validation['task_tracker_synced'] ? 'synced' : 'pending' }}</span>
                    </aside>
                </div>
            </section>

            <section class="section dark" id="gates">
                <div class="section-title">
                    <div>
                        <span class="chip">Go / No-Go</span>
                        <h2>Every major V9-V14 quality gate in one place.</h2>
                    </div>
                    <p>No single beautiful page makes the project ready. V14 checks system-wide UX truth.</p>
                </div>

                <div class="grid-3">
                    @foreach ($release['gates'] as $gate)
                        <article class="card">
                            <span class="pill {{ $readyClass($gate['ready']) }}">{{ $gate['ready'] ? 'Ready' : 'Pending' }}</span>
                            <h3>{{ $gate['label'] }}</h3>
                            <p>{{ $gate['key'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="section">
                <div class="section-title">
                    <div>
                        <span class="chip">Previous Versions</span>
                        <h2>V9-V13 must stay green.</h2>
                    </div>
                    <p>V14 is not allowed to hide regressions from earlier UI versions.</p>
                </div>

                <div class="grid-4">
                    @foreach ($validation['previous_versions'] as $version => $ready)
                        <article class="card">
                            <span class="pill {{ $readyClass($ready) }}">{{ $ready ? 'Ready' : 'Pending' }}</span>
                            <h3>{{ strtoupper($version) }}</h3>
                            <p>{{ $ready ? 'Release candidate readiness is true.' : 'Needs review before release.' }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="section" id="routes">
                <div class="section-title">
                    <div>
                        <span class="chip">Full UI Route Inventory Verification</span>
                        <h2>Configured routes are checked by group.</h2>
                    </div>
                    <p>Route inventory covers admin, public marketing, marketplace, developers, Mall, customer, partner, network, and this V14 report.</p>
                </div>

                <div class="grid-3">
                    @foreach ($inventory['groups'] as $group => $stats)
                        <article class="card">
                            <span class="chip">{{ $group }}</span>
                            <div class="metric">{{ $stats['count'] }}</div>
                            <p>{{ $stats['missing'] }} missing routes</p>
                        </article>
                    @endforeach
                </div>

                <div class="route-list" style="margin-top: 16px;">
                    @foreach ($inventory['routes'] as $route)
                        <div class="route-row">
                            <strong>{{ $route['group'] }}</strong>
                            <span>{{ $route['label'] }} · {{ $route['route'] }}</span>
                            <span class="pill {{ $readyClass($route['exists']) }}">{{ $route['exists'] ? 'Ready' : 'Missing' }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="section">
                <div class="section-title">
                    <div>
                        <span class="chip">Quality Coverage</span>
                        <h2>Accessibility, responsive, permission, design, QA, separation.</h2>
                    </div>
                    <p>These are checklist-backed gates. Some still require human visual QA before production, but the contract is documented and testable.</p>
                </div>

                <div class="grid-3">
                    @foreach ($coverage as $key => $gate)
                        <article class="card">
                            <span class="pill {{ $readyClass($gate['ready']) }}">{{ $gate['ready'] ? 'Ready' : 'Pending' }}</span>
                            <h3>{{ str($key)->replace('_', ' ')->title() }}</h3>
                            <p>{{ $gate['note'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="section dark" id="commands">
                <div class="section-title">
                    <div>
                        <span class="chip">Full UI Smoke Test Suite</span>
                        <h2>Commands required before merge or deployment.</h2>
                    </div>
                    <p>The final command is the whole project regression. It is intentionally not optional for V14.</p>
                </div>

                <div class="grid-2">
                    @foreach ($release['commands'] as $command)
                        <code>{{ $command }}</code>
                    @endforeach
                </div>
            </section>
        </main>

        <footer class="footer">
            <span>{{ __('kabeeri.brand.name') }} V14 UI Quality RC</span>
            <span>{{ $config['rules']['runtime'] }}</span>
        </footer>
    </div>
</body>
</html>
