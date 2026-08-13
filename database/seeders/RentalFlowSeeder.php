<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\ContractItem;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\EquipmentUtilization;
use App\Models\Invoice;
use App\Models\MaintenanceRecord;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\RentalRequest;
use App\Models\RentalRequestItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RentalFlowSeeder extends Seeder
{
    private array $weightMap = [
        'Hydraulic Excavator' => 30, 'Mini Excavator' => 6, 'Bulldozer' => 12,
        'Wheel Loader' => 10, 'Skid Steer Loader' => 4, 'Dump Truck' => 18,
        'Articulated Dump Truck' => 7, 'Crawler Crane' => 5, 'Mobile Crane' => 6,
        'Tower Crane' => 4, 'Truck-Mounted Crane' => 4, 'Forklift' => 8,
        'Telehandler' => 5, 'Reach Stacker' => 3, 'Motor Grader' => 7,
        'Vibro Roller' => 5, 'Road Roller' => 3, 'Asphalt Paver' => 3,
        'Soil Compactor' => 4, 'Hydraulic Rock Breaker' => 4, 'Backhoe Loader' => 8,
        'Water Tanker' => 4, 'Fuel Tanker' => 3, 'Lowbed Trailer' => 4,
        'Pile Driver' => 3, 'Concrete Pump' => 3,
    ];

    public function run(): void
    {
        $this->command->info('Seeding rental business flow...');

        // Clean slate for flow tables to ensure full idempotency
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            RentalRequestItem::truncate();
            RentalRequest::truncate();
            QuotationItem::truncate();
            Quotation::truncate();
            ContractItem::truncate();
            Contract::truncate();
            Delivery::truncate();
            EquipmentUtilization::truncate();
            MaintenanceRecord::truncate();
            Payment::truncate();
            Invoice::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } catch (\Throwable $e) {
            // Fallback for sqlite or environments without mysql foreign key statement
        }

        $customers = Customer::all();
        $categories = EquipmentCategory::all()->keyBy('name');
        $equipmentByCategory = Equipment::all()->groupBy('category_id');
        $equipmentAll = Equipment::all();
        $projects = Project::all();

        $customerWeight = ['strategic' => 6, 'high_value' => 4, 'medium_value' => 2, 'low_value' => 1];

        // ============================================================
        // 1. RENTAL REQUESTS (~320) spread over 24 months, growth trend
        // ============================================================
        $requestSequence = 1;
        $quotedEntries = [];

        for ($m = 0; $m < 24; $m++) {
            $monthDate = now()->subMonths(23 - $m)->startOfMonth();
            $monthCount = (int) round(6 + $m * 0.5 + random_int(0, 3));

            for ($i = 0; $i < $monthCount; $i++) {
                $customer = $this->weightedCustomer($customers, $customerWeight);
                $createdAt = $monthDate->copy()->addDays(random_int(0, 25))->addHours(random_int(8, 16));

                $itemCount = random_int(1, 100) <= 15 ? random_int(2, 3) : 1;
                $items = [];
                for ($k = 0; $k < $itemCount; $k++) {
                    $category = $this->weightedCategory($categories);
                    $equipment = $this->pickEquipment($equipmentByCategory, $category, $k === 0);
                    $quantity = random_int(1, 3);
                    $startDate = $createdAt->copy()->addDays(random_int(7, 30));
                    $duration = $this->durationForCategory($category->name);
                    $endDate = $startDate->copy()->addDays($duration);

                    $items[] = [
                        'category' => $category,
                        'equipment' => $equipment,
                        'quantity' => $quantity,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ];
                }

                $roll = random_int(1, 100);
                if ($roll <= 7) {
                    $status = 'rejected';
                } elseif ($roll <= 11) {
                    $status = 'cancelled';
                } elseif ($roll <= 20) {
                    $status = 'pending';
                } elseif ($roll <= 27) {
                    $status = 'reviewed';
                } else {
                    $status = 'quoted';
                }

                $project = random_int(1, 3) === 1 && $projects->isNotEmpty() ? $projects->random() : null;

                $rentalRequest = RentalRequest::create([
                    'request_number' => 'REQ-' . $createdAt->format('Y') . '-' . str_pad((string) $requestSequence, 4, '0', STR_PAD_LEFT),
                    'customer_id' => $customer->id,
                    'project_id' => $project?->id,
                    'contact_person' => $customer->contact_person,
                    'contact_phone' => $customer->phone,
                    'project_name' => $project?->name ?? $customer->company_name . ' Site Works',
                    'project_type' => $project?->industry ?? $customer->industry,
                    'project_location' => $project?->city ?? $customer->city,
                    'operator_required' => random_int(1, 3) === 1,
                    'transportation_included' => random_int(1, 3) === 1,
                    'fuel_included' => random_int(1, 4) === 1,
                    'additional_requirements' => random_int(1, 5) === 1 ? 'Operator and basic maintenance included. Fuel excluded unless stated.' : null,
                    'status' => $status,
                    'reviewed_by' => random_int(1, 2),
                    'reviewed_at' => $status !== 'pending' && $status !== 'reviewed' ? $createdAt->copy()->addDays(random_int(1, 5)) : null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
                $requestSequence++;

                if ($status === 'quoted') {
                    $quotedEntries[] = [
                        'request_id' => $rentalRequest->id,
                        'customer' => $customer,
                        'created_at' => $createdAt,
                        'items' => $items,
                    ];
                }

                foreach ($items as $item) {
                    RentalRequestItem::create([
                        'rental_request_id' => $rentalRequest->id,
                        'equipment_id' => $item['equipment']?->id,
                        'equipment_category_id' => $item['category']->id,
                        'quantity' => $item['quantity'],
                        'start_date' => $item['start_date'],
                        'end_date' => $item['end_date'],
                    ]);
                }
            }
        }

        $this->command->info('Rental requests seeded: ' . RentalRequest::count());

        // ============================================================
        // 2. QUOTATIONS (200) + 3. CONTRACTS (~150) integrated
        //    Recent requests are guaranteed to flow through to contracts
        // ============================================================
        $quotationTarget = min(200, count($quotedEntries));

        $collection = collect($quotedEntries);
        $recent = $collection->filter(fn ($e) => $e['created_at']->gt(now()->subMonths(7)))->values();
        $older = $collection->filter(fn ($e) => $e['created_at']->lte(now()->subMonths(7)))->values();

        $recentCount = min($recent->count(), 80);
        $selection = $recent->shuffle()->take($recentCount)
            ->concat($older->shuffle()->take($quotationTarget - $recentCount))
            ->sortBy('created_at')
            ->values();

        $quotationSequence = 1;
        $contractSequence = 1;
        $contractsByEquipment = [];
        $activeContractEquipment = [];

        foreach ($selection as $entry) {
            $createdAt = $entry['created_at'];
            $items = $entry['items'];
            $firstItem = $items[0];
            $customer = $entry['customer'];

            $rentalRequest = RentalRequest::find($entry['request_id']);
            if (! $rentalRequest) {
                continue;
            }
            $rentalRequest->update(['status' => 'quoted', 'reviewed_at' => $createdAt->copy()->addDays(random_int(1, 5))]);

            $duration = $firstItem['start_date']->diffInDays($firstItem['end_date']) + 1;

            $rentalRate = 0;
            $lineItems = [];
            foreach ($items as $item) {
                $equipment = $item['equipment'];
                $rate = $equipment ? (float) $equipment->daily_rate : 2500000;
                $lineTotal = $rate * $item['quantity'] * $duration;
                $rentalRate += $lineTotal;
                $lineItems[] = [
                    'equipment' => $equipment,
                    'quantity' => $item['quantity'],
                    'unit_rate' => $rate,
                    'duration' => $duration,
                    'line_total' => $lineTotal,
                ];
            }

            $operatorCost = $rentalRequest->operator_required ? $duration * random_int(550000, 850000) : 0;
            $transportationCost = $rentalRequest->transportation_included ? random_int(1500000, 8000000) : 0;
            $fuelCost = $rentalRequest->fuel_included ? $rentalRate * 0.18 : 0;
            $additionalCost = random_int(1, 4) === 1 ? random_int(500000, 3000000) : 0;
            $discount = random_int(1, 4) === 1 ? $rentalRate * random_int(2, 6) / 100 : 0;
            $subtotal = $rentalRate + $operatorCost + $transportationCost + $fuelCost + $additionalCost - $discount;
            $taxRate = 11;
            $taxAmount = $subtotal * ($taxRate / 100);
            $grandTotal = $subtotal + $taxAmount;

            $start = $firstItem['start_date'];
            $end = $firstItem['end_date'];
            $isRecent = $start->gt(now()->subMonths(3));

            $roll = random_int(1, 100);
            if ($isRecent) {
                // Recent requests must flow into active contracts for a live dashboard
                $status = $roll <= 10 ? 'revision' : 'accepted';
            } elseif ($roll <= 12) {
                $status = 'rejected';
            } elseif ($roll <= 18) {
                $status = 'expired';
            } elseif ($roll <= 24) {
                $status = 'revision';
            } elseif ($roll <= 30) {
                $status = 'sent';
            } else {
                $status = 'accepted';
            }

            $quotation = Quotation::create([
                'quotation_number' => 'QUO-' . $createdAt->format('Y') . '-' . str_pad((string) $quotationSequence, 4, '0', STR_PAD_LEFT),
                'rental_request_id' => $rentalRequest->id,
                'customer_id' => $rentalRequest->customer_id,
                'project_id' => $rentalRequest->project_id,
                'valid_until' => $createdAt->copy()->addDays(30),
                'rental_period_start' => $start,
                'rental_period_end' => $end,
                'rental_rate' => $rentalRate,
                'operator_cost' => $operatorCost,
                'transportation_cost' => $transportationCost,
                'fuel_cost' => $fuelCost,
                'additional_service_cost' => $additionalCost,
                'discount' => $discount,
                'tax_rate' => $taxRate,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal,
                'status' => $status,
                'notes' => random_int(1, 3) === 1 ? 'Rates valid for 30 days. Delivery schedule based on site readiness.' : null,
                'created_by' => 2,
                'created_at' => $createdAt->copy()->addDays(random_int(2, 6)),
                'updated_at' => $createdAt,
            ]);
            $quotationSequence++;

            foreach ($lineItems as $item) {
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'equipment_id' => $item['equipment']?->id,
                    'equipment_name_snapshot' => $item['equipment'] ? "{$item['equipment']->brand} {$item['equipment']->model} ({$item['equipment']->equipment_code})" : 'Equipment',
                    'quantity' => $item['quantity'],
                    'unit' => 'unit',
                    'unit_rate' => $item['unit_rate'],
                    'duration_days' => $item['duration'],
                    'line_total' => $item['line_total'],
                ]);
            }

            // ---- Contract for accepted quotations (cap at 150) ----
            if ($status === 'accepted' && $contractSequence <= 150) {
                $isPast = $end->lt(now());
                $croll = random_int(1, 100);
                if ($croll <= 2) {
                    $cstatus = 'draft';
                } elseif ($croll <= 4) {
                    $cstatus = 'terminated';
                } elseif ($isPast) {
                    $cstatus = 'completed';
                } else {
                    $cstatus = 'active';
                }

                $contract = Contract::create([
                    'contract_number' => 'CON-' . $start->format('Y') . '-' . str_pad((string) $contractSequence, 4, '0', STR_PAD_LEFT),
                    'quotation_id' => $quotation->id,
                    'customer_id' => $quotation->customer_id,
                    'project_id' => $quotation->project_id,
                    'start_date' => $start,
                    'end_date' => $end,
                    'rental_rate' => $rentalRate,
                    'deposit' => round($grandTotal * 0.12),
                    'payment_terms' => ['30 days', '15 days', '60 days', 'Net 30'][random_int(0, 3)],
                    'contract_value' => $grandTotal,
                    'status' => $cstatus,
                    'signed_at' => $start->copy()->subDays(random_int(1, 7)),
                    'notes' => null,
                    'created_at' => $createdAt->copy()->addDays(random_int(7, 14)),
                    'updated_at' => $createdAt,
                ]);
                $contractSequence++;

                foreach ($lineItems as $item) {
                    $equipment = $item['equipment'];
                    if (! $equipment) {
                        continue;
                    }
                    ContractItem::create([
                        'contract_id' => $contract->id,
                        'equipment_id' => $equipment->id,
                        'quantity' => $item['quantity'],
                        'unit_rate' => $item['unit_rate'],
                        'duration_days' => $item['duration'],
                        'line_total' => $item['line_total'],
                    ]);

                    $contractsByEquipment[$equipment->id][] = [$start, $end, $contract->id];
                    if ($cstatus === 'active') {
                        $activeContractEquipment[$equipment->id] = $contract->id;
                    }
                }
            }
        }

        $this->command->info('Quotations seeded: ' . Quotation::count() . ', Contracts: ' . Contract::count());

        // Mark equipment status
        Equipment::query()->update(['status' => 'available']);
        foreach ($activeContractEquipment as $eqId => $contractId) {
            Equipment::where('id', $eqId)->update(['status' => 'rented']);
        }

        // ============================================================
        // 4. INVOICES (210) + PAYMENTS (300+)
        // ============================================================
        $invoiceSequence = 1;
        $paymentSequence = 1;
        $contracts = Contract::whereIn('status', ['active', 'completed'])->with('customer', 'items')->get();

        foreach ($contracts as $contract) {
            $items = $contract->items;
            if ($items->isEmpty()) {
                continue;
            }

            $billingStart = $contract->start_date->copy();
            $invoiceDates = [];
            $periods = [];

            while ($billingStart->lt($contract->end_date) && count($periods) < 3) {
                $periodEnd = $billingStart->copy()->addDays(29);
                if ($periodEnd->gt($contract->end_date)) {
                    $periodEnd = $contract->end_date->copy();
                }
                $invoiceDates[] = $billingStart->copy()->addDays(random_int(0, 4));
                $periods[] = [$billingStart->copy(), $periodEnd->copy()];
                $billingStart = $periodEnd->copy()->addDay();
            }

            foreach ($periods as $idx => [$pStart, $pEnd]) {
                if ($invoiceSequence > 210) {
                    break 2;
                }

                $days = $pStart->diffInDays($pEnd) + 1;
                $subtotal = round($contract->contract_value / max(1, $contract->duration_days) * $days);
                $tax = round($subtotal * 0.11);
                $total = $subtotal + $tax;
                $invoiceDate = $invoiceDates[$idx];
                $dueDate = $invoiceDate->copy()->addDays(30);

                $roll = random_int(1, 100);
                if ($invoiceDate->copy()->addDays(35)->lt(now())) {
                    if ($roll <= 20) {
                        $paymentStatus = 'overdue';
                    } elseif ($roll <= 32) {
                        $paymentStatus = 'partial';
                    } else {
                        $paymentStatus = 'paid';
                    }
                } elseif ($invoiceDate->gt(now())) {
                    $paymentStatus = 'pending';
                } else {
                    if ($roll <= 25) {
                        $paymentStatus = 'pending';
                    } elseif ($roll <= 40) {
                        $paymentStatus = 'partial';
                    } else {
                        $paymentStatus = 'paid';
                    }
                }

                $invoice = Invoice::create([
                    'invoice_number' => 'INV-' . $invoiceDate->format('Y') . '-' . str_pad((string) $invoiceSequence, 4, '0', STR_PAD_LEFT),
                    'contract_id' => $contract->id,
                    'customer_id' => $contract->customer_id,
                    'project_id' => $contract->project_id,
                    'invoice_date' => $invoiceDate,
                    'due_date' => $dueDate,
                    'period_start' => $pStart,
                    'period_end' => $pEnd,
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'total' => $total,
                    'amount_paid' => 0,
                    'payment_status' => $paymentStatus,
                    'notes' => null,
                    'created_at' => $invoiceDate,
                    'updated_at' => $invoiceDate,
                ]);
                $invoiceSequence++;

                $paymentsForInvoice = [];
                $amountPaid = 0;

                if ($paymentStatus === 'paid') {
                    $first = round($total * 0.6);
                    $paymentsForInvoice[] = $first;
                    if ($first < $total) {
                        $paymentsForInvoice[] = $total - $first;
                    }
                } elseif ($paymentStatus === 'partial') {
                    $paymentsForInvoice[] = round($total * 0.45);
                } elseif ($paymentStatus === 'overdue') {
                    $paymentsForInvoice[] = random_int(1, 3) === 1 ? round($total * 0.3) : 0;
                }

                foreach ($paymentsForInvoice as $amount) {
                    if ($amount <= 0) {
                        continue;
                    }
                    $payDate = $dueDate->copy()->subDays(random_int(0, 25));
                    if ($payDate->gt(now())) {
                        $payDate = now()->copy()->subDays(random_int(0, 5));
                    }
                    Payment::create([
                        'payment_number' => 'PAY-' . $payDate->format('Y') . '-' . str_pad((string) $paymentSequence, 4, '0', STR_PAD_LEFT),
                        'invoice_id' => $invoice->id,
                        'customer_id' => $contract->customer_id,
                        'amount' => $amount,
                        'payment_date' => $payDate,
                        'method' => ['bank_transfer', 'cash', 'cheque', 'giro'][random_int(0, 3)],
                        'reference' => 'TRX-' . random_int(1000000000, 9999999999),
                        'notes' => null,
                        'created_at' => $payDate,
                        'updated_at' => $payDate,
                    ]);
                    $amountPaid += $amount;
                    $paymentSequence++;
                }

                $invoice->update(['amount_paid' => $amountPaid]);
            }
        }

        $this->command->info('Invoices seeded: ' . Invoice::count() . ', Payments: ' . Payment::count());

        // ============================================================
        // 5. DELIVERIES
        // ============================================================
        $deliverySequence = 1;
        $activeOrCompleted = Contract::whereIn('status', ['active', 'completed'])->with('items.equipment', 'customer')->get();

        foreach ($activeOrCompleted as $contract) {
            foreach ($contract->items as $item) {
                if (! $item->equipment) {
                    continue;
                }
                $isCompleted = $contract->status === 'completed';
                $deliveryDate = $contract->start_date->copy()->subDays(random_int(0, 4));

                Delivery::create([
                    'delivery_number' => 'DLV-' . $deliveryDate->format('Y') . '-' . str_pad((string) $deliverySequence, 4, '0', STR_PAD_LEFT),
                    'contract_id' => $contract->id,
                    'equipment_id' => $item->equipment->id,
                    'customer_id' => $contract->customer_id,
                    'project_id' => $contract->project_id,
                    'pickup_location' => "{$item->equipment->city} Depot",
                    'destination' => $contract->project?->city ?? $contract->customer->city,
                    'driver_name' => ['Sutrisno', 'Ujang', 'Yanto', 'Bambang'][random_int(0, 3)],
                    'driver_phone' => '081' . random_int(2, 9) . str_pad((string) random_int(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                    'transport_vehicle' => 'Lowbed Trailer 60 Ton',
                    'plate_number' => 'B ' . random_int(1000, 9999) . ' ' . strtoupper(substr(md5((string) random_int(1, 99)), 0, 3)),
                    'delivery_date' => $deliveryDate,
                    'estimated_arrival' => $deliveryDate->copy()->addDays(random_int(1, 2)),
                    'status' => $isCompleted ? 'confirmed' : ['scheduled', 'preparing', 'in_transit', 'delivered'][random_int(0, 3)],
                    'notes' => null,
                    'created_at' => $deliveryDate->copy()->subDays(random_int(1, 5)),
                    'updated_at' => $deliveryDate,
                ]);
                $deliverySequence++;
            }
        }

        $this->command->info('Deliveries seeded: ' . Delivery::count());

        // ============================================================
        // 6. MAINTENANCE RECORDS
        // ============================================================
        $maintenanceSequence = 1;
        $maintenanceByEquipment = [];

        foreach ($equipmentAll as $eq) {
            $profile = (($eq->id * 37) % 100) / 100;
            $recordCount = 0;
            if ($profile > 0.8) {
                $recordCount = random_int(3, 5);
            } elseif ($profile > 0.5) {
                $recordCount = random_int(1, 3);
            } elseif (random_int(1, 3) === 1) {
                $recordCount = random_int(1, 2);
            }

            for ($r = 0; $r < $recordCount; $r++) {
                $type = random_int(1, 4) === 1 ? 'corrective' : 'preventive';
                $date = now()->subDays(random_int(5, 340));
                $cost = $type === 'corrective'
                    ? random_int(3000000, 28000000)
                    : random_int(1200000, 12000000);
                $downtime = $type === 'corrective' ? random_int(12, 72) : random_int(4, 24);

                $status = random_int(1, 6) === 1 ? 'scheduled' : 'completed';
                if ($date->gt(now())) {
                    $status = 'scheduled';
                }

                $record = MaintenanceRecord::create([
                    'maintenance_number' => 'MNT-' . $date->format('Y') . '-' . str_pad((string) $maintenanceSequence, 4, '0', STR_PAD_LEFT),
                    'equipment_id' => $eq->id,
                    'type' => $type,
                    'title' => $type === 'preventive'
                        ? 'Scheduled service - ' . random_int(1000, 10000) . ' hour interval'
                        : 'Corrective repair - ' . ['engine fault', 'hydraulic leak', 'transmission issue', 'undercarriage wear', 'electrical fault'][random_int(0, 4)],
                    'description' => $type === 'preventive' ? 'Standard preventive maintenance including oil, filters, and fluid checks.' : 'Diagnosed and repaired by certified technician.',
                    'technician' => ['Fajar', 'Doni', 'Irfan', 'Lutfi', 'Oman'][random_int(0, 4)],
                    'date' => $date,
                    'cost' => $cost,
                    'downtime_hours' => $downtime,
                    'parts_used' => $type === 'preventive' ? 'Oil, filters, lubricants' : ['Hydraulic hose kit', 'Final drive assembly', 'Engine gasket set', 'Undercarriage components'][random_int(0, 3)],
                    'next_due_date' => $date->copy()->addDays(random_int(60, 120)),
                    'status' => $status,
                    'created_at' => $date->copy()->subDays(random_int(0, 2)),
                    'updated_at' => $date,
                ]);
                $maintenanceSequence++;

                if ($status === 'scheduled' && $record->next_due_date->gte(now())) {
                    $maintenanceByEquipment[$eq->id][] = [$date->copy(), $date->copy()->addDays($downtime)];
                }
            }
        }

        $this->command->info('Maintenance records seeded: ' . MaintenanceRecord::count());

        // ============================================================
        // 7. EQUIPMENT UTILIZATION (daily, last 365 days)
        // ============================================================
        $this->command->info('Seeding equipment utilization (this may take a moment)...');

        $from = now()->subDays(365)->startOfDay();
        $to = now()->startOfDay();
        $chunk = [];

        foreach ($equipmentAll as $eq) {
            $dailyRate = (float) $eq->daily_rate;
            $ranges = $contractsByEquipment[$eq->id] ?? [];
            $maintRanges = $maintenanceByEquipment[$eq->id] ?? [];

            for ($day = $from->copy(); $day->lte($to); $day->addDay()) {
                $status = 'available';
                $hours = 0;
                $revenue = 0;
                $contractId = null;

                foreach ($ranges as [$s, $e, $cid]) {
                    if ($day->between($s, $e)) {
                        $status = 'rented';
                        $hours = round(random_int(70, 115) / 10, 1);
                        $revenue = round($dailyRate * (random_int(90, 100) / 100), 2);
                        $contractId = $cid;
                        break;
                    }
                }

                if ($status === 'available') {
                    foreach ($maintRanges as [$ms, $me]) {
                        if ($day->between($ms, $me)) {
                            $status = 'maintenance';
                            break;
                        }
                    }
                }

                $chunk[] = [
                    'equipment_id' => $eq->id,
                    'date' => $day->toDateString(),
                    'status' => $status,
                    'hours_operated' => $hours,
                    'revenue' => $revenue,
                    'project_id' => null,
                    'contract_id' => $contractId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($chunk) >= 100) {
                    DB::table('equipment_utilization')->insert($chunk);
                    $chunk = [];
                }
            }
        }

        if (! empty($chunk)) {
            DB::table('equipment_utilization')->insert($chunk);
        }

        $this->command->info('Equipment utilization seeded: ' . EquipmentUtilization::count());

        // ============================================================
        // 8. Notifications for customer accounts
        // ============================================================
        $customerUsers = Customer::whereNotNull('user_id')->with('user')->get();
        foreach ($customerUsers as $customer) {
            $types = ['Quotation Received', 'Request Status Update', 'Invoice Issued', 'Contract Signed'];
            $count = random_int(2, 5);
            for ($i = 0; $i < $count; $i++) {
                $title = $types[random_int(0, count($types) - 1)];
                \App\Models\AppNotification::create([
                    'user_id' => $customer->user_id,
                    'title' => $title,
                    'message' => $title === 'Quotation Received'
                        ? 'A new quotation is ready for your review in the portal.'
                        : ($title === 'Invoice Issued' ? 'A new invoice has been issued to your account.' : 'Your request has been updated by our team.'),
                    'type' => random_int(1, 4) === 1 ? 'success' : 'info',
                    'link' => null,
                    'read_at' => random_int(1, 3) === 1 ? now()->subDays(random_int(1, 20)) : null,
                    'created_at' => now()->subDays(random_int(1, 60)),
                ]);
            }
        }

        $this->command->info('Business flow seeding complete.');
    }

    private function weightedCustomer($customers, $weightMap)
    {
        $total = 0;
        foreach ($customers as $c) {
            $total += $weightMap[$c->segment] ?? 1;
        }
        $roll = random_int(1, $total);
        $cumulative = 0;
        foreach ($customers as $c) {
            $cumulative += $weightMap[$c->segment] ?? 1;
            if ($roll <= $cumulative) {
                return $c;
            }
        }

        return $customers->first();
    }

    private function weightedCategory($categories)
    {
        $pool = [];
        foreach ($categories as $category) {
            $weight = $this->weightMap[$category->name] ?? 3;
            for ($i = 0; $i < $weight; $i++) {
                $pool[] = $category;
            }
        }

        return $pool[random_int(0, count($pool) - 1)];
    }

    private function pickEquipment($equipmentByCategory, $category, bool $preferHighProfile)
    {
        $pool = $equipmentByCategory[$category->id] ?? collect();
        if ($pool->isEmpty()) {
            return null;
        }

        $pool = $pool->filter(fn ($eq) => $eq->status !== 'unavailable');
        if ($pool->isEmpty()) {
            return null;
        }

        if ($preferHighProfile) {
            $sorted = $pool->sortByDesc(fn ($eq) => (($eq->id * 37) % 100) / 100);
            $top = $sorted->take(max(2, intdiv($pool->count(), 2)));
            if ($top->isNotEmpty()) {
                $pool = $top;
            }
        }

        return $pool->random();
    }

    private function durationForCategory(string $categoryName): int
    {
        $long = ['Crawler Crane', 'Tower Crane', 'Asphalt Paver', 'Pile Driver', 'Concrete Pump'];
        $short = ['Forklift', 'Skid Steer Loader', 'Mini Excavator', 'Backhoe Loader', 'Telehandler'];

        if (in_array($categoryName, $long)) {
            return random_int(60, 180);
        }
        if (in_array($categoryName, $short)) {
            return random_int(14, 60);
        }

        return random_int(30, 120);
    }
}