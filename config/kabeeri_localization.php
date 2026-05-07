<?php

return [
    'default' => env('KABEERI_DEFAULT_LOCALE', 'ar'),
    'session_key' => 'kabeeri_locale',
    'query_key' => 'lang',
    'context_session_keys' => [
        'platform_public' => 'kabeeri_locale_platform_public',
        'admin' => 'kabeeri_locale_admin',
        'customer' => 'kabeeri_locale_customer',
        'app_public' => 'kabeeri_locale_app_public',
    ],

    'supported' => [
        'ar' => ['label' => 'Arabic', 'native_label' => 'العربية', 'short_label' => 'AR', 'direction' => 'rtl'],
        'en' => ['label' => 'English', 'native_label' => 'English', 'short_label' => 'EN', 'direction' => 'ltr'],
        'es' => ['label' => 'Spanish', 'native_label' => 'Español', 'short_label' => 'ES', 'direction' => 'ltr'],
        'fr' => ['label' => 'French', 'native_label' => 'Français', 'short_label' => 'FR', 'direction' => 'ltr'],
        'it' => ['label' => 'Italian', 'native_label' => 'Italiano', 'short_label' => 'IT', 'direction' => 'ltr'],
        'de' => ['label' => 'German', 'native_label' => 'Deutsch', 'short_label' => 'DE', 'direction' => 'ltr'],
        'pt' => ['label' => 'Portuguese', 'native_label' => 'Português', 'short_label' => 'PT', 'direction' => 'ltr'],
        'ru' => ['label' => 'Russian', 'native_label' => 'Русский', 'short_label' => 'RU', 'direction' => 'ltr'],
        'hi' => ['label' => 'Hindi', 'native_label' => 'हिन्दी', 'short_label' => 'HI', 'direction' => 'ltr'],
        'ur' => ['label' => 'Urdu', 'native_label' => 'اردو', 'short_label' => 'UR', 'direction' => 'rtl'],
        'tr' => ['label' => 'Turkish', 'native_label' => 'Türkçe', 'short_label' => 'TR', 'direction' => 'ltr'],
        'id' => ['label' => 'Indonesian', 'native_label' => 'Bahasa Indonesia', 'short_label' => 'ID', 'direction' => 'ltr'],
        'zh' => ['label' => 'Chinese', 'native_label' => '中文', 'short_label' => 'ZH', 'direction' => 'ltr'],
        'ja' => ['label' => 'Japanese', 'native_label' => '日本語', 'short_label' => 'JA', 'direction' => 'ltr'],
        'ko' => ['label' => 'Korean', 'native_label' => '한국어', 'short_label' => 'KO', 'direction' => 'ltr'],
        'bn' => ['label' => 'Bengali', 'native_label' => 'বাংলা', 'short_label' => 'BN', 'direction' => 'ltr'],
    ],

    'contexts' => [
        'visitor' => 'Visitor interfaces',
        'platform_public' => 'Platform public interfaces',
        'customer' => 'Customer dashboard',
        'admin' => 'Platform admin and team dashboard',
        'app_public' => 'Customer app public pages',
    ],
];
