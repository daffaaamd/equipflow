<x-layouts.app>
    @php
        $badges = ['scheduled' => 'blue', 'preparing' => 'amber', 'in_transit' => 'navy', 'delivered' => 'green', 'confirmed' => 'green'];
    @endphp

    <x-slot:title>{{ $delivery->delivery_number }}</x-slot:title>
    <x-slot:subtitle>Delivery for {{ $delivery->customer?->company_name }}</x-slot:subtitle>

    <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
        <x-badge type="{{ $badges[$delivery->status] ?? 'gray' }}" class="!text-sm !px-3 !py-1">{{ ucfirst(str_replace('_', ' ', $delivery->status)) }}</x-badge>
        <div class="flex gap-2">
            <button data-print class="btn-outline btn-md">Print</button>
            <a href="{{ route('admin.deliveries.edit', $delivery->id) }}" class="btn-outline btn-md">Edit</a>
            <form method="POST" action="{{ route('admin.deliveries.destroy', $delivery->id) }}" data-confirm="Delete this delivery?">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger btn-md">Delete</button>
            </form>
        </div>
    </div>

    <div class="print-area card">
        <div class="border-b-4 border-brand-500 p-6 sm:p-8">
            <div class="flex flex-wrap items-start justify-between gap-6">
                <div>
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-10 w-10 items-center justify-center bg-brand-500 font-display text-xl font-bold text-white">E</span>
                        <span class="font-display text-2xl font-bold uppercase tracking-wide text-navy-900">Equip<span class="text-brand-500">Flow</span></span>
                    </div>
                    <p class="mt-3 text-sm text-charcoal-500">Menara EquipFlow Lt. 18, Jl. Jend. Sudirman Kav. 52-53<br>Jakarta Selatan 12190 · +62 21 5050 1800</p>
                </div>
                <div class="text-right">
                    <h2 class="font-display text-3xl font-bold uppercase tracking-tight text-navy-900">Delivery Order</h2>
                    <p class="mt-1 text-sm text-charcoal-500">{{ $delivery->delivery_number }}</p>
                    <p class="text-sm text-charcoal-500">{{ $delivery->delivery_date->format('d M Y') }}</p>
                </div>
            </div>
        </div>

        <div class="grid gap-6 p-6 sm:grid-cols-2 sm:p-8">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Customer</h3>
                <p class="mt-2 font-semibold text-navy-900">{{ $delivery->customer?->company_name }}</p>
                <p class="text-sm text-charcoal-500">{{ $delivery->customer?->contact_person }}<br>{{ $delivery->customer?->email }}</p>
            </div>
            <div>
                <h3 class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Route</h3>
                <p class="mt-2 text-sm text-charcoal-700"><strong>From:</strong> {{ $delivery->pickup_location ?? 'EquipFlow Depot' }}</p>
                <p class="text-sm text-charcoal-700"><strong>To:</strong> {{ $delivery->destination ?? '—' }}</p>
            </div>
            <div>
                <h3 class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Equipment</h3>
                <p class="mt-2 font-semibold text-navy-900">{{ $delivery->equipment?->name }}</p>
                <p class="text-sm text-charcoal-500">{{ $delivery->equipment?->equipment_code }}</p>
            </div>
            <div>
                <h3 class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Transport</h3>
                <p class="mt-2 text-sm text-charcoal-700">{{ $delivery->transport_vehicle ?? '—' }} · {{ $delivery->plate_number ?? '' }}</p>
                <p class="text-sm text-charcoal-500">{{ $delivery->driver_name ?? '—' }} {{ $delivery->driver_phone ? '· ' . $delivery->driver_phone : '' }}</p>
                <p class="text-sm text-charcoal-500">ETA: {{ $delivery->estimated_arrival?->format('d M Y') ?? '—' }}</p>
            </div>
        </div>
    </div>

    @if ($delivery->notes)
        <div class="card mt-5 p-6 text-sm">
            <p class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Notes</p>
            <p class="mt-1 text-charcoal-700">{{ $delivery->notes }}</p>
        </div>
    @endif

    <div class="card no-print mt-5">
        <div class="card-header"><h3 class="text-lg font-semibold">Update Delivery Status</h3></div>
        <form method="POST" action="{{ route('admin.deliveries.update', $delivery->id) }}" class="flex flex-wrap items-end gap-4 p-6">
            @csrf @method('PUT')
            <div class="w-52">
                <x-field label="Status" name="status">
                    <select name="status" class="input">
                        @foreach (['scheduled' => 'Scheduled', 'preparing' => 'Preparing', 'in_transit' => 'In Transit', 'delivered' => 'Delivered', 'confirmed' => 'Confirmed'] as $val => $label)
                            <option value="{{ $val }}" @selected($delivery->status === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </x-field>
            </div>
            <button type="submit" class="btn-navy btn-md">Update</button>
        </form>
    </div>
</x-layouts.app>