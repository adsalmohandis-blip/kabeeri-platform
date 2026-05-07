<?php

return [
    'default' => env('KABEERI_DEFAULT_LOCALE', 'ar'),
    'session_key' => 'kabeeri_locale',
    'query_key' => 'lang',

    'supported' => [
        'ar' => [
            'label' => 'Arabic',
            'native_label' => 'العربية',
            'short_label' => 'AR',
            'direction' => 'rtl',
        ],
        'en' => [
            'label' => 'English',
            'native_label' => 'English',
            'short_label' => 'EN',
            'direction' => 'ltr',
        ],
    ],

    'contexts' => [
        'visitor' => 'Visitor interfaces',
        'customer' => 'Customer dashboard',
        'admin' => 'Platform admin and team dashboard',
    ],
];
