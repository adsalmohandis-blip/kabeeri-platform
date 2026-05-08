@php
    $activeNav = $activeNav ?? 'overview';
    $activeOrganization = $dashboard['active_organization'] ?? null;
    $sites = $dashboard['sites'] ?? collect();
    $firstSite = $sites->first();
    $workspaceReady = filled($activeOrganization);
    $navItems = [
        ['key' => 'dashboard_overview', 'route' => route('customer.workspace'), 'icon' => 'chart', 'active' => $activeNav === 'overview'],
        ['key' => 'apps_manage', 'route' => route('customer.apps.index'), 'icon' => 'apps', 'active' => $activeNav === 'apps'],
        ['key' => 'new_app', 'route' => route('customer.apps.create'), 'icon' => 'plus', 'active' => $activeNav === 'create'],
        ['key' => 'themes_plugins', 'route' => $firstSite ? route('customer.apps.plugins', ['username' => $firstSite->username]) : route('customer.apps.index'), 'icon' => 'plugin', 'active' => in_array($activeNav, ['themes', 'plugins'], true)],
        ['key' => 'trash', 'route' => route('customer.apps.trash'), 'icon' => 'trash', 'active' => $activeNav === 'trash'],
        ['key' => 'capabilities', 'route' => route('customer.workspace').'#capabilities', 'icon' => 'account', 'active' => $activeNav === 'capabilities'],
    ];
@endphp

@once
    <style>
        .kbr-customer-shell{transition:grid-template-columns .22s ease}
        @media(min-width:1024px){
            html[data-kbr-customer-sidebar="collapsed"] .kbr-customer-shell{grid-template-columns:5.25rem minmax(0,1fr)!important}
            html[data-kbr-customer-sidebar="collapsed"] #workspace-sidebar{width:5.25rem}
            html[data-kbr-customer-sidebar="collapsed"] #workspace-sidebar .kbr-sidebar-label,
            html[data-kbr-customer-sidebar="collapsed"] #workspace-sidebar .kbr-sidebar-brand-copy,
            html[data-kbr-customer-sidebar="collapsed"] #workspace-sidebar .kbr-sidebar-status{display:none}
            html[data-kbr-customer-sidebar="collapsed"] #workspace-sidebar .kbr-sidebar-brand,
            html[data-kbr-customer-sidebar="collapsed"] #workspace-sidebar .kbr-sidebar-link,
            html[data-kbr-customer-sidebar="collapsed"] #workspace-sidebar .kbr-sidebar-toggle{justify-content:center}
            html[data-kbr-customer-sidebar="collapsed"] #workspace-sidebar .kbr-icon{margin-inline-end:0!important}
        }
    </style>
    <script>
        (() => {
            const storageKey = 'kabeeri.sidebar.customer';
            const applyState = (collapsed) => {
                document.documentElement.dataset.kbrCustomerSidebar = collapsed ? 'collapsed' : 'expanded';
                document.querySelectorAll('[data-kbr-sidebar-toggle="customer"]').forEach((button) => {
                    const label = collapsed ? button.dataset.labelExpand : button.dataset.labelCollapse;
                    button.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
                    button.setAttribute('title', label);
                    const text = button.querySelector('[data-kbr-sidebar-toggle-label]');
                    if (text) {
                        text.textContent = label;
                    }
                });
            };

            document.addEventListener('DOMContentLoaded', () => {
                applyState(localStorage.getItem(storageKey) === 'collapsed');
                document.querySelectorAll('[data-kbr-sidebar-toggle="customer"]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const collapsed = document.documentElement.dataset.kbrCustomerSidebar !== 'collapsed';
                        localStorage.setItem(storageKey, collapsed ? 'collapsed' : 'expanded');
                        applyState(collapsed);
                    });
                });
            });
        })();
    </script>
@endonce

<aside id="workspace-sidebar" class="hidden border-e border-[#000000]/10 bg-[#000000] text-[#f0f0f0] lg:sticky lg:top-0 lg:flex lg:h-screen lg:flex-col">
    <div class="px-5 py-5">
        <a href="{{ route('customer.workspace') }}" class="kbr-sidebar-brand flex items-center gap-3">
            <span class="grid h-10 w-10 place-items-center rounded-full bg-[#f0f0f0] text-sm font-black text-[#000000]">{{ __('kabeeri.brand.mark') }}</span>
            <span class="kbr-sidebar-brand-copy">
                <strong class="block text-sm font-black leading-tight">{{ __('kabeeri.brand.name') }}</strong>
                <small class="mt-0.5 block text-[11px] font-bold text-[#f0f0f0]/58">{{ __('kabeeri.ui.apps_dashboard') }}</small>
            </span>
        </a>
    </div>

    <nav class="flex-1 space-y-1 px-3" aria-label="{{ __('kabeeri.ui.dashboard_nav') }}">
        @foreach ($navItems as $item)
            <a href="{{ $item['route'] }}" class="kbr-sidebar-link flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-black transition {{ $item['active'] ? 'bg-[#f0f0f0] text-[#000000]' : 'text-[#f0f0f0]/74 hover:bg-[#fafafa]/10 hover:text-[#f0f0f0]' }}">
                <x-kabeeri-icon name="{{ $item['icon'] }}" />
                <span class="kbr-sidebar-label">{{ __('kabeeri.ui.'.$item['key']) }}</span>
            </a>
        @endforeach
    </nav>

    <div class="kbr-sidebar-status m-3 rounded-3xl border border-[#fafafa]/10 bg-[#fafafa]/[.06] p-4">
        <p class="text-[10px] font-black uppercase tracking-[.18em] text-[#f0f0f0]/58">{{ __('kabeeri.ui.workspace_status') }}</p>
        <p class="mt-2 text-sm font-black">{{ __('kabeeri.ui.'.($workspaceReady ? 'ready' : 'needs_setup')) }}</p>
        <p class="mt-1 line-clamp-2 text-xs leading-5 text-[#f0f0f0]/60">{{ $activeOrganization?->name ?? __('kabeeri.ui.needs_setup_sentence') }}</p>
        @unless ($workspaceReady)
            <a href="{{ route('customer.onboarding') }}" class="mt-3 inline-flex w-full items-center justify-center rounded-full bg-[#f0f0f0] px-3 py-2 text-xs font-black text-[#000000]"><x-kabeeri-icon name="plus" />{{ __('kabeeri.ui.start_now') }}</a>
        @endunless
    </div>

    <div class="px-3 pb-4">
        <button
            type="button"
            class="kbr-sidebar-toggle flex w-full items-center gap-3 rounded-2xl border border-[#fafafa]/10 bg-[#fafafa]/[.06] px-3 py-2.5 text-xs font-black text-[#f0f0f0] transition hover:bg-[#fafafa]/10"
            data-kbr-sidebar-toggle="customer"
            data-label-collapse="{{ __('kabeeri.ui.collapse_sidebar') }}"
            data-label-expand="{{ __('kabeeri.ui.expand_sidebar') }}"
            aria-expanded="true"
        >
            <x-kabeeri-icon name="settings" />
            <span class="kbr-sidebar-label" data-kbr-sidebar-toggle-label>{{ __('kabeeri.ui.collapse_sidebar') }}</span>
        </button>
    </div>
</aside>
