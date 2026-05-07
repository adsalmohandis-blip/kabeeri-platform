@props([
    'name' => 'sparkles',
    'label' => null,
    'strokeWidth' => 1.8,
])

@php
    $icons = [
        'account' => '<path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/><path d="M4.5 20a7.5 7.5 0 0 1 15 0"/>',
        'admin' => '<path d="M12 3 4.5 6v5.5c0 4.5 3 7.9 7.5 9.5 4.5-1.6 7.5-5 7.5-9.5V6L12 3Z"/><path d="m9.5 12 1.7 1.7 3.8-4"/>',
        'apps' => '<path d="M4 5a1 1 0 0 1 1-1h5v6H4V5Z"/><path d="M14 4h5a1 1 0 0 1 1 1v5h-6V4Z"/><path d="M4 14h6v6H5a1 1 0 0 1-1-1v-5Z"/><path d="M14 14h6v5a1 1 0 0 1-1 1h-5v-6Z"/>',
        'book' => '<path d="M5 4.5A2.5 2.5 0 0 1 7.5 2H20v17H7.5A2.5 2.5 0 0 0 5 21.5v-17Z"/><path d="M5 4.5A2.5 2.5 0 0 0 2.5 7v12.5A2.5 2.5 0 0 1 5 17h15"/>',
        'builder' => '<path d="m14.5 5 4.5 4.5-9.5 9.5H5v-4.5L14.5 5Z"/><path d="m13 6.5 4.5 4.5"/><path d="M4 21h16"/>',
        'chart' => '<path d="M4 19V5"/><path d="M4 19h16"/><path d="M8 16v-4"/><path d="M12 16V8"/><path d="M16 16v-6"/>',
        'check' => '<path d="M20 6 9 17l-5-5"/>',
        'check-circle' => '<path d="M21 11.1V12a9 9 0 1 1-5.3-8.2"/><path d="m9 11 2.4 2.4L20 4.8"/>',
        'code' => '<path d="m8 9-4 3 4 3"/><path d="m16 9 4 3-4 3"/><path d="m14 4-4 16"/>',
        'command' => '<path d="M4 17h16"/><path d="m6 7 4 4-4 4"/><path d="M12 15h4"/>',
        'database' => '<ellipse cx="12" cy="5" rx="7" ry="3"/><path d="M5 5v6c0 1.7 3.1 3 7 3s7-1.3 7-3V5"/><path d="M5 11v6c0 1.7 3.1 3 7 3s7-1.3 7-3v-6"/>',
        'document' => '<path d="M7 3h6l4 4v14H7V3Z"/><path d="M13 3v5h5"/><path d="M9 13h6"/><path d="M9 17h6"/>',
        'external' => '<path d="M14 4h6v6"/><path d="M20 4 10 14"/><path d="M11 5H5v14h14v-6"/>',
        'flag' => '<path d="M5 21V4"/><path d="M5 4h10l-1 4 1 4H5"/>',
        'globe' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3a14 14 0 0 1 0 18"/><path d="M12 3a14 14 0 0 0 0 18"/>',
        'home' => '<path d="m3 11 9-8 9 8"/><path d="M5 10v10h5v-6h4v6h5V10"/>',
        'info' => '<circle cx="12" cy="12" r="9"/><path d="M12 10v6"/><path d="M12 7h.01"/>',
        'login' => '<path d="M10 17 15 12l-5-5"/><path d="M15 12H3"/><path d="M14 4h5a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-5"/>',
        'logout' => '<path d="M14 17 19 12l-5-5"/><path d="M19 12H8"/><path d="M10 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5"/>',
        'mall' => '<path d="M6 8h12l1 13H5L6 8Z"/><path d="M9 8a3 3 0 0 1 6 0"/><path d="M8 12h8"/>',
        'map' => '<path d="m9 18-6 3V6l6-3 6 3 6-3v15l-6 3-6-3Z"/><path d="M9 3v15"/><path d="M15 6v15"/>',
        'moon' => '<path d="M20 14.5A8.5 8.5 0 0 1 9.5 4 7 7 0 1 0 20 14.5Z"/>',
        'partner' => '<path d="M8 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/><path d="M16 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/><path d="M3 20a5 5 0 0 1 10 0"/><path d="M11 20a5 5 0 0 1 10 0"/>',
        'plugin' => '<path d="M8 3v4"/><path d="M16 3v4"/><path d="M7 7h10v5a5 5 0 0 1-10 0V7Z"/><path d="M12 17v4"/>',
        'plus' => '<path d="M12 5v14"/><path d="M5 12h14"/>',
        'pricing' => '<path d="M4 7h16v10H4V7Z"/><path d="M8 11h.01"/><path d="M16 13h.01"/><path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>',
        'product' => '<path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m4 7.5 8 4.5 8-4.5"/><path d="M12 12v9"/>',
        'rocket' => '<path d="M5 15c2.5-6 6.5-10 14-10-1 7.5-4 11.5-10 14l-4-4Z"/><path d="M9 19c-1.5.8-3 1-5 1 .1-2 .3-3.5 1-5"/><path d="M15 9h.01"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>',
        'service' => '<path d="M12 3a5 5 0 0 0-5 5v3a5 5 0 0 0 10 0V8a5 5 0 0 0-5-5Z"/><path d="M5 11H3v3a4 4 0 0 0 4 4h2"/><path d="M19 11h2v3a4 4 0 0 1-4 4h-2"/>',
        'settings' => '<path d="M4 7h16"/><path d="M4 17h16"/><path d="M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/><path d="M16 21a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/>',
        'sparkles' => '<path d="M12 3 13.8 8.2 19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3Z"/><path d="M19 15v4"/><path d="M21 17h-4"/><path d="M5 3v3"/><path d="M6.5 4.5h-3"/>',
        'steps' => '<path d="M6 6h12"/><path d="M6 12h12"/><path d="M6 18h12"/><path d="M3 6h.01"/><path d="M3 12h.01"/><path d="M3 18h.01"/>',
        'store' => '<path d="M4 9h16l-1-5H5L4 9Z"/><path d="M6 9v11h12V9"/><path d="M9 20v-6h6v6"/><path d="M4 9a3 3 0 0 0 6 0"/><path d="M10 9a3 3 0 0 0 6 0"/><path d="M16 9a3 3 0 0 0 6 0"/>',
        'sun' => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/>',
        'theme' => '<path d="M12 21a9 9 0 1 1 8.7-6.8c.4 1.5-.7 2.8-2.2 2.8H16a2 2 0 0 0-2 2c0 1.1-.9 2-2 2Z"/><path d="M7.5 10.5h.01"/><path d="M10 7h.01"/><path d="M14 7h.01"/><path d="M16.5 10.5h.01"/>',
        'trash' => '<path d="M4 7h16"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M6 7l1 14h10l1-14"/><path d="M9 7V4h6v3"/>',
        'trust' => '<path d="M12 3 4.5 6v5.5c0 4.5 3 7.9 7.5 9.5 4.5-1.6 7.5-5 7.5-9.5V6L12 3Z"/><path d="m9 12 2 2 4-5"/>',
        'user-plus' => '<path d="M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M18 8v6"/><path d="M21 11h-6"/>',
    ];

    $icon = $icons[$name] ?? $icons['sparkles'];
    $baseStyle = 'width:1em;height:1em;display:inline-block;flex:none;vertical-align:-0.14em;margin-inline-end:.34em;';
    $style = trim((string) $attributes->get('style', ''));
@endphp

<svg
    {{ $attributes->except('style')->merge(['class' => 'kbr-icon']) }}
    style="{{ $baseStyle }}{{ $style }}"
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="{{ $strokeWidth }}"
    stroke-linecap="round"
    stroke-linejoin="round"
    role="{{ $label ? 'img' : 'presentation' }}"
    aria-hidden="{{ $label ? 'false' : 'true' }}"
>
    @if ($label)
        <title>{{ $label }}</title>
    @endif
    {!! $icon !!}
</svg>
