@php
    $context = $context ?? 'visitor';
    $currentLocale = \App\Support\Localization\KabeeriLocale::current();
    $supportedLocales = \App\Support\Localization\KabeeriLocale::supported();
    $currentNativeLabel = $supportedLocales[$currentLocale]['native_label'] ?? $currentLocale;
@endphp

@include('components.theme-foundation')

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
