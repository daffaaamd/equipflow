@props(['title' => null, 'maxWidth' => 'lg', 'show' => false])

@php
    $widths = ['sm' => 'max-w-md', 'md' => 'max-w-lg', 'lg' => 'max-w-2xl', 'xl' => 'max-w-4xl'];
@endphp

<div {{ $attributes->merge(['class' => 'relative z-50']) }} x-data="{ open: @js($show) }" x-show="open" x-cloak>
    <div class="fixed inset-0 bg-charcoal-950/60" x-on:click="open = false"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4" x-show="open" x-transition.opacity>
        <div class="w-full {{ $widths[$maxWidth] }} bg-white shadow-xl" x-on:click.stop>
            @if ($title)
                <div class="flex items-center justify-between border-b border-charcoal-200 px-5 py-4">
                    <h3 class="text-base font-semibold">{{ $title }}</h3>
                    <button type="button" class="text-charcoal-400 hover:text-charcoal-600" x-on:click="open = false">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            @endif
            <div class="p-5">{{ $slot }}</div>
        </div>
    </div>
</div>
