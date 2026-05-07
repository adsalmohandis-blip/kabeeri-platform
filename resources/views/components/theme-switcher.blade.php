@php
    $context = $context ?? ($kabeeriUi['context'] ?? 'platform_public');
    $currentTheme = $kabeeriUi['theme'] ?? 'light';
    $themes = config('kabeeri_ui_preferences.themes', []);
    $redirectTarget = request()->getRequestUri();
    $themeIcon = fn (string $theme): string => $theme === 'dark' ? 'moon' : 'sun';
@endphp

@include('components.theme-foundation')

<div
    class="kbr-theme-toggle"
    data-theme-switcher
    data-theme-context="{{ $context }}"
    role="group"
    aria-label="{{ __('kabeeri.ui.theme_mode') }}"
>
    @foreach ($themes as $theme => $item)
        @php($themeLabel = __("kabeeri.ui.theme_mode_{$theme}"))
        @if ($theme === $currentTheme)
            <span
                class="kbr-theme-toggle__button is-active"
                data-theme-option="{{ $theme }}"
                aria-current="true"
                title="{{ $themeLabel }}"
            >
                <x-kabeeri-icon :name="$themeIcon($theme)" style="margin-inline-end:0" />
                <span class="sr-only">{{ $themeLabel }}</span>
            </span>
        @else
            <a
                class="kbr-theme-toggle__button"
                href="{{ route('ui.theme', ['theme' => $theme, 'redirect' => $redirectTarget]) }}"
                data-theme-option="{{ $theme }}"
                aria-label="{{ $themeLabel }}"
                title="{{ $themeLabel }}"
            >
                <x-kabeeri-icon :name="$themeIcon($theme)" style="margin-inline-end:0" />
                <span class="sr-only">{{ $themeLabel }}</span>
            </a>
        @endif
    @endforeach
</div>
