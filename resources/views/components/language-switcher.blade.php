@php
    $context = $context ?? 'visitor';
    $currentLocale = \App\Support\Localization\KabeeriLocale::current();
    $supportedLocales = \App\Support\Localization\KabeeriLocale::supported();
    $currentNativeLabel = $supportedLocales[$currentLocale]['native_label'] ?? $currentLocale;
@endphp

@once
    <style>
        html[data-kbr-theme="dark"]{color-scheme:dark}
        html[data-kbr-theme="dark"] body{background:#17130d!important;color:#fffaf0!important}
        html[data-kbr-theme="dark"] .bg-\[\#fffaf0\],
        html[data-kbr-theme="dark"] .bg-white,
        html[data-kbr-theme="dark"] .bg-white\/66,
        html[data-kbr-theme="dark"] .bg-white\/70{background-color:#211a11!important;color:#fffaf0!important}
        html[data-kbr-theme="dark"] .text-\[\#17130d\]{color:#fffaf0!important}
        html[data-kbr-theme="dark"] .border-\[\#17130d\]\/10{border-color:rgba(255,250,240,.16)!important}
        html{--kbr-font-family:{{ $kabeeriUi['font_family'] ?? '"IBM Plex Sans Arabic", "Almarai", sans-serif' }}}
        body{font-family:var(--kbr-font-family)!important}
        .kbr-language-switcher{position:relative;display:inline-block;font-family:"IBM Plex Sans Arabic","Almarai",sans-serif}
        .kbr-language-switcher__trigger{display:inline-flex;min-height:2.2rem;align-items:center;gap:.35rem;border:1px solid rgba(23,19,13,.18);border-radius:999px;background:#fffaf0;padding:0 .85rem;color:#17130d;font-size:.74rem;font-weight:900;line-height:1;box-shadow:0 10px 26px rgba(23,19,13,.08);cursor:pointer;list-style:none}
        .kbr-language-switcher__trigger::-webkit-details-marker{display:none}
        .kbr-language-switcher__trigger:after{content:"";width:.42rem;height:.42rem;border-inline-end:2px solid currentColor;border-bottom:2px solid currentColor;transform:rotate(45deg) translateY(-.12rem);opacity:.7}
        .kbr-language-switcher[open] .kbr-language-switcher__trigger{background:#17130d;color:#fffaf0}
        .kbr-language-switcher__menu{position:absolute;inset-inline-end:0;top:calc(100% + .45rem);z-index:80;display:grid;width:min(18rem,calc(100vw - 2rem));gap:.25rem;border:1px solid rgba(23,19,13,.12);border-radius:1.2rem;background:#fffaf0;padding:.45rem;box-shadow:0 24px 70px rgba(23,19,13,.18)}
        .kbr-language-switcher__option{display:flex;min-height:2.15rem;align-items:center;justify-content:space-between;border-radius:.9rem;padding:0 .75rem;border:1px solid transparent;color:#17130d;text-decoration:none;font-size:.76rem;font-weight:900;line-height:1}
        .kbr-language-switcher__option:hover{border-color:rgba(201,138,46,.35);background:rgba(201,138,46,.12)}
        .kbr-language-switcher__option[aria-current="true"]{background:#17130d;color:#fffaf0;border-color:#17130d}
        .kbr-language-switcher--admin{margin-inline-start:.5rem}
        .kbr-language-switcher .sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
        html[data-kbr-theme="dark"] .kbr-language-switcher__trigger{background:#fffaf0;color:#17130d;border-color:rgba(255,250,240,.3)}
        html[data-kbr-theme="dark"] .kbr-language-switcher__menu{background:#211a11;border-color:rgba(255,250,240,.16)}
        html[data-kbr-theme="dark"] .kbr-language-switcher__option{color:#fffaf0}
        html[data-kbr-theme="dark"] .kbr-language-switcher__option[aria-current="true"]{background:#fffaf0;color:#17130d}
        @media(max-width:720px){.kbr-language-switcher{width:100%}.kbr-language-switcher__trigger{width:100%;justify-content:center}.kbr-language-switcher__menu{inset-inline:0;width:100%}}
    </style>
@endonce

<details
    class="kbr-language-switcher kbr-language-switcher--{{ $context }}"
    data-language-switcher
    data-language-context="{{ $context }}"
>
    <summary class="kbr-language-switcher__trigger" aria-label="{{ __('kabeeri.language.aria') }}">
        <x-kabeeri-icon name="globe" />
        {{ __('kabeeri.language.label') }}
        <strong>{{ $currentNativeLabel }}</strong>
        <span class="sr-only">- {{ __("kabeeri.language.contexts.{$context}") }}</span>
    </summary>

    <div class="kbr-language-switcher__menu" role="listbox" aria-label="{{ __('kabeeri.language.aria') }}">
        @foreach ($supportedLocales as $locale => $language)
            @if ($locale === $currentLocale)
                <span class="kbr-language-switcher__option" aria-current="true" title="{{ __('kabeeri.language.current') }}">
                    {{ $language['native_label'] }}
                    <small>{{ $language['short_label'] }}</small>
                </span>
            @else
                <a
                    class="kbr-language-switcher__option"
                    href="{{ \App\Support\Localization\KabeeriLocale::localizedUrl($locale, request()->getRequestUri()) }}"
                    hreflang="{{ $locale }}"
                >
                    {{ $language['native_label'] }}
                    <small>{{ $language['short_label'] }}</small>
                </a>
            @endif
        @endforeach
    </div>
</details>
