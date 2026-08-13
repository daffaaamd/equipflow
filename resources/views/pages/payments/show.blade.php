<x-layouts.app>
    @php
        $methodBadges = ['bank_transfer' => 'blue', 'cash' => 'green', 'cheque' => 'amber', 'giro' => 'navy'];
    @endphp

    <x-slot:title>{{ $payment->payment_number }}</x-slot:title>
    <x-slot:subtitle>Payment received from {{ $payment->customer?->company_name }}</x-slot:subtitle>

    <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
        <x-badge type="green" class="!text-sm !px-3 !py-1">Recorded</x-badge>
        <div class="flex gap-2">
            <button data-print class="btn-outline btn-md">Print</button>
            <a href="{{ route('admin.payments.edit', $payment->id) }}" class="btn-outline btn-md">Edit</a>
            <form method="POST" action="{{ route('admin.payments.destroy', $payment->id) }}" data-confirm="Delete this payment record?">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger btn-md">Delete</button>
            </form>
        </div>
    </div>

    <div class="print-area grid gap-5 lg:grid-cols-3">
        <div class="card lg:col-span-2">
            <div class="card-header bg-navy-950">
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Payment Receipt</h3>
            </div>
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-display text-3xl font-bold text-navy-900">{{ $payment->payment_number }}</p>
                        <p class="text-sm text-charcoal-500">Paid {{ $payment->payment_date->format('d M Y') }}</p>
                    </div>
                    <p class="font-display text-3xl font-bold text-green-600">IDR {{ number_format($payment->amount, 0) }}</p>
                </div>

                <dl class="mt-6 grid gap-4 text-sm sm:grid-cols-2">
                    <div class="rounded-lg border border-charcoal-100 bg-charcoal-50 p-4">
                        <dt class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Customer</dt>
                        <dd class="mt-1 font-semibold text-navy-900">{{ $payment->customer?->company_name }}</dd>
                    </div>
                    <div class="rounded-lg border border-charcoal-100 bg-charcoal-50 p-4">
                        <dt class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Invoice</dt>
                        <dd class="mt-1 font-semibold text-navy-900">
                            @if ($payment->invoice)
                                <a href="{{ route('admin.invoices.show', $payment->invoice->id) }}" class="hover:text-brand-500">{{ $payment->invoice->invoice_number }}</a>
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    <div class="rounded-lg border border-charcoal-100 bg-charcoal-50 p-4">
                        <dt class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Method</dt>
                        <dd class="mt-1"><x-badge type="{{ $methodBadges[$payment->method] ?? 'gray' }}">{{ ucfirst(str_replace('_', ' ', $payment->method)) }}</x-badge></dd>
                    </div>
                    <div class="rounded-lg border border-charcoal-100 bg-charcoal-50 p-4">
                        <dt class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Reference</dt>
                        <dd class="mt-1 font-semibold text-navy-900">{{ $payment->reference ?? '—' }}</dd>
                    </div>
                </dl>

                @if ($payment->notes)
                    <div class="mt-4 border border-charcoal-200 bg-charcoal-50 p-4 text-sm">
                        <p class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Notes</p>
                        <p class="mt-1 text-charcoal-700">{{ $payment->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="card p-6">
            <h3 class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Invoice Progress</h3>
            @if ($payment->invoice)
                @php $inv = $payment->invoice; $pct = $inv->total > 0 ? round(($inv->amount_paid / $inv->total) * 100) : 0; @endphp
                <div class="mt-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-charcoal-500">{{ $inv->payment_status }}</span>
                        <span class="font-semibold text-navy-900">{{ $pct }}%</span>
                    </div>
                    <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-charcoal-100">
                        <div class="h-full rounded-full {{ $inv->payment_status === 'paid' ? 'bg-green-500' : 'bg-brand-500' }}" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-charcoal-500">Total</dt><dd class="font-semibold text-navy-900">IDR {{ number_format($inv->total, 0) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-charcoal-500">Paid</dt><dd class="text-green-600">IDR {{ number_format($inv->amount_paid, 0) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-charcoal-500">Balance</dt><dd class="{{ $inv->balance > 0 ? 'text-red-600' : 'text-green-600' }}">IDR {{ number_format($inv->balance, 0) }}</dd></div>
                </dl>
            @else
                <p class="mt-2 text-sm text-charcoal-400">No linked invoice.</p>
            @endif
        </div>
    </div>
</x-layouts.app>