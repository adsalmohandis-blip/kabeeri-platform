@php
    $activeTheme = $kabeeriUi['theme'] ?? 'light';
    $activeFontFamily = $kabeeriUi['font_family'] ?? '"IBM Plex Sans Arabic", "Almarai", sans-serif';
@endphp

@once
    <style>
        :root {
            --kbr-font-family: {!! $activeFontFamily !!};
            --kbr-page: #f0f0f0;
            --kbr-surface: rgba(240,240,240,.92);
            --kbr-card: rgba(240,240,240,.92);
            --kbr-card-strong: rgba(250,250,250,.76);
            --kbr-ink: #000000;
            --kbr-muted: rgba(0,0,0,.66);
            --kbr-line: rgba(0,0,0,.12);
            --kbr-gold: #000000;
            --kbr-dark: #000000;
            --kbr-nav-layer: 2147483000;
            --kbr-nav-control-layer: 2147483100;
            --kbr-dropdown-layer: 2147483200;
        }

        html[data-kbr-theme="dark"] {
            color-scheme: dark;
            --kbr-page: #000000;
            --kbr-surface: #000000;
            --kbr-card: #000000;
            --kbr-card-strong: #000000;
            --kbr-ink: #f0f0f0;
            --kbr-muted: rgba(240,240,240,.68);
            --kbr-line: rgba(240,240,240,.16);
            --ink: #f0f0f0;
            --soft: rgba(240,240,240,.72);
            --muted: rgba(240,240,240,.68);
            --panel: rgba(0,0,0,.92);
            --panel-strong: rgba(0,0,0,.88);
            --cream: #000000;
            --line: rgba(240,240,240,.16);
        }

        html[data-kbr-theme] body {
            font-family: var(--kbr-font-family) !important;
        }

        html[data-kbr-theme="dark"] body {
            background:
                radial-gradient(circle at 88% 8%, rgba(0,0,0,.22), transparent 24rem),
                radial-gradient(circle at 8% 12%, rgba(240,240,240,.08), transparent 26rem),
                linear-gradient(135deg, #000000 0%, #000000 62%, #000000 100%) !important;
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
        html[data-kbr-theme="dark"] [class*="bg-[#f0f0f0]"],
        html[data-kbr-theme="dark"] [class*="bg-[#fafafa]"] {
            background-color: var(--kbr-card) !important;
            border-color: var(--kbr-line) !important;
            color: var(--kbr-ink) !important;
        }

        html[data-kbr-theme="dark"] [class*="bg-[#f0f0f0]/"],
        html[data-kbr-theme="dark"] [class*="bg-[#fafafa]/"] {
            background-color: rgba(0,0,0,.88) !important;
        }

        html[data-kbr-theme="dark"] [class*="bg-[#000000]/"] {
            background-color: rgba(240,240,240,.09) !important;
            color: var(--kbr-ink) !important;
        }

        html[data-kbr-theme="dark"] [class*="text-[#000000]"] {
            color: #f0f0f0 !important;
        }

        html[data-kbr-theme="dark"] [class*="bg-[#000000]"],
        html[data-kbr-theme="dark"] [class*="bg-[#000000]/"] {
            background-color: #f0f0f0 !important;
            color: #000000 !important;
        }

        html[data-kbr-theme="dark"] [class*="text-kabeeri-ink"] {
            color: var(--kbr-ink) !important;
        }

        html[data-kbr-theme="dark"] [class*="border-[#000000]"],
        html[data-kbr-theme="dark"] [class*="ring-[#000000]"] {
            border-color: var(--kbr-line) !important;
            --tw-ring-color: var(--kbr-line) !important;
        }

        html[data-kbr-theme="dark"] [class*="divide-[#000000]"] > :not([hidden]) ~ :not([hidden]) {
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
            background-color: #000000;
            color: #f0f0f0;
        }

        html[data-kbr-theme="dark"] ::placeholder {
            color: rgba(240,240,240,.48);
        }

        html[data-kbr-theme="dark"] .nav a:not(.primary),
        html[data-kbr-theme="dark"] .button:not(.primary),
        html[data-kbr-theme="dark"] button:not(.primary):not(.fi-btn):not(.kbr-sidebar-toggle) {
            background: #f0f0f0 !important;
            border-color: rgba(240,240,240,.24) !important;
            color: #000000 !important;
        }

        html[data-kbr-theme="dark"] .nav a.primary,
        html[data-kbr-theme="dark"] .button.primary,
        html[data-kbr-theme="dark"] button.primary {
            background: #000000 !important;
            border-color: rgba(0,0,0,.42) !important;
            color: #f0f0f0 !important;
            box-shadow: inset 0 0 0 1px rgba(0,0,0,.28);
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
            border: 1px solid rgba(0,0,0,.18);
            border-radius: 999px;
            background: #f0f0f0;
            padding: 0 .85rem;
            color: #000000;
            font-size: .74rem;
            font-weight: 900;
            line-height: 1;
            box-shadow: 0 10px 26px rgba(0,0,0,.08);
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
            background: #000000;
            color: #f0f0f0;
        }

        .kbr-language-switcher__menu {
            position: absolute;
            inset-inline-end: 0;
            top: calc(100% + .45rem);
            z-index: var(--kbr-dropdown-layer);
            display: grid;
            width: min(18rem, calc(100vw - 2rem));
            gap: .25rem;
            border: 1px solid rgba(0,0,0,.12);
            border-radius: 1.2rem;
            background: #f0f0f0;
            padding: .45rem;
            box-shadow: 0 24px 70px rgba(0,0,0,.18);
        }

        .kbr-language-switcher__option {
            display: flex;
            min-height: 2.15rem;
            align-items: center;
            justify-content: space-between;
            border: 1px solid transparent;
            border-radius: .9rem;
            padding: 0 .75rem;
            color: #000000;
            text-decoration: none;
            font-size: .76rem;
            font-weight: 900;
            line-height: 1;
        }

        .kbr-language-switcher__option:hover {
            border-color: rgba(0,0,0,.35);
            background: rgba(0,0,0,.12);
        }

        .kbr-language-switcher__option[aria-current="true"] {
            background: #000000;
            color: #f0f0f0;
            border-color: #000000;
        }

        .kbr-theme-toggle {
            position: relative;
            z-index: var(--kbr-nav-control-layer);
            display: inline-flex;
            align-items: center;
            gap: .18rem;
            min-height: 2.25rem;
            border: 1px solid rgba(0,0,0,.16);
            border-radius: 999px;
            background: rgba(240,240,240,.9);
            padding: .18rem;
            box-shadow: 0 10px 26px rgba(0,0,0,.08);
            font-family: var(--kbr-font-family);
        }

        .kbr-theme-toggle__button {
            display: inline-grid;
            place-items: center;
            width: 1.9rem;
            height: 1.9rem;
            border: 1px solid transparent;
            border-radius: 999px;
            color: #000000;
            background: #f0f0f0;
            text-decoration: none;
            transition: transform .18s ease, background .18s ease, color .18s ease, border-color .18s ease;
        }

        .kbr-theme-toggle__button:hover {
            transform: translateY(-1px);
            border-color: rgba(0,0,0,.38);
            background: rgba(0,0,0,.14);
        }

        .kbr-theme-toggle__button.is-active {
            color: #f0f0f0;
            border-color: #000000;
            background: #000000;
            box-shadow: inset 0 0 0 1px rgba(240,240,240,.16);
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
            background: #f0f0f0;
            color: #000000;
            border-color: rgba(240,240,240,.3);
        }

        html[data-kbr-theme="dark"] .kbr-language-switcher[open] .kbr-language-switcher__trigger {
            background: #f0f0f0;
            color: #000000;
        }

        html[data-kbr-theme="dark"] .kbr-language-switcher__menu {
            background: #000000;
            border-color: rgba(240,240,240,.16);
        }

        html[data-kbr-theme="dark"] .kbr-language-switcher__option {
            color: #f0f0f0;
        }

        html[data-kbr-theme="dark"] .kbr-language-switcher__option[aria-current="true"] {
            background: #f0f0f0;
            color: #000000;
        }

        html[data-kbr-theme="dark"] .kbr-theme-toggle {
            border-color: rgba(240,240,240,.18);
            background: rgba(0,0,0,.9);
        }

        html[data-kbr-theme="dark"] .kbr-theme-toggle__button {
            color: #f0f0f0;
            background: rgba(240,240,240,.08);
        }

        html[data-kbr-theme="dark"] .kbr-theme-toggle__button:hover {
            border-color: rgba(240,240,240,.34);
            background: rgba(240,240,240,.12);
        }

        html[data-kbr-theme="dark"] .kbr-theme-toggle__button.is-active {
            color: #000000;
            border-color: #f0f0f0;
            background: #f0f0f0;
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
