<?php

return [
    'theme_session_prefix' => 'kabeeri_theme_',
    'font_session_prefix' => 'kabeeri_font_',
    'sidebar_session_prefix' => 'kabeeri_sidebar_',
    'default_theme' => 'light',
    'default_font' => 'ibm-plex-sans-arabic',
    'themes' => [
        'light' => ['label' => 'Light', 'native_label' => 'Light'],
        'dark' => ['label' => 'Dark', 'native_label' => 'Dark'],
    ],
    'fonts' => [
        'ibm-plex-sans-arabic' => [
            'label' => 'IBM Plex Arabic',
            'family' => '"IBM Plex Sans Arabic", "Almarai", sans-serif',
            'href' => 'https://fonts.bunny.net/css?family=ibm-plex-sans-arabic:400,500,600,700|almarai:400,700,800',
        ],
        'almarai' => [
            'label' => 'Almarai',
            'family' => '"Almarai", "IBM Plex Sans Arabic", sans-serif',
            'href' => 'https://fonts.bunny.net/css?family=almarai:400,700,800|ibm-plex-sans-arabic:400,500,600,700',
        ],
        'tajawal' => [
            'label' => 'Tajawal',
            'family' => '"Tajawal", "IBM Plex Sans Arabic", sans-serif',
            'href' => 'https://fonts.bunny.net/css?family=tajawal:400,500,700,800|ibm-plex-sans-arabic:400,500,600,700',
        ],
    ],
    'contexts' => [
        'platform_public' => ['theme' => 'platform_admin', 'font' => 'platform_admin'],
        'admin' => ['theme' => 'admin_user', 'font' => 'admin_user'],
        'customer' => ['theme' => 'customer_user', 'font' => 'customer_user'],
        'app_public' => ['theme' => 'app_owner', 'font' => 'app_owner'],
    ],
];
