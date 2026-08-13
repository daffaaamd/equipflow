@props(['type' => 'gray'])

@php
    $map = [
        'gray' => 'bg-charcoal-100 text-charcoal-600',
        'navy' => 'bg-navy-100 text-navy-800',
        'brand' => 'bg-brand-100 text-brand-700',
        'green' => 'bg-green-100 text-green-700',
        'red' => 'bg-red-100 text-red-700',
        'amber' => 'bg-amber-100 text-amber-700',
        'blue' => 'bg-blue-100 text-blue-700',
        'purple' => 'bg-purple-100 text-purple-700',
        'outline' => 'border border-charcoal-300 text-charcoal-600 bg-white',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'badge ' . ($map[$type] ?? $map['gray'])]) }}>
    {{ $slot }}
</span>
