@props(['title' => null, 'description' => null, 'actions' => null, 'padded' => true])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if ($title || $actions)
        <div class="card-header flex flex-wrap items-center justify-between gap-3">
            <div>
                @if ($title)
                    <h3 class="text-lg font-semibold">{{ $title }}</h3>
                @endif
                @if ($description)
                    <p class="mt-0.5 text-sm text-charcoal-500">{{ $description }}</p>
                @endif
            </div>
            @if ($actions)
                <div class="flex items-center gap-2">{{ $actions }}</div>
            @endif
        </div>
    @endif
    <div @class(['px-5 py-5' => $padded, 'p-0' => !$padded])>
        {{ $slot }}
    </div>
</div>
