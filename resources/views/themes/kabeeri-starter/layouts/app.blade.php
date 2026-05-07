@php
    $publicThemeMode = $site->metadata['public_theme_mode'] ?? config('kabeeri_ui_preferences.default_theme', 'light');
    $publicFont = $site->metadata['public_font'] ?? config('kabeeri_ui_preferences.default_font', 'ibm-plex-sans-arabic');
@endphp

<!DOCTYPE html>
<html lang="{{ $language }}" dir="{{ $direction }}" data-kbr-theme="{{ $publicThemeMode }}" data-kbr-font="{{ $publicFont }}" style="--kbr-font-family: {{ \App\Support\Ui\KabeeriUiPreference::fontFamily($publicFont) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle ?? $site->name }}</title>
    <style>
        :root {
            --ks-primary: {{ $primaryColor ?? '#17130d' }};
            --ks-text: #17130d;
            --ks-muted: #17130d;
            --ks-bg: #ffffff;
            --ks-surface: #ffffff;
            --ks-border: #fffaf0;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: var(--kbr-font-family);
            color: var(--ks-text);
            background: var(--ks-bg);
            line-height: 1.65;
        }
        .ks-wrap { max-width: 980px; margin: 0 auto; padding: 0 16px; }
        .ks-header {
            border-bottom: 1px solid var(--ks-border);
            background: var(--ks-surface);
        }
        .ks-header-inner {
            min-height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .ks-brand {
            margin: 0;
            display: inline-flex;
            align-items: center;
            font-size: 1.05rem;
            font-weight: 700;
        }
        .ks-theme {
            margin: 0;
            color: var(--ks-muted);
            font-size: 0.9rem;
        }
        .ks-main {
            padding: 26px 0;
        }
        .ks-card {
            background: var(--ks-surface);
            border: 1px solid var(--ks-border);
            border-radius: 8px;
            padding: 22px;
        }
        .ks-footer {
            border-top: 1px solid var(--ks-border);
            color: var(--ks-muted);
            font-size: 0.9rem;
            padding: 18px 0;
        }
        a {
            color: var(--ks-primary);
            text-decoration: none;
        }
        a:hover { text-decoration: underline; }
        @media (max-width: 720px) {
            .ks-main { padding: 18px 0; }
            .ks-card { padding: 16px; }
        }
    </style>
</head>
<body>
<header class="ks-header">
    <div class="ks-wrap ks-header-inner">
        <p class="ks-brand"><x-kabeeri-icon name="store" />{{ $site->name }}</p>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;justify-content:flex-end">
            <p class="ks-theme"><x-kabeeri-icon name="theme" />{{ $themeName }}</p>
            @include('components.language-switcher', ['context' => 'app_public'])
        </div>
    </div>
</header>
<main class="ks-main">
    <div class="ks-wrap">
        @yield('content')
    </div>
</main>
<footer class="ks-footer">
    <div class="ks-wrap">
        {{ $site->name }} &copy; {{ now()->year }}
    </div>
</footer>
</body>
</html>
