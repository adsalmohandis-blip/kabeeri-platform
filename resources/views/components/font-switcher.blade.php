@php
    $context = $context ?? ($kabeeriUi['context'] ?? 'customer');
    $currentFont = $kabeeriUi['font'] ?? config('kabeeri_ui_preferences.default_font');
    $fonts = config('kabeeri_ui_preferences.fonts', []);
    $redirectTarget = request()->getRequestUri();
    $currentFontLabelKey = 'font_'.str_replace('-', '_', $currentFont);
@endphp

@if ($context !== 'visitor' && $context !== 'platform_public')
    <details class="kbr-language-switcher kbr-language-switcher--font" data-font-switcher data-font-context="{{ $context }}">
        <summary class="kbr-language-switcher__trigger" aria-label="{{ __('kabeeri.ui.font') }}">
            <x-kabeeri-icon name="document" />
            <strong>{{ __("kabeeri.ui.{$currentFontLabelKey}") }}</strong>
        </summary>
        <div class="kbr-language-switcher__menu" role="listbox" aria-label="{{ __('kabeeri.ui.font') }}">
            @foreach ($fonts as $font => $item)
                @php($fontLabelKey = 'font_'.str_replace('-', '_', $font))
                @if ($font === $currentFont)
                    <span class="kbr-language-switcher__option" aria-current="true">{{ __("kabeeri.ui.{$fontLabelKey}") }}</span>
                @else
                    <a class="kbr-language-switcher__option" href="{{ route('ui.font', ['font' => $font, 'redirect' => $redirectTarget]) }}">{{ __("kabeeri.ui.{$fontLabelKey}") }}</a>
                @endif
            @endforeach
        </div>
    </details>
@endif
