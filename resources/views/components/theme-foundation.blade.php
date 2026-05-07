@php
    $activeTheme = $kabeeriUi['theme'] ?? 'light';
    $activeFontFamily = $kabeeriUi['font_family'] ?? '"IBM Plex Sans Arabic", "Almarai", sans-serif';
@endphp

@once
    <style>
        :root {
            --kbr-font-family: {!! $activeFontFamily !!};
            --kbr-page: #fffaf0;
            --kbr-surface: rgba(255,250,240,.92);
            --kbr-card: rgba(255,250,240,.92);
            --kbr-card-strong: rgba(255,255,255,.76);
            --kbr-ink: #17130d;
            --kbr-muted: rgba(23,19,13,.66);
            --kbr-line: rgba(23,19,13,.12);
            --kbr-gold: #c98a2e;
            --kbr-dark: #17130d;
        }

        html[data-kbr-theme="dark"] {
            color-scheme: dark;
            --kbr-page: #17130d;
            --kbr-surface: #211a11;
            --kbr-card: #211a11;
            --kbr-card-strong: #2a2116;
            --kbr-ink: #fffaf0;
            --kbr-muted: rgba(255,250,240,.68);
            --kbr-line: rgba(255,250,240,.16);
            --ink: #fffaf0;
            --soft: rgba(255,250,240,.72);
            --muted: rgba(255,250,240,.68);
            --panel: rgba(33,26,17,.92);
            --panel-strong: rgba(42,33,22,.88);
            --cream: #211a11;
            --line: rgba(255,250,240,.16);
        }

        html[data-kbr-theme] body {
            font-family: var(--kbr-font-family) !important;
        }

        html[data-kbr-theme="dark"] body {
            background:
                radial-gradient(circle at 88% 8%, rgba(201,138,46,.22), transparent 24rem),
                radial-gradient(circle at 8% 12%, rgba(255,250,240,.08), transparent 26rem),
                linear-gradient(135deg, #17130d 0%, #17130d 62%, #211a11 100%) !important;
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
        html[data-kbr-theme="dark"] [class*="bg-[#fffaf0]"],
        html[data-kbr-theme="dark"] [class*="bg-white"] {
            background-color: var(--kbr-card) !important;
            border-color: var(--kbr-line) !important;
            color: var(--kbr-ink) !important;
        }

        html[data-kbr-theme="dark"] [class*="bg-[#fffaf0]/"],
        html[data-kbr-theme="dark"] [class*="bg-white/"] {
            background-color: rgba(33,26,17,.88) !important;
        }

        html[data-kbr-theme="dark"] [class*="bg-[#17130d]/"] {
            background-color: rgba(255,250,240,.09) !important;
            color: var(--kbr-ink) !important;
        }

        html[data-kbr-theme="dark"] [class*="text-[#17130d]"],
        html[data-kbr-theme="dark"] [class*="text-kabeeri-ink"] {
            color: var(--kbr-ink) !important;
        }

        html[data-kbr-theme="dark"] [class*="text-[#c98a2e]"] {
            color: #f4c46b !important;
        }

        html[data-kbr-theme="dark"] [class*="bg-[#c98a2e]"],
        html[data-kbr-theme="dark"] [class*="bg-[#c98a2e]/"] {
            color: #17130d !important;
        }

        html[data-kbr-theme="dark"] [class*="border-[#17130d]"],
        html[data-kbr-theme="dark"] [class*="ring-[#17130d]"] {
            border-color: var(--kbr-line) !important;
            --tw-ring-color: var(--kbr-line) !important;
        }

        html[data-kbr-theme="dark"] [class*="divide-[#17130d]"] > :not([hidden]) ~ :not([hidden]) {
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
            background-color: #211a11;
            color: #fffaf0;
        }

        html[data-kbr-theme="dark"] ::placeholder {
            color: rgba(255,250,240,.48);
        }

        .kbr-language-switcher {
            position: relative;
            display: inline-block;
            font-family: var(--kbr-font-family);
        }

        .kbr-language-switcher__trigger {
            display: inline-flex;
            min-height: 2.2rem;
            align-items: center;
            gap: .35rem;
            border: 1px solid rgba(23,19,13,.18);
            border-radius: 999px;
            background: #fffaf0;
            padding: 0 .85rem;
            color: #17130d;
            font-size: .74rem;
            font-weight: 900;
            line-height: 1;
            box-shadow: 0 10px 26px rgba(23,19,13,.08);
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
            background: #17130d;
            color: #fffaf0;
        }

        .kbr-language-switcher__menu {
            position: absolute;
            inset-inline-end: 0;
            top: calc(100% + .45rem);
            z-index: 80;
            display: grid;
            width: min(18rem, calc(100vw - 2rem));
            gap: .25rem;
            border: 1px solid rgba(23,19,13,.12);
            border-radius: 1.2rem;
            background: #fffaf0;
            padding: .45rem;
            box-shadow: 0 24px 70px rgba(23,19,13,.18);
        }

        .kbr-language-switcher__option {
            display: flex;
            min-height: 2.15rem;
            align-items: center;
            justify-content: space-between;
            border: 1px solid transparent;
            border-radius: .9rem;
            padding: 0 .75rem;
            color: #17130d;
            text-decoration: none;
            font-size: .76rem;
            font-weight: 900;
            line-height: 1;
        }

        .kbr-language-switcher__option:hover {
            border-color: rgba(201,138,46,.35);
            background: rgba(201,138,46,.12);
        }

        .kbr-language-switcher__option[aria-current="true"] {
            background: #17130d;
            color: #fffaf0;
            border-color: #17130d;
        }

        .kbr-language-switcher--admin {
            margin-inline-start: .5rem;
        }

        .kbr-language-switcher .sr-only {
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
            background: #fffaf0;
            color: #17130d;
            border-color: rgba(255,250,240,.3);
        }

        html[data-kbr-theme="dark"] .kbr-language-switcher[open] .kbr-language-switcher__trigger {
            background: #c98a2e;
            color: #17130d;
        }

        html[data-kbr-theme="dark"] .kbr-language-switcher__menu {
            background: #211a11;
            border-color: rgba(255,250,240,.16);
        }

        html[data-kbr-theme="dark"] .kbr-language-switcher__option {
            color: #fffaf0;
        }

        html[data-kbr-theme="dark"] .kbr-language-switcher__option[aria-current="true"] {
            background: #fffaf0;
            color: #17130d;
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
