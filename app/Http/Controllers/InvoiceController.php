<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Project;
use App\Services\ActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Invoice::with('customer');

        if ($request->user()->role === 'customer') {
            $query->whereHas('customer', fn ($q) => $q->where('user_id', $request->user()->id));
        }

        $invoices = $query
            ->when($request->search, fn ($q, $s) => $q->where('invoice_number', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $v) => $q->where('payment_status', $v))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $filters = $request->only(['search', 'status']);

        return view('pages.invoices.index', compact('invoices', 'filters'));
    }

    public function create(Request $request): View
    {
        $contracts = Contract::whereIn('status', ['active', 'completed'])->with('customer')->latest()->get();

        return view('pages.invoices.create', compact('contracts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'contract_id' => ['required', 'exists:contracts,id'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:invoice_date'],
            'period_start' => ['nullable', 'date'],
            'period_end' => ['nullable', 'date', 'after_or_equal:period_start'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'tax' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $contract = Contract::findOrFail($validated['contract_id']);

        $invoice = Invoice::create(array_merge($validated, [
            'invoice_number' => 'INV-' . date('Y') . '-' . str_pad((string) (Invoice::count() + 1), 4, '0', STR_PAD_LEFT),
            'customer_id' => $contract->customer_id,
            'project_id' => $contract->project_id,
            'total' => $validated['subtotal'] + $validated['tax'],
            'amount_paid' => 0,
            'payment_status' => 'pending',
        ]));

        ActivityService::log('create', 'invoice', $invoice->id, "Invoice {$invoice->invoice_number} issued");

        if ($contract->customer->user_id) {
            \App\Services\NotificationService::send($contract->customer->user_id, 'Invoice Issued', "Invoice {$invoice->invoice_number} has been issued.", 'info', route('customer.invoices.show', $invoice->id));
        }

        return redirect()->route('admin.invoices.show', $invoice->id)->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load('customer', 'contract', 'project', 'payments');

        return view('pages.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice): View
    {
        $contracts = Contract::whereIn('status', ['active', 'completed'])->with('customer')->latest()->get();

        return view('pages.invoices.edit', compact('invoice', 'contracts'));
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        $validated = $request->validate([
            'invoice_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:invoice_date'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'tax' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $invoice->update(array_merge($validated, ['total' => $validated['subtotal'] + $validated['tax']]));
        $this->refreshPaymentStatus($invoice);
        ActivityService::log('update', 'invoice', $invoice->id, "Invoice {$invoice->invoice_number} updated");

        return redirect()->route('admin.invoices.show', $invoice->id)->with('success', 'Invoice updated.');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        ActivityService::log('delete', 'invoice', $invoice->id, "Invoice {$invoice->invoice_number} removed");
        $invoice->delete();

        return redirect()->route('admin.invoices.index')->with('success', 'Invoice removed.');
    }

    public function refreshPaymentStatus(Invoice $invoice): void
    {
        $paid = (float) $invoice->payments()->sum('amount');
        $invoice->amount_paid = $paid;

        if ($paid <= 0) {
            $invoice->payment_status = $invoice->due_date->lt(now()) ? 'overdue' : 'pending';
        } elseif ($paid >= $invoice->total) {
            $invoice->payment_status = 'paid';
        } else {
            $invoice->payment_status = $invoice->due_date->lt(now()) ? 'overdue' : 'partial';
        }

        $invoice->save();
    }
}
