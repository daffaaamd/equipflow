@props([
    'label' => null,
    'value' => null,
    'icon' => null,
    'trend' => null,
    'trendGood' => true,
    'accent' => 'brand',
    'suffix' => '',
])

@php
    $accents = [
        'brand' => 'text-brand-500',
        'navy' => 'text-navy-600',
        'green' => 'text-green-600',
        'amber' => 'text-amber-600',
        'red' => 'text-red-600',
        'blue' => 'text-blue-600',
    ];

    $namedIcons = [
        'layers' => '<path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>',
        'fleet' => '<path d="M7.5 3.5l9 2.25v12.5l-9 2.25V3.5z"/><path d="M7.5 7.5h9M7.5 12.5h9M7.5 17h9M16.5 6v12M7.5 6v12"/>',
        'activity' => '<path d="M22 12h-4l-3 9L9 3l-3 9H2"/>',
        'trending-up' => '<path d="M23 6l-9.5 9.5-5-5L1 18"/><path d="M17 6h6v6"/>',
        'check' => '<path d="M20 6L9 17l-5-5"/>',
        'clock' => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
        'alert' => '<circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/>',
        'tool' => '<path d="M14.7 6.3a4.5 4.5 0 00-6.1 6.1L3 18l3 3 5.6-5.6a4.5 4.5 0 006.1-6.1L14 13l-3-3 3.7-3.7z"/>',
        'settings' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/>',
        'percent' => '<line x1="19" y1="5" x2="5" y2="19"/><circle cx="6.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/>',
        'file' => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/>',
        'users' => '<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>',
        'user-plus' => '<path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/>',
        'refresh' => '<path d="M23 4v6h-6M1 20v-6h6M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/>',
        'money' => '<path d="M2 6h20v12H2z"/><circle cx="12" cy="12" r="2.5"/><path d="M6 10h.01M18 14h.01"/>',
    ];

    $iconSvg = $icon ? ($namedIcons[$icon] ?? $icon) : null;
@endphp

<div {{ $attributes->merge(['class' => 'card relative overflow-hidden px-5 py-5']) }}>
    <div class="flex items-center justify-between">
        <p class="text-xs font-semibold uppercase tracking-wide text-charcoal-500">{{ $label }}</p>
        @if ($iconSvg)
            <span class="{{ $accents[$accent] ?? $accents['brand'] }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">{!! $iconSvg !!}</svg>
            </span>
        @endif
    </div>
    <p class="mt-2 font-display text-3xl font-semibold tracking-tight text-charcoal-900">
        {{ $value }}{{ $suffix }}
    </p>
    @if ($trend !== null)
        <p @class(['mt-2 flex items-center gap-1 text-xs font-medium', $trendGood ? 'text-green-600' : 'text-red-600'])>
            @if ($trend >= 0)
                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" /></svg>
            @else
                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 4.5l-15 15m0 0h11.25m-11.25 0V8.25" /></svg>
            @endif
            <span>{{ abs($trend) }}% vs prev. period</span>
        </p>
    @endif
</div>
