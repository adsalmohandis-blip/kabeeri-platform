@php
    $v13 = $v13 ?? \App\Support\Ui\V13ExternalExperience::mallData();
    $pageTitle = $pageTitle ?? __('kabeeri.brand.name').' Mall';
@endphp

<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Localization\KabeeriLocale::direction() }}" data-kbr-theme="{{ $kabeeriUi['theme'] ?? 'light' }}" data-kbr-font="{{ $kabeeriUi['font'] ?? 'ibm-plex-sans-arabic' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=almarai:400,700,800|ibm-plex-sans-arabic:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root{--ink:#000000;--soft:#000000;--paper:#f0f0f0;--forest:#000000;--sage:#000000;--sky:#000000;--clay:#000000;--wheat:#000000;--line:rgba(0,0,0,.13);--white-line:rgba(240,240,240,.18);--shadow:0 26px 84px rgba(0,0,0,.16)}
        *{box-sizing:border-box}body{margin:0;min-height:100vh;color:var(--ink);background:radial-gradient(circle at 82% 10%,rgba(0,0,0,.74),transparent 27rem),radial-gradient(circle at 7% 20%,rgba(0,0,0,.36),transparent 24rem),linear-gradient(135deg,#f0f0f0 0%,#f0f0f0 48%,#000000 100%);font-family:"IBM Plex Sans Arabic","Almarai",sans-serif}body:before{content:"";position:fixed;inset:0;pointer-events:none;background-image:radial-gradient(rgba(0,0,0,.06) 1px,transparent 1px);background-size:28px 28px;mask-image:linear-gradient(to bottom,rgba(0,0,0,.84),transparent 82%)}a{color:inherit;text-decoration:none}.shell{width:min(100% - 32px,1320px);margin:0 auto;padding:18px 0 72px}.topbar{position:sticky;top:14px;z-index:20;display:flex;align-items:center;justify-content:space-between;gap:14px;padding:14px;border:1px solid rgba(240,240,240,.72);border-radius:999px;background:rgba(240,240,240,.74);box-shadow:0 18px 64px rgba(0,0,0,.12);backdrop-filter:blur(22px)}.brand{display:flex;align-items:center;gap:12px;min-width:210px}.brand-mark{display:grid;place-items:center;width:48px;height:48px;border-radius:17px;color:var(--paper);background:linear-gradient(135deg,var(--forest),var(--sage));font-weight:900;letter-spacing:-.1em}.brand small{display:block;margin-top:2px;color:var(--soft);font-size:12px}.nav,.actions,.chips{display:flex;flex-wrap:wrap;gap:8px}.nav{justify-content:center}.nav a,.chip,.button{display:inline-flex;align-items:center;justify-content:center;border-radius:999px;font-weight:900}.nav a{padding:10px 13px;color:rgba(0,0,0,.72);font-size:13px}.nav a:hover{background:rgba(240,240,240,.9);color:var(--forest)}.nav a.active{background:#000000;color:#f0f0f0}.button{min-height:42px;padding:10px 16px;border:1px solid var(--line);background:rgba(240,240,240,.7);color:var(--forest);transition:.2s ease}.button:hover{transform:translateY(-2px);box-shadow:0 16px 34px rgba(0,0,0,.13)}.button.primary{border-color:transparent;color:var(--paper);background:linear-gradient(135deg,var(--forest),#000000)}.hero,.section,.card{border:1px solid var(--line);background:rgba(240,240,240,.72);box-shadow:0 14px 40px rgba(0,0,0,.08)}.hero{overflow:hidden;margin-top:22px;border-radius:44px;box-shadow:var(--shadow)}.hero-grid{display:grid;grid-template-columns:minmax(0,1.08fr) minmax(320px,.92fr);gap:24px;padding:32px}.eyebrow,.chip{min-height:30px;padding:6px 11px;border:1px solid rgba(0,0,0,.18);color:var(--forest);background:rgba(240,240,240,.76);font-size:12px}h1,h2,h3,.brand strong{letter-spacing:-.045em}h1{margin:18px 0 16px;font-size:clamp(30px,4.8vw,52px);line-height:.98}.hero p,.section-title p,.card p,.card li,dd{line-height:1.82;color:var(--soft)}.section{margin-top:22px;padding:30px;border-radius:34px}.section.dark{color:var(--paper);border-color:var(--white-line);background:radial-gradient(circle at top left,rgba(0,0,0,.18),transparent 24rem),linear-gradient(135deg,#000000,#000000)}.dark .card{border-color:var(--white-line);background:rgba(240,240,240,.08);box-shadow:none}.dark p,.dark dd{color:rgba(240,240,240,.72)}.section-title{display:flex;align-items:end;justify-content:space-between;gap:18px;margin-bottom:18px}.section-title h2{margin:8px 0 0;font-size:clamp(22px,2.8vw,32px);line-height:1.08}.grid-2,.grid-3,.grid-4{display:grid;gap:14px}.grid-2{grid-template-columns:repeat(2,minmax(0,1fr))}.grid-3{grid-template-columns:repeat(3,minmax(0,1fr))}.grid-4{grid-template-columns:repeat(4,minmax(0,1fr))}.card{padding:22px;border-radius:26px}.card h3{margin:11px 0 9px;font-size:22px}.metric{margin-top:12px;color:var(--forest);font-size:20px;font-weight:900}.search{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:10px;margin-top:18px}.search input{width:100%;min-height:46px;border:1px solid var(--line);border-radius:999px;background:rgba(240,240,240,.8);padding:10px 16px;font:inherit}.footer{display:flex;justify-content:space-between;gap:12px;margin-top:26px;padding:24px 4px 0;color:rgba(0,0,0,.62);font-size:13px}
        @media(max-width:1120px){.hero-grid,.grid-2{grid-template-columns:1fr}.grid-3,.grid-4{grid-template-columns:repeat(2,minmax(0,1fr))}.topbar{border-radius:30px;align-items:stretch;flex-direction:column}.brand,.actions{justify-content:center}}@media(max-width:720px){.shell{width:min(100% - 20px,1320px)}.hero-grid,.section{padding:24px}.section-title,.footer,.actions{align-items:stretch;flex-direction:column}.grid-3,.grid-4,.search{grid-template-columns:1fr}.nav a,.button{width:100%}}
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <a class="brand" href="{{ route('mall.index') }}">
                <span class="brand-mark">Kb</span>
                <span>
                    <strong>{{ __('kabeeri.brand.name') }} Mall</strong>
                    <small>V13 Public Discovery</small>
                </span>
            </a>

            @include('mall.partials.navigation')

            <div class="actions">
                <a class="button" href="{{ route('customer.dashboard') }}"><x-kabeeri-icon name="apps" />Customer Portal</a>
                <a class="button primary" href="{{ route('mall.trust') }}"><x-kabeeri-icon name="trust" />Trust</a>
                @include('components.language-switcher', ['context' => 'platform_public'])
                @include('components.theme-switcher', ['context' => 'platform_public'])
            </div>
        </header>

        <main>
            @yield('content')
        </main>

        <footer class="footer">
            <span>{{ __('kabeeri.brand.name') }} V13 Mall UX</span>
            <span>Mall is public discovery. Marketplace is internal extensions.</span>
        </footer>
    </div>
</body>
</html>
