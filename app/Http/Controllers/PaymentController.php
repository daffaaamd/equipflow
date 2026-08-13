<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\ActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $payments = Payment::with('customer', 'invoice')
            ->when($request->search, fn ($q, $s) => $q->where('payment_number', 'like', "%{$s}%"))
            ->when($request->customer_id, fn ($q, $v) => $q->where('customer_id', $v))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $filters = $request->only(['search', 'customer_id']);
        $customers = Customer::orderBy('company_name')->get();

        return view('pages.payments.index', compact('payments', 'filters', 'customers'));
    }

    public function create(Request $request): View
    {
        $invoices = Invoice::whereIn('payment_status', ['pending', 'partial', 'overdue'])
            ->with('customer')->latest()->get();

        $selectedInvoice = $request->query('invoice') ? Invoice::with('customer')->find($request->query('invoice')) : null;

        return view('pages.payments.create', compact('invoices', 'selectedInvoice'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'invoice_id' => ['required', 'exists:invoices,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_date' => ['required', 'date'],
            'method' => ['required', 'in:bank_transfer,cash,cheque,giro'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $invoice = Invoice::with('payments')->findOrFail($validated['invoice_id']);
        $balance = $invoice->total - $invoice->amount_paid;

        if ($validated['amount'] > $balance) {
            return back()->withErrors(['amount' => "Payment exceeds the outstanding balance of " . number_format($balance) . "."])->withInput();
        }

        $payment = Payment::create(array_merge($validated, [
            'payment_number' => 'PAY-' . date('Y') . '-' . str_pad((string) (Payment::count() + 1), 4, '0', STR_PAD_LEFT),
            'customer_id' => $invoice->customer_id,
        ]));

        app(InvoiceController::class)->refreshPaymentStatus($invoice);

        ActivityService::log('create', 'payment', $payment->id, "Payment {$payment->payment_number} received for {$invoice->invoice_number}");

        return redirect()->route('admin.payments.show', $payment->id)->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment): View
    {
        $payment->load('invoice', 'customer');

        return view('pages.payments.show', compact('payment'));
    }

    public function edit(Payment $payment): View
    {
        $invoices = Invoice::with('customer')->latest()->get();

        return view('pages.payments.edit', compact('payment', 'invoices'));
    }

    public function update(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'payment_date' => ['required', 'date'],
            'method' => ['required', 'in:bank_transfer,cash,cheque,giro'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $payment->update($validated);
        app(InvoiceController::class)->refreshPaymentStatus($payment->invoice);
        ActivityService::log('update', 'payment', $payment->id, "Payment {$payment->payment_number} updated");

        return redirect()->route('admin.payments.show', $payment->id)->with('success', 'Payment updated.');
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        $invoice = $payment->invoice;
        ActivityService::log('delete', 'payment', $payment->id, "Payment {$payment->payment_number} removed");
        $payment->delete();

        if ($invoice) {
            app(InvoiceController::class)->refreshPaymentStatus($invoice);
        }

        return redirect()->route('admin.payments.index')->with('success', 'Payment removed.');
    }
}
