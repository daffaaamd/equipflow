<x-layouts.app>
    @php
        $isCustomer = auth()->user()->role === 'customer';
        $badges = ['draft' => 'gray', 'active' => 'green', 'completed' => 'navy', 'terminated' => 'red'];
        $invBadges = ['pending' => 'amber', 'partial' => 'blue', 'paid' => 'green', 'overdue' => 'red'];
        $dlvBadges = ['scheduled' => 'blue', 'preparing' => 'amber', 'in_transit' => 'navy', 'delivered' => 'green', 'confirmed' => 'green'];
    @endphp

    <x-slot:title>{{ $contract->contract_number }}</x-slot:title>
    <x-slot:subtitle>Contract with {{ $contract->customer?->company_name }}</x-slot:subtitle>

    <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
        <div class="flex gap-2">
            <x-badge type="{{ $badges[$contract->status] ?? 'gray' }}" class="!text-sm !px-3 !py-1">{{ ucfirst($contract->status) }}</x-badge>
            @if ($contract->signed_at)
                <x-badge type="outline" class="!text-sm !px-3 !py-1">Signed {{ $contract->signed_at->format('d M Y') }}</x-badge>
            @endif
        </div>
        <div class="flex flex-wrap gap-2">
            <button data-print class="btn-outline btn-md">Print</button>
            @if (!$isCustomer)
                <a href="{{ route('admin.contracts.edit', $contract->id) }}" class="btn-outline btn-md">Edit</a>
                <a href="{{ route('admin.deliveries.create') }}" class="btn-navy btn-md">Schedule Delivery</a>
                <form method="POST" action="{{ route('admin.contracts.destroy', $contract->id) }}" data-confirm="Delete this contract?">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger btn-md">Delete</button>
                </form>
            @endif
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
                    <h2 class="font-display text-3xl font-bold uppercase tracking-tight text-navy-900">Rental Contract</h2>
                    <p class="mt-1 text-sm text-charcoal-500">{{ $contract->contract_number }}</p>
                    <p class="text-sm text-charcoal-500">Terms: {{ $contract->payment_terms }}</p>
                </div>
            </div>

            <div class="mt-8 grid gap-6 sm:grid-cols-3">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Customer</h3>
                    <p class="mt-2 font-semibold text-navy-900">{{ $contract->customer?->company_name }}</p>
                    <p class="text-sm text-charcoal-500">{{ $contract->customer?->contact_person }}<br>{{ $contract->customer?->email }}</p>
                </div>
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Rental Period</h3>
                    <p class="mt-2 font-semibold text-navy-900">{{ $contract->start_date->format('d M Y') }} — {{ $contract->end_date->format('d M Y') }}</p>
                    <p class="text-sm text-charcoal-500">{{ $contract->duration_days }} days</p>
                </div>
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Contract Value</h3>
                    <p class="mt-2 font-display text-2xl font-bold text-brand-600">IDR {{ number_format($contract->contract_value, 0) }}</p>
                    <p class="text-sm text-charcoal-500">Deposit: IDR {{ number_format($contract->deposit, 0) }}</p>
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-8">
            <div class="table-wrap">
                <table class="table-base">
                    <thead><tr><th>Equipment</th><th>Qty</th><th>Rate (IDR)</th><th>Days</th><th class="text-right">Amount (IDR)</th></tr></thead>
                    <tbody>
                        @forelse ($contract->items as $item)
                            <tr>
                                <td class="font-semibold text-navy-900">
                                    @if ($item->equipment)
                                        <a href="{{ route('admin.equipment.show', $item->equipment_id) }}" class="hover:text-brand-500">{{ $item->equipment->name }}</a>
                                        <span class="block text-xs text-charcoal-500">{{ $item->equipment->equipment_code }}</span>
                                    @else
                                        Equipment #{{ $item->equipment_id }}
                                    @endif
                                </td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->unit_rate, 0) }}</td>
                                <td>{{ $item->duration_days }}</td>
                                <td class="text-right font-semibold">{{ number_format($item->line_total, 0) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-charcoal-400">No contract items</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($contract->notes)
                <div class="mt-6 border border-charcoal-200 bg-charcoal-50 p-4 text-sm">
                    <p class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Notes</p>
                    <p class="mt-1 text-charcoal-700">{{ $contract->notes }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Related --}}
    <div class="mt-5 grid gap-5 lg:grid-cols-2">
        <div class="card no-print">
            <div class="card-header flex items-center justify-between">
                <div><h3 class="text-lg font-semibold">Invoices</h3><p class="text-sm text-charcoal-500">{{ $contract->invoices->count() }} invoices</p></div>
                @if (!$isCustomer)
                    <a href="{{ route('admin.invoices.create') }}" class="btn-outline btn-sm">Issue Invoice</a>
                @endif
            </div>
            <div class="table-wrap">
                <table class="table-base">
                    <thead><tr><th>Invoice</th><th>Total</th><th>Paid</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($contract->invoices as $inv)
                            <tr>
                                <td class="font-semibold text-navy-900"><a href="{{ route(($isCustomer ? 'customer' : 'admin') . '.invoices.show', $inv->id) }}" class="hover:text-brand-500">{{ $inv->invoice_number }}</a></td>
                                <td>IDR {{ number_format($inv->total, 0) }}</td>
                                <td>IDR {{ number_format($inv->amount_paid, 0) }}</td>
                                <td><x-badge type="{{ $invBadges[$inv->payment_status] ?? 'gray' }}">{{ ucfirst($inv->payment_status) }}</x-badge></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-charcoal-400">No invoices</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card no-print">
            <div class="card-header"><h3 class="text-lg font-semibold">Deliveries</h3><p class="text-sm text-charcoal-500">{{ $contract->deliveries->count() }} deliveries</p></div>
            <div class="table-wrap">
                <table class="table-base">
                    <thead><tr><th>Delivery</th><th>Equipment</th><th>Date</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($contract->deliveries as $d)
                            <tr>
                                <td class="font-semibold text-navy-900">
                                    @if ($isCustomer)
                                        {{ $d->delivery_number }}
                                    @else
                                        <a href="{{ route('admin.deliveries.show', $d->id) }}" class="hover:text-brand-500">{{ $d->delivery_number }}</a>
                                    @endif
                                </td>
                                <td>{{ $d->equipment?->name }}</td>
                                <td>{{ $d->delivery_date->format('d M Y') }}</td>
                                <td><x-badge type="{{ $dlvBadges[$d->status] ?? 'gray' }}">{{ ucfirst(str_replace('_', ' ', $d->status)) }}</x-badge></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-charcoal-400">No deliveries</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if (!$isCustomer)
        <div class="card no-print mt-5">
            <div class="card-header"><h3 class="text-lg font-semibold">Update Contract</h3></div>
            <form method="POST" action="{{ route('admin.contracts.update', $contract->id) }}" class="flex flex-wrap items-end gap-4 p-6">
                @csrf @method('PUT')
                <div class="w-56">
                    <x-field label="Status" name="status">
                        <select name="status" class="input">
                            @foreach (['draft', 'active', 'completed', 'terminated'] as $val)
                                <option value="{{ $val }}" @selected($contract->status === $val)>{{ ucfirst($val) }}</option>
                            @endforeach
                        </select>
                    </x-field>
                </div>
                <div class="min-w-64 flex-1">
                    <x-field label="Notes" name="notes">
                        <input type="text" name="notes" class="input" value="{{ old('notes', $contract->notes) }}">
                    </x-field>
                </div>
                <button type="submit" class="btn-navy btn-md">Update</button>
            </form>
        </div>
    @endif
</x-layouts.app>