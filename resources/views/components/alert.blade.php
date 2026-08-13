@props(['type' => 'success'])

@php
    $map = [
        'success' => 'border-green-200 bg-green-50 text-green-800',
        'error' => 'border-red-200 bg-red-50 text-red-800',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
        'info' => 'border-blue-200 bg-blue-50 text-blue-800',
    ];
@endphp

@if (session()->has('success') || session()->has('error') || $errors->any() || isset($slot) && trim((string) $slot) !== '')
    <div {{ $attributes->merge(['class' => 'border px-4 py-3 text-sm ' . ($map[$type] ?? $map['info'])]) }}>
        @if ($type === 'success' && session('success'))
            <div class="flex items-start gap-2">
                <span class="mt-0.5">✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @elseif ($type === 'error' && session('error'))
            <div class="flex items-start gap-2">
                <span class="mt-0.5">✕</span>
                <span>{{ session('error') }}</span>
            </div>
        @elseif ($type === 'error' && $errors->any())
            <ul class="space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @else
            {{ $slot }}
        @endif
    </div>
@endif
