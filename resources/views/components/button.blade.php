@props([
    'href' => null,
    'type' => 'brand',
    'size' => 'md',
    'variant' => '',
])

@php
    $sizes = ['sm' => 'btn-sm', 'md' => 'btn-md', 'lg' => 'btn-lg'];
    $class = $sizes[$size] ?? $sizes['md'];
    $class .= match ($type) {
        'brand' => ' btn-brand',
        'navy' => ' btn-navy',
        'outline' => ' btn-outline',
        'outline-navy' => ' btn-outline-navy',
        'danger' => ' btn-danger',
        default => ' btn-outline',
    };
    $class .= ' ' . $variant;
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</button>
@endif
