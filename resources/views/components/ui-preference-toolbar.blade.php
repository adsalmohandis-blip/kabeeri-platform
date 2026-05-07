@php($context = $context ?? 'platform_public')
<span class="inline-flex flex-wrap items-center gap-2">
    @include('components.language-switcher', ['context' => $context])
    @include('components.theme-switcher', ['context' => $context])
    @include('components.font-switcher', ['context' => $context])
</span>
