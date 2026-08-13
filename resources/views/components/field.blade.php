@props(['label' => null, 'name' => null, 'required' => false, 'error' => null, 'hint' => null])

<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $name }}" class="label">
            {{ $label }} @if ($required) <span class="text-red-500">*</span> @endif
        </label>
    @endif
    {{ $slot }}
    @if ($hint)
        <p class="mt-1 text-xs text-charcoal-400">{{ $hint }}</p>
    @endif
    @if ($error)
        <p class="mt-1 text-xs text-red-600">{{ $error }}</p>
    @endif
</div>
