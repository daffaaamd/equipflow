@props(['icon' => 'circle', 'title' => 'No records found', 'description' => 'There is nothing to display here yet.'])

<div class="flex flex-col items-center justify-center px-6 py-14 text-center">
    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-charcoal-100 text-charcoal-400">
        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            @if ($icon === 'search')
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
            @elseif ($icon === 'chart')
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
            @else
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            @endif
        </svg>
    </div>
    <h4 class="mt-4 text-base font-semibold text-charcoal-800">{{ $title }}</h4>
    <p class="mt-1 max-w-md text-sm text-charcoal-500">{{ $description }}</p>
    @if (isset($action))
        <div class="mt-5">{{ $action }}</div>
    @endif
</div>
