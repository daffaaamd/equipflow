@props(['title' => null, 'subtitle' => null, 'id' => null])

<div {{ $attributes->merge(['class' => 'card']) }}>
    <div class="card-header">
        @if ($title)
            <h3 class="text-base font-semibold">{{ $title }}</h3>
        @endif
        @if ($subtitle)
            <p class="mt-0.5 text-sm text-charcoal-500">{{ $subtitle }}</p>
        @endif
    </div>
    <div class="px-4 py-5">
        <div class="relative w-full" style="position: relative; height: 280px; min-height: 280px; width: 100%;">
            @if ($slot->isNotEmpty())
                {{ $slot }}
            @else
                <canvas id="{{ $id ?? 'chart-' . Str::random(8) }}"></canvas>
            @endif
        </div>
    </div>
</div>
