@php
    $adminFontFamily = $kabeeriUi['font_family'] ?? '"IBM Plex Sans Arabic", "Almarai", sans-serif';
    $adminTheme = ($kabeeriUi['theme'] ?? 'light') === 'dark' ? 'dark' : 'light';
@endphp

@include('components.theme-foundation')

<script>
    (() => {
        const kabeeriAdminTheme = @js($adminTheme);
        const applyKabeeriAdminTheme = () => {
            document.documentElement.dataset.kbrTheme = kabeeriAdminTheme;
            document.documentElement.classList.toggle('dark', kabeeriAdminTheme === 'dark');
            window.theme = kabeeriAdminTheme;

            try {
                localStorage.setItem('theme', kabeeriAdminTheme);
            } catch (error) {
                // Some privacy modes block localStorage; the server preference still applies.
            }
        };

        applyKabeeriAdminTheme();
        document.addEventListener('alpine:init', applyKabeeriAdminTheme);
        document.addEventListener('livewire:navigated', applyKabeeriAdminTheme);
    })();
</script>

<style>
    html.fi {
        --kbr-admin-font-family: {!! $adminFontFamily !!};
    }

    .fi-body,
    .fi-topbar,
    .fi-sidebar,
    .fi-dropdown-panel {
        font-family: var(--kbr-admin-font-family), var(--font-family), sans-serif !important;
    }

    .fi-sidebar svg,
    .fi-topbar svg {
        max-width: 1.125rem;
        max-height: 1.125rem;
        flex-shrink: 0;
    }

    .kabeeri-admin-widget svg,
    .kabeeri-admin-surface svg {
        max-width: 1.25rem;
        max-height: 1.25rem;
        flex-shrink: 0;
    }

    .kabeeri-admin-widget [data-admin-icon-scale="compact"] svg,
    .kabeeri-admin-surface [data-admin-icon-scale="compact"] svg {
        width: 1.125rem;
        height: 1.125rem;
    }

    html.fi[data-kbr-theme="dark"] .fi-body {
        background: #000000;
        color: #f0f0f0;
    }

    html.fi[data-kbr-theme="dark"] .fi-sidebar,
    html.fi[data-kbr-theme="dark"] .fi-topbar,
    html.fi[data-kbr-theme="dark"] .fi-main,
    html.fi[data-kbr-theme="dark"] .fi-section,
    html.fi[data-kbr-theme="dark"] .fi-dropdown-panel,
    html.fi[data-kbr-theme="dark"] .fi-modal-window,
    html.fi[data-kbr-theme="dark"] .fi-ta-ctn,
    html.fi[data-kbr-theme="dark"] .fi-input-wrp {
        border-color: rgba(240,240,240,.14) !important;
    }

    html.fi[data-kbr-theme="dark"] .fi-sidebar,
    html.fi[data-kbr-theme="dark"] .fi-topbar,
    html.fi[data-kbr-theme="dark"] .fi-section,
    html.fi[data-kbr-theme="dark"] .fi-dropdown-panel,
    html.fi[data-kbr-theme="dark"] .fi-modal-window,
    html.fi[data-kbr-theme="dark"] .fi-ta-ctn,
    html.fi[data-kbr-theme="dark"] .fi-input-wrp {
        background-color: #000000 !important;
        color: #f0f0f0 !important;
    }

    html.fi[data-kbr-theme="dark"] .fi-main-sidebar,
    html.fi[data-kbr-theme="dark"] .fi-sidebar-header {
        background-color: #000000 !important;
        border-color: rgba(240,240,240,.12) !important;
    }

    html.fi[data-kbr-theme="dark"] .fi-sidebar-item-active a,
    html.fi[data-kbr-theme="dark"] .fi-sidebar-item a:hover {
        background-color: #f0f0f0 !important;
        color: #000000 !important;
    }

    html.fi[data-kbr-theme="dark"] .fi-btn-color-primary {
        background-color: #000000 !important;
        color: #000000 !important;
    }
</style>
