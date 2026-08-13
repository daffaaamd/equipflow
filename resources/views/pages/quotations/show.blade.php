<x-layouts.app>
    @php
        $isCustomer = auth()->user()->role === 'customer';
        $badges = ['sent' => 'blue', 'accepted' => 'green', 'revision' => 'amber', 'rejected' => 'red', 'expired' => 'gray'];
        $respondRoute = $isCustomer ? route('customer.quotations.respond', $quotation->id) : route('admin.quotations.update', $quotation->id);
    @endphp

    <x-slot:title>{{ $quotation->quotation_number }}</x-slot:title>
    <x-slot:subtitle>Quotation for {{ $quotation->customer?->company_name }}</x-slot:subtitle>

    <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
        <div class="flex gap-2">
            <x-badge type="{{ $badges[$quotation->status] ?? 'gray' }}" class="!text-sm !px-3 !py-1">{{ ucfirst($quotation->status) }}</x-badge>
            @if ($quotation->contract)
                <x-badge type="green" class="!text-sm !px-3 !py-1">Converted to {{ $quotation->contract->contract_number }}</x-badge>
            @endif
        </div>
        @if (!$isCustomer)
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.quotations.edit', $quotation->id) }}" class="btn-outline btn-md">Edit</a>
                @if (in_array($quotation->status, ['sent', 'accepted']) && !$quotation->contract)
                    <a href="{{ route('admin.quotations.contract', $quotation->id) }}" class="btn-brand btn-md">Generate Contract</a>
                @endif
                <form method="POST" action="{{ route('admin.quotations.destroy', $quotation->id) }}" data-confirm="Delete this quotation?">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger btn-md">Delete</button>
                </form>
            </div>
        @endif
    </div>

    <div class="print-area card">
        <div class="border-b-4 border-brand-500 bg-white p-6 sm:p-8">
            <div class="flex flex-wrap items-start justify-between gap-6">
                <div>
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-10 w-10 items-center justify-center bg-brand-500 font-display text-xl font-bold text-white">E</span>
                        <span class="font-display text-2xl font-bold uppercase tracking-wide text-navy-900">Equip<span class="text-brand-500">Flow</span></span>
                    </div>
                    <p class="mt-3 text-sm text-charcoal-500">Menara EquipFlow Lt. 18, Jl. Jend. Sudirman Kav. 52-53<br>Jakarta Selatan 12190 · +62 21 5050 1800</p>
                </div>
                <div class="text-right">
                    <h2 class="font-display text-3xl font-bold uppercase tracking-tight text-navy-900">Quotation</h2>
                    <p class="mt-1 text-sm text-charcoal-500">{{ $quotation->quotation_number }}</p>
                    <p class="text-sm text-charcoal-500">Valid until {{ $quotation->valid_until?->format('d M Y') }}</p>
                </div>
            </div>

            <div class="mt-8 grid gap-6 sm:grid-cols-2">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Quoted To</h3>
                    <p class="mt-2 font-semibold text-navy-900">{{ $quotation->customer?->company_name }}</p>
                    <p class="text-sm text-charcoal-500">{{ $quotation->customer?->contact_person }}<br>{{ $quotation->customer?->email }}</p>
                </div>
                <div class="sm:text-right">
                    <h3 class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Rental Period</h3>
                    <p class="mt-2 font-semibold text-navy-900">
                        {{ $quotation->rental_period_start?->format('d M Y') }} — {{ $quotation->rental_period_end?->format('d M Y') }}
                    </p>
                    <p class="text-sm text-charcoal-500">{{ $quotation->duration_days }} days</p>
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-8">
            <div class="table-wrap">
                <table class="table-base">
                    <thead><tr><th>Description</th><th>Qty</th><th>Rate (IDR)</th><th>Days</th><th class="text-right">Amount (IDR)</th></tr></thead>
                    <tbody>
                        @foreach ($quotation->items as $item)
                            <tr>
                                <td class="font-semibold text-navy-900">{{ $item->equipment_name_snapshot }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->unit_rate, 0) }}</td>
                                <td>{{ $item->duration_days }}</td>
                                <td class="text-right font-semibold">{{ number_format($item->line_total, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 ml-auto max-w-xs space-y-2 text-sm">
                <p class="flex justify-between"><span class="text-charcoal-500">Rental Rate</span><span>IDR {{ number_format($quotation->rental_rate, 0) }}</span></p>
                @if ($quotation->operator_cost > 0)
                    <p class="flex justify-between"><span class="text-charcoal-500">Operator</span><span>IDR {{ number_format($quotation->operator_cost, 0) }}</span></p>
                @endif
                @if ($quotation->transportation_cost > 0)
                    <p class="flex justify-between"><span class="text-charcoal-500">Transportation</span><span>IDR {{ number_format($quotation->transportation_cost, 0) }}</span></p>
                @endif
                @if ($quotation->fuel_cost > 0)
                    <p class="flex justify-between"><span class="text-charcoal-500">Fuel</span><span>IDR {{ number_format($quotation->fuel_cost, 0) }}</span></p>
                @endif
                @if ($quotation->additional_service_cost > 0)
                    <p class="flex justify-between"><span class="text-charcoal-500">Additional Services</span><span>IDR {{ number_format($quotation->additional_service_cost, 0) }}</span></p>
                @endif
                @if ($quotation->discount > 0)
                    <p class="flex justify-between"><span class="text-charcoal-500">Discount</span><span class="text-red-600">- IDR {{ number_format($quotation->discount, 0) }}</span></p>
                @endif
                <p class="flex justify-between border-t border-charcoal-200 pt-2"><span class="text-charcoal-500">Subtotal</span><span>IDR {{ number_format($quotation->subtotal, 0) }}</span></p>
                <p class="flex justify-between"><span class="text-charcoal-500">Tax ({{ $quotation->tax_rate }}%)</span><span>IDR {{ number_format($quotation->tax_amount, 0) }}</span></p>
                <p class="flex justify-between border-t-2 border-navy-900 pt-2 font-display text-lg font-bold text-navy-900"><span>Grand Total</span><span>IDR {{ number_format($quotation->grand_total, 0) }}</span></p>
            </div>

            @if ($quotation->notes)
                <div class="mt-6 border border-charcoal-200 bg-charcoal-50 p-4 text-sm">
                    <p class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Notes</p>
                    <p class="mt-1 text-charcoal-700">{{ $quotation->notes }}</p>
                </div>
            @endif
        </div>
    </div>

    @if (in_array($quotation->status, ['sent', 'revision']))
        <div class="card mt-5 p-6">
            <h3 class="text-lg font-semibold">Respond to this Quotation</h3>
            <div class="mt-4 flex flex-wrap gap-3">
                <form method="POST" action="{{ $respondRoute }}">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="accepted">
                    <button type="submit" class="btn-brand btn-md" data-confirm="Accept this quotation?">Accept Quotation</button>
                </form>
                <form method="POST" action="{{ $respondRoute }}">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="revision">
                    <button type="submit" class="btn-outline btn-md">Request Revision</button>
                </form>
                <form method="POST" action="{{ $respondRoute }}">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="rejected">
                    <button type="submit" class="btn-danger btn-md" data-confirm="Reject this quotation?">Reject</button>
                </form>
            </div>
        </div>
    @endif
</x-layouts.app>