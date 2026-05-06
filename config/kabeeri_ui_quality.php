<?php

return [
    'version' => 'V14',
    'name' => 'KABEERI UI Quality, Accessibility, Responsive, and Release Candidate',
    'rules' => [
        'scope' => 'V14 is a release-candidate QA layer over V9-V13. It should not introduce a new product surface that hides quality problems.',
        'runtime' => 'Laravel Blade pages remain bridge/fallback surfaces. Next.js remains the target public theme runtime.',
        'truth' => 'Task tracker status must match real implementation and automated verification.',
        'separation' => 'Mall public discovery and Marketplace extension installs must remain visibly separated.',
        'permission_safety' => 'Sensitive admin, package, billing, finance, verification, and AI actions must be permission-aware and explain blocked states.',
    ],
    'route_groups' => [
        'v9_foundation' => ['label' => 'V9 UI Foundation', 'source' => 'App\Support\Ui\V9UiFoundation::currentRoutes'],
        'v10_admin' => ['label' => 'V10 Admin Command', 'source' => 'config:kabeeri_admin.pages+quick_actions'],
        'v11_public' => ['label' => 'V11 Public Marketing', 'source' => 'config:kabeeri_public.pages'],
        'v12_marketplace' => ['label' => 'V12 Marketplace and Developers', 'source' => 'config:kabeeri_marketplace.pages+admin_routes'],
        'v13_external' => ['label' => 'V13 Mall, Customer, Partner, Network', 'source' => 'config:kabeeri_external.pages+admin_routes'],
    ],
    'accessibility' => [
        'admin' => ['semantic headings', 'visible status labels', 'action labels', 'permission explanations', 'keyboard-readable links', 'color not sole status indicator'],
        'public' => ['html lang and dir', 'semantic navigation labels', 'form labels', 'focusable links/buttons', 'responsive text scale', 'contrast-oriented palettes'],
        'forms' => ['CSRF', 'server validation', 'error summary', 'success feedback', 'required field clarity'],
    ],
    'responsive' => [
        'mobile' => ['single column layout', 'full-width actions', 'reduced shell width', 'stacked navigation', 'readable hero scale'],
        'tablet' => ['two-column card grids', 'wrapped navigation', 'safe sticky header behavior', 'balanced hero columns'],
        'desktop' => ['wide shell limit', 'multi-column grids', 'scan-friendly sections', 'sticky navigation without content overlap'],
        'breakpoints' => ['720px', '1120px', '1320px shell'],
    ],
    'navigation' => [
        'consistent_links' => ['home/root command center', 'admin', 'public', 'marketplace', 'mall', 'customer', 'partners', 'network'],
        'separation_links' => ['Mall links to public discovery', 'Marketplace links to developer/extensions', 'Admin links to Filament/governance'],
        'blocked_actions' => ['billing.manage', 'company.verification.approve', 'finance.journal.post', 'users.role.assign', 'plugin.install', 'ai.apply_sensitive_change'],
    ],
    'states' => [
        'empty' => ['No businesses are published yet.', 'No products are published yet.', 'No services are published yet.', 'No courses are published yet.', 'No talent profiles are published yet.', 'No travel listings are published yet.'],
        'error' => ['validation error summary', '404 for unpublished listings', 'No-Go release gates'],
        'feedback' => ['contact sales success status', 'Ready/Pending badges', 'release commands', 'task tracker counts'],
    ],
    'security_permission' => [
        'admin' => ['permission-aware navigation', 'blocked sensitive actions', 'audit expectations', 'policy-backed resources'],
        'marketplace' => ['permission disclosure', 'compatibility', 'signing', 'safe uninstall', 'rollback', 'support policy'],
        'mall' => ['claim/report', 'moderation cases', 'publication consent', 'verification trust'],
    ],
    'performance' => [
        'commands' => ['npm run build', 'vendor/bin/pint --test', 'php artisan test'],
        'asset_expectations' => ['Vite manifest exists after build', 'CSS bundle generated', 'JS entry generated', 'no committed generated diff required'],
    ],
    'manual_qa' => [
        'browsers' => ['Chrome/Edge Chromium', 'Firefox', 'Safari/WebKit where available'],
        'devices' => ['mobile portrait', 'mobile landscape', 'tablet', 'desktop', 'wide desktop'],
        'flows' => ['admin command pages', 'public onboarding', 'marketplace themes/plugins', 'Mall discovery', 'customer portal', 'partner referrals'],
    ],
    'next_runtime' => [
        'status' => 'documented_not_scaffolded',
        'expected_path' => 'apps/public-web',
        'reason' => 'V9-V14 document the separation and bridge contracts. A production Next.js app should be scaffolded only when API contracts and owner direction are ready.',
        'compliance_checks' => ['Blade marked as bridge', 'Next.js target documented', 'React component mapping documented in V12', 'public pages avoid pretending Blade is permanent theme runtime'],
    ],
    'quality_gates' => [
        ['key' => 'all_previous_versions_ready', 'label' => 'V9-V13 readiness remains true'],
        ['key' => 'route_inventory_ready', 'label' => 'Full UI route inventory has no missing configured routes'],
        ['key' => 'accessibility_ready', 'label' => 'Accessibility checklist coverage exists'],
        ['key' => 'responsive_ready', 'label' => 'Mobile/tablet/desktop responsive checklist coverage exists'],
        ['key' => 'permission_ready', 'label' => 'Security and permission UI coverage exists'],
        ['key' => 'design_system_ready', 'label' => 'Design system and component coverage exists'],
        ['key' => 'theme_plugin_qa_ready', 'label' => 'Theme and plugin QA coverage exists'],
        ['key' => 'mall_marketplace_separated', 'label' => 'Mall and Marketplace separation is represented'],
        ['key' => 'docs_ready', 'label' => 'V14 docs and handoff are present'],
        ['key' => 'tests_ready', 'label' => 'V14 smoke tests are present'],
        ['key' => 'tracker_synced', 'label' => 'V14 task tracker is synced'],
    ],
];
