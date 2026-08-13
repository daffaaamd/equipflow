<x-layouts.app>
    @php
        $isCustomer = auth()->user()->role === 'customer';
        $badges = ['pending' => 'amber', 'partial' => 'blue', 'paid' => 'green', 'overdue' => 'red'];
        $methodBadges = ['bank_transfer' => 'blue', 'cash' => 'green', 'cheque' => 'amber', 'giro' => 'navy'];
    @endphp

    <x-slot:title>{{ $invoice->invoice_number }}</x-slot:title>
    <x-slot:subtitle>Invoice for {{ $invoice->customer?->company_name }}</x-slot:subtitle>

    <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
        <x-badge type="{{ $badges[$invoice->payment_status] ?? 'gray' }}" class="!text-sm !px-3 !py-1">{{ ucfirst($invoice->payment_status) }}</x-badge>
        <div class="flex gap-2">
            <button data-print class="btn-outline btn-md">Print</button>
            @if (!$isCustomer)
                <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="btn-outline btn-md">Edit</a>
                <a href="{{ route('admin.payments.create', ['invoice' => $invoice->id]) }}" class="btn-navy btn-md">Record Payment</a>
                <form method="POST" action="{{ route('admin.invoices.destroy', $invoice->id) }}" data-confirm="Delete this invoice?">
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
                    <h2 class="font-display text-3xl font-bold uppercase tracking-tight text-navy-900">Invoice</h2>
                    <p class="mt-1 text-sm text-charcoal-500">{{ $invoice->invoice_number }}</p>
                    <p class="text-sm text-charcoal-500">Issued {{ $invoice->invoice_date->format('d M Y') }}</p>
                </div>
            </div>

            <div class="mt-8 grid gap-6 sm:grid-cols-3">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Billed To</h3>
                    <p class="mt-2 font-semibold text-navy-900">{{ $invoice->customer?->company_name }}</p>
                    <p class="text-sm text-charcoal-500">{{ $invoice->customer?->contact_person }}<br>{{ $invoice->customer?->email }}</p>
                </div>
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Contract</h3>
                    <p class="mt-2 font-semibold text-navy-900">{{ $invoice->contract?->contract_number ?? '—' }}</p>
                    <p class="text-sm text-charcoal-500">Project: {{ $invoice->project?->name ?? '—' }}</p>
                </div>
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Billing Period</h3>
                    <p class="mt-2 font-semibold text-navy-900">{{ $invoice->period_start?->format('d M Y') ?? '—' }} — {{ $invoice->period_end?->format('d M Y') ?? '—' }}</p>
                    <p class="text-sm text-charcoal-500">Due {{ $invoice->due_date->format('d M Y') }}</p>
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-8">
            <div class="ml-auto max-w-xs space-y-2 text-sm">
                <p class="flex justify-between"><span class="text-charcoal-500">Subtotal</span><span>IDR {{ number_format($invoice->subtotal, 0) }}</span></p>
                <p class="flex justify-between"><span class="text-charcoal-500">Tax</span><span>IDR {{ number_format($invoice->tax, 0) }}</span></p>
                <p class="flex justify-between border-t-2 border-navy-900 pt-2 font-display text-lg font-bold text-navy-900"><span>Total</span><span>IDR {{ number_format($invoice->total, 0) }}</span></p>
                <p class="flex justify-between"><span class="text-charcoal-500">Amount Paid</span><span class="text-green-600">- IDR {{ number_format($invoice->amount_paid, 0) }}</span></p>
                <p class="flex justify-between font-semibold"><span class="text-charcoal-500">Balance Due</span><span class="{{ $invoice->balance > 0 ? 'text-red-600' : 'text-green-600' }}">IDR {{ number_format($invoice->balance, 0) }}</span></p>
            </div>

            @if ($invoice->notes)
                <div class="mt-6 border border-charcoal-200 bg-charcoal-50 p-4 text-sm">
                    <p class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Notes</p>
                    <p class="mt-1 text-charcoal-700">{{ $invoice->notes }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="card no-print mt-5">
        <div class="card-header"><h3 class="text-lg font-semibold">Payments</h3><p class="text-sm text-charcoal-500">{{ $invoice->payments->count() }} payment{{ $invoice->payments->count() !== 1 ? 's' : '' }}</p></div>
        <div class="table-wrap">
            <table class="table-base">
                <thead><tr><th>Payment</th><th>Date</th><th>Method</th><th>Reference</th><th class="text-right">Amount</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse ($invoice->payments as $p)
                        <tr>
                            <td class="font-semibold text-navy-900"><a href="{{ route('admin.payments.show', $p->id) }}" class="hover:text-brand-500">{{ $p->payment_number }}</a></td>
                            <td>{{ $p->payment_date->format('d M Y') }}</td>
                            <td><x-badge type="{{ $methodBadges[$p->method] ?? 'gray' }}">{{ ucfirst(str_replace('_', ' ', $p->method)) }}</x-badge></td>
                            <td class="text-xs">{{ $p->reference ?? '—' }}</td>
                            <td class="text-right font-semibold text-green-600">IDR {{ number_format($p->amount, 0) }}</td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.payments.show', $p->id) }}" class="btn-outline btn-sm">View</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-charcoal-400">No payments recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>