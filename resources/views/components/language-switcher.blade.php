@php
    $context = $context ?? 'visitor';
    $currentLocale = \App\Support\Localization\KabeeriLocale::current();
    $supportedLocales = \App\Support\Localization\KabeeriLocale::supported();
    $redirectTarget = request()->getRequestUri();
@endphp

@once
    <style>
        .kbr-language-switcher{display:inline-flex;align-items:center;gap:.35rem;flex-wrap:wrap;border:1px solid rgba(23,19,13,.14);border-radius:999px;background:rgba(255,250,240,.76);padding:.25rem;box-shadow:0 12px 32px rgba(23,19,13,.10);font-family:"IBM Plex Sans Arabic","Almarai",sans-serif}
        .kbr-language-switcher__label{display:inline-flex;align-items:center;gap:.2rem;padding:0 .55rem;color:#17130d;font-size:.72rem;font-weight:900;white-space:nowrap}
        .kbr-language-switcher__option{display:inline-flex;min-height:2rem;align-items:center;justify-content:center;border-radius:999px;padding:0 .7rem;border:1px solid transparent;color:#17130d;text-decoration:none;font-size:.74rem;font-weight:900;line-height:1}
        .kbr-language-switcher__option:hover{border-color:rgba(201,138,46,.45);background:rgba(201,138,46,.14)}
        .kbr-language-switcher__option[aria-current="true"]{background:#17130d;color:#fffaf0;border-color:#17130d}
        .kbr-language-switcher--admin{margin-inline-start:.5rem;background:#fffaf0;border-color:rgba(23,19,13,.18)}
        .kbr-language-switcher--customer{background:rgba(255,255,255,.68)}
        .kbr-language-switcher .sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
        @media(max-width:720px){.kbr-language-switcher{width:100%;justify-content:center;border-radius:1rem}.kbr-language-switcher__option{flex:1}}
    </style>
@endonce

<div
    class="kbr-language-switcher kbr-language-switcher--{{ $context }}"
    data-language-switcher
    data-language-context="{{ $context }}"
    role="group"
    aria-label="{{ __('kabeeri.language.aria') }}"
>
    <span class="kbr-language-switcher__label">
        <x-kabeeri-icon name="globe" />
        {{ __('kabeeri.language.label') }}
        <span class="sr-only">- {{ __("kabeeri.language.contexts.{$context}") }}</span>
    </span>

    @foreach ($supportedLocales as $locale => $language)
        @if ($locale === $currentLocale)
            <span class="kbr-language-switcher__option" aria-current="true" title="{{ __('kabeeri.language.current') }}">
                {{ $language['short_label'] }}
            </span>
        @else
            <a
                class="kbr-language-switcher__option"
                href="{{ route('language.switch', ['locale' => $locale, 'redirect' => $redirectTarget]) }}"
                hreflang="{{ $locale }}"
                rel="nofollow"
            >
                {{ $language['short_label'] }}
            </a>
        @endif
    @endforeach
</div>
