@php
    $activeTheme = $kabeeriUi['theme'] ?? 'light';
    $activeFontFamily = $kabeeriUi['font_family'] ?? '"IBM Plex Sans Arabic", "Almarai", sans-serif';
@endphp

@once
    <style>
        :root {
            --kbr-font-family: {!! $activeFontFamily !!};
            --kbr-page: #f3e5ab;
            --kbr-surface: rgba(243,229,171,.92);
            --kbr-card: rgba(243,229,171,.92);
            --kbr-card-strong: rgba(255,255,255,.76);
            --kbr-ink: #111111;
            --kbr-muted: rgba(17,17,17,.66);
            --kbr-line: rgba(17,17,17,.12);
            --kbr-gold: #111111;
            --kbr-dark: #111111;
            --kbr-nav-layer: 2147483000;
            --kbr-nav-control-layer: 2147483100;
            --kbr-dropdown-layer: 2147483200;
        }

        html[data-kbr-theme="dark"] {
            color-scheme: dark;
            --kbr-page: #111111;
            --kbr-surface: #1a1a1a;
            --kbr-card: #1a1a1a;
            --kbr-card-strong: #202020;
            --kbr-ink: #f3e5ab;
            --kbr-muted: rgba(243,229,171,.68);
            --kbr-line: rgba(243,229,171,.16);
            --ink: #f3e5ab;
            --soft: rgba(243,229,171,.72);
            --muted: rgba(243,229,171,.68);
            --panel: rgba(26,26,26,.92);
            --panel-strong: rgba(32,32,32,.88);
            --cream: #1a1a1a;
            --line: rgba(243,229,171,.16);
        }

        html[data-kbr-theme] body {
            font-family: var(--kbr-font-family) !important;
        }

        html[data-kbr-theme="dark"] body {
            background:
                radial-gradient(circle at 88% 8%, rgba(17,17,17,.22), transparent 24rem),
                radial-gradient(circle at 8% 12%, rgba(243,229,171,.08), transparent 26rem),
                linear-gradient(135deg, #111111 0%, #111111 62%, #1a1a1a 100%) !important;
            color: var(--kbr-ink) !important;
        }

        html[data-kbr-theme="dark"] body::before {
            opacity: .12;
        }

        html[data-kbr-theme="dark"] .topbar,
        html[data-kbr-theme="dark"] .hero,
        html[data-kbr-theme="dark"] .section:not(.dark),
        html[data-kbr-theme="dark"] .card:not(.dark),
        html[data-kbr-theme="dark"] .stat,
        html[data-kbr-theme="dark"] .final,
        html[data-kbr-theme="dark"] .route-list,
        html[data-kbr-theme="dark"] .doc-list,
        html[data-kbr-theme="dark"] .table-chip,
        html[data-kbr-theme="dark"] .history-item,
        html[data-kbr-theme="dark"] .check-item,
        html[data-kbr-theme="dark"] [class*="bg-[#f3e5ab]"],
        html[data-kbr-theme="dark"] [class*="bg-white"] {
            background-color: var(--kbr-card) !important;
            border-color: var(--kbr-line) !important;
            color: var(--kbr-ink) !important;
        }

        html[data-kbr-theme="dark"] [class*="bg-[#f3e5ab]/"],
        html[data-kbr-theme="dark"] [class*="bg-white/"] {
            background-color: rgba(26,26,26,.88) !important;
        }

        html[data-kbr-theme="dark"] [class*="bg-[#111111]/"] {
            background-color: rgba(243,229,171,.09) !important;
            color: var(--kbr-ink) !important;
        }

        html[data-kbr-theme="dark"] [class*="text-[#111111]"] {
            color: #f3e5ab !important;
        }

        html[data-kbr-theme="dark"] [class*="bg-[#111111]"],
        html[data-kbr-theme="dark"] [class*="bg-[#111111]/"] {
            background-color: #f3e5ab !important;
            color: #111111 !important;
        }

        html[data-kbr-theme="dark"] [class*="text-kabeeri-ink"] {
            color: var(--kbr-ink) !important;
        }

        html[data-kbr-theme="dark"] [class*="border-[#111111]"],
        html[data-kbr-theme="dark"] [class*="ring-[#111111]"] {
            border-color: var(--kbr-line) !important;
            --tw-ring-color: var(--kbr-line) !important;
        }

        html[data-kbr-theme="dark"] [class*="divide-[#111111]"] > :not([hidden]) ~ :not([hidden]) {
            border-color: var(--kbr-line) !important;
        }

        html[data-kbr-theme="dark"] input,
        html[data-kbr-theme="dark"] select,
        html[data-kbr-theme="dark"] textarea {
            background-color: var(--kbr-card-strong) !important;
            border-color: var(--kbr-line) !important;
            color: var(--kbr-ink) !important;
        }

        html[data-kbr-theme="dark"] option {
            background-color: #1a1a1a;
            color: #f3e5ab;
        }

        html[data-kbr-theme="dark"] ::placeholder {
            color: rgba(243,229,171,.48);
        }

        html[data-kbr-theme="dark"] .nav a:not(.primary),
        html[data-kbr-theme="dark"] .button:not(.primary),
        html[data-kbr-theme="dark"] button:not(.primary):not(.fi-btn):not(.kbr-sidebar-toggle) {
            background: #f3e5ab !important;
            border-color: rgba(243,229,171,.24) !important;
            color: #111111 !important;
        }

        html[data-kbr-theme="dark"] .nav a.primary,
        html[data-kbr-theme="dark"] .button.primary,
        html[data-kbr-theme="dark"] button.primary {
            background: #111111 !important;
            border-color: rgba(17,17,17,.42) !important;
            color: #f3e5ab !important;
            box-shadow: inset 0 0 0 1px rgba(17,17,17,.28);
        }

        .topbar,
        .top,
        .fi-topbar {
            z-index: var(--kbr-nav-layer) !important;
            overflow: visible !important;
            isolation: isolate;
        }

        header:has(.kbr-language-switcher),
        header:has(.kbr-theme-toggle),
        nav:has(.kbr-language-switcher),
        nav:has(.kbr-theme-toggle) {
            z-index: var(--kbr-nav-layer) !important;
            overflow: visible !important;
            isolation: isolate;
        }

        header details[open],
        nav details[open],
        .fi-dropdown-panel {
            z-index: var(--kbr-dropdown-layer) !important;
        }

        header details[open] > :not(summary),
        nav details[open] > :not(summary) {
            position: relative;
            z-index: var(--kbr-dropdown-layer) !important;
        }

        .kbr-language-switcher {
            position: relative;
            z-index: var(--kbr-nav-control-layer);
            display: inline-block;
            font-family: var(--kbr-font-family);
        }

        .kbr-language-switcher[open] {
            z-index: var(--kbr-dropdown-layer);
        }

        .kbr-language-switcher__trigger {
            display: inline-flex;
            min-height: 2.2rem;
            align-items: center;
            gap: .35rem;
            border: 1px solid rgba(17,17,17,.18);
            border-radius: 999px;
            background: #f3e5ab;
            padding: 0 .85rem;
            color: #111111;
            font-size: .74rem;
            font-weight: 900;
            line-height: 1;
            box-shadow: 0 10px 26px rgba(17,17,17,.08);
            cursor: pointer;
            list-style: none;
        }

        .kbr-language-switcher__trigger::-webkit-details-marker {
            display: none;
        }

        .kbr-language-switcher__trigger::after {
            content: "";
            width: .42rem;
            height: .42rem;
            border-inline-end: 2px solid currentColor;
            border-bottom: 2px solid currentColor;
            transform: rotate(45deg) translateY(-.12rem);
            opacity: .7;
        }

        .kbr-language-switcher[open] .kbr-language-switcher__trigger {
            background: #111111;
            color: #f3e5ab;
        }

        .kbr-language-switcher__menu {
            position: absolute;
            inset-inline-end: 0;
            top: calc(100% + .45rem);
            z-index: var(--kbr-dropdown-layer);
            display: grid;
            width: min(18rem, calc(100vw - 2rem));
            gap: .25rem;
            border: 1px solid rgba(17,17,17,.12);
            border-radius: 1.2rem;
            background: #f3e5ab;
            padding: .45rem;
            box-shadow: 0 24px 70px rgba(17,17,17,.18);
        }

        .kbr-language-switcher__option {
            display: flex;
            min-height: 2.15rem;
            align-items: center;
            justify-content: space-between;
            border: 1px solid transparent;
            border-radius: .9rem;
            padding: 0 .75rem;
            color: #111111;
            text-decoration: none;
            font-size: .76rem;
            font-weight: 900;
            line-height: 1;
        }

        .kbr-language-switcher__option:hover {
            border-color: rgba(17,17,17,.35);
            background: rgba(17,17,17,.12);
        }

        .kbr-language-switcher__option[aria-current="true"] {
            background: #111111;
            color: #f3e5ab;
            border-color: #111111;
        }

        .kbr-theme-toggle {
            position: relative;
            z-index: var(--kbr-nav-control-layer);
            display: inline-flex;
            align-items: center;
            gap: .18rem;
            min-height: 2.25rem;
            border: 1px solid rgba(17,17,17,.16);
            border-radius: 999px;
            background: rgba(243,229,171,.9);
            padding: .18rem;
            box-shadow: 0 10px 26px rgba(17,17,17,.08);
            font-family: var(--kbr-font-family);
        }

        .kbr-theme-toggle__button {
            display: inline-grid;
            place-items: center;
            width: 1.9rem;
            height: 1.9rem;
            border: 1px solid transparent;
            border-radius: 999px;
            color: #111111;
            background: #f3e5ab;
            text-decoration: none;
            transition: transform .18s ease, background .18s ease, color .18s ease, border-color .18s ease;
        }

        .kbr-theme-toggle__button:hover {
            transform: translateY(-1px);
            border-color: rgba(17,17,17,.38);
            background: rgba(17,17,17,.14);
        }

        .kbr-theme-toggle__button.is-active {
            color: #f3e5ab;
            border-color: #111111;
            background: #111111;
            box-shadow: inset 0 0 0 1px rgba(243,229,171,.16);
        }

        .kbr-language-switcher--admin {
            margin-inline-start: .5rem;
        }

        .kbr-language-switcher .sr-only,
        .kbr-theme-toggle .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0,0,0,0);
            white-space: nowrap;
            border: 0;
        }

        html[data-kbr-theme="dark"] .kbr-language-switcher__trigger {
            background: #f3e5ab;
            color: #111111;
            border-color: rgba(243,229,171,.3);
        }

        html[data-kbr-theme="dark"] .kbr-language-switcher[open] .kbr-language-switcher__trigger {
            background: #f3e5ab;
            color: #111111;
        }

        html[data-kbr-theme="dark"] .kbr-language-switcher__menu {
            background: #1a1a1a;
            border-color: rgba(243,229,171,.16);
        }

        html[data-kbr-theme="dark"] .kbr-language-switcher__option {
            color: #f3e5ab;
        }

        html[data-kbr-theme="dark"] .kbr-language-switcher__option[aria-current="true"] {
            background: #f3e5ab;
            color: #111111;
        }

        html[data-kbr-theme="dark"] .kbr-theme-toggle {
            border-color: rgba(243,229,171,.18);
            background: rgba(26,26,26,.9);
        }

        html[data-kbr-theme="dark"] .kbr-theme-toggle__button {
            color: #f3e5ab;
            background: rgba(243,229,171,.08);
        }

        html[data-kbr-theme="dark"] .kbr-theme-toggle__button:hover {
            border-color: rgba(243,229,171,.34);
            background: rgba(243,229,171,.12);
        }

        html[data-kbr-theme="dark"] .kbr-theme-toggle__button.is-active {
            color: #111111;
            border-color: #f3e5ab;
            background: #f3e5ab;
        }

        @media (max-width: 720px) {
            .kbr-language-switcher {
                width: 100%;
            }

            .kbr-language-switcher__trigger {
                width: 100%;
                justify-content: center;
            }

            .kbr-language-switcher__menu {
                inset-inline: 0;
                width: 100%;
            }

            .kbr-theme-toggle {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
    <script>
        (() => {
            const kabeeriTheme = @js($activeTheme === 'dark' ? 'dark' : 'light');
            const applyKabeeriTheme = () => {
                document.documentElement.dataset.kbrTheme = kabeeriTheme;
                document.documentElement.classList.toggle('dark', kabeeriTheme === 'dark');
            };

            applyKabeeriTheme();
            document.addEventListener('DOMContentLoaded', applyKabeeriTheme);
            document.addEventListener('livewire:navigated', applyKabeeriTheme);
        })();
    </script>
@endonce
