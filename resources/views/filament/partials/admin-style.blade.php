@php($adminFontFamily = $kabeeriUi['font_family'] ?? '"IBM Plex Sans Arabic", "Almarai", sans-serif')

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
</style>
