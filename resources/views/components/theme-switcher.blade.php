@php
    $context = $context ?? ($kabeeriUi['context'] ?? 'platform_public');
    $currentTheme = $kabeeriUi['theme'] ?? 'light';
    $themes = config('kabeeri_ui_preferences.themes', []);
    $redirectTarget = request()->getRequestUri();
    $currentThemeLabel = __("kabeeri.ui.theme_mode_{$currentTheme}");
@endphp

<details class="kbr-language-switcher kbr-language-switcher--theme" data-theme-switcher data-theme-context="{{ $context }}">
    <summary class="kbr-language-switcher__trigger" aria-label="{{ __('kabeeri.ui.theme_mode') }}">
        <x-kabeeri-icon name="settings" />
        <strong>{{ $currentThemeLabel }}</strong>
    </summary>
    <div class="kbr-language-switcher__menu" role="listbox" aria-label="{{ __('kabeeri.ui.theme_mode') }}">
        @foreach ($themes as $theme => $item)
            @php($themeLabel = __("kabeeri.ui.theme_mode_{$theme}"))
            @if ($theme === $currentTheme)
                <span class="kbr-language-switcher__option" aria-current="true">{{ $themeLabel }}</span>
            @else
                <a class="kbr-language-switcher__option" href="{{ route('ui.theme', ['theme' => $theme, 'redirect' => $redirectTarget]) }}">{{ $themeLabel }}</a>
            @endif
        @endforeach
    </div>
</details>
