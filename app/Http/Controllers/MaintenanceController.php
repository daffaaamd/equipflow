<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\MaintenanceRecord;
use App\Services\ActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceController extends Controller
{
    public function index(Request $request): View
    {
        $records = MaintenanceRecord::with('equipment')
            ->when($request->search, fn ($q, $s) => $q->where('maintenance_number', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->type, fn ($q, $v) => $q->where('type', $v))
            ->when($request->equipment_id, fn ($q, $v) => $q->where('equipment_id', $v))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $filters = $request->only(['search', 'status', 'type', 'equipment_id']);
        $equipment = Equipment::with('category')->get();

        return view('pages.maintenance.index', compact('records', 'filters', 'equipment'));
    }

    public function create(): View
    {
        $equipment = Equipment::with('category')->get();

        return view('pages.maintenance.create', compact('equipment'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'equipment_id' => ['required', 'exists:equipment,id'],
            'type' => ['required', 'in:preventive,corrective'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'technician' => ['nullable', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'cost' => ['required', 'numeric', 'min:0'],
            'downtime_hours' => ['required', 'numeric', 'min:0'],
            'parts_used' => ['nullable', 'string'],
            'next_due_date' => ['nullable', 'date'],
            'status' => ['required', 'in:scheduled,in_progress,completed,cancelled'],
        ]);

        $record = MaintenanceRecord::create(array_merge($validated, [
            'maintenance_number' => 'MNT-' . date('Y') . '-' . str_pad((string) (MaintenanceRecord::count() + 1), 4, '0', STR_PAD_LEFT),
        ]));

        if ($validated['status'] === 'in_progress' || $validated['status'] === 'scheduled') {
            Equipment::where('id', $validated['equipment_id'])->update(['status' => 'maintenance']);
        }

        ActivityService::log('create', 'maintenance', $record->id, "Maintenance {$record->maintenance_number} scheduled for {$record->equipment->equipment_code}");

        return redirect()->route('admin.maintenance.show', $record->id)->with('success', 'Maintenance record created.');
    }

    public function show(MaintenanceRecord $maintenanceRecord): View
    {
        $maintenanceRecord->load('equipment.category', 'equipment.assignedOperators');

        return view('pages.maintenance.show', compact('maintenanceRecord'));
    }

    public function edit(MaintenanceRecord $maintenanceRecord): View
    {
        $equipment = Equipment::with('category')->get();

        return view('pages.maintenance.edit', compact('maintenanceRecord', 'equipment'));
    }

    public function update(Request $request, MaintenanceRecord $maintenanceRecord): RedirectResponse
    {
        $validated = $request->validate([
            'equipment_id' => ['required', 'exists:equipment,id'],
            'type' => ['required', 'in:preventive,corrective'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'technician' => ['nullable', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'cost' => ['required', 'numeric', 'min:0'],
            'downtime_hours' => ['required', 'numeric', 'min:0'],
            'parts_used' => ['nullable', 'string'],
            'next_due_date' => ['nullable', 'date'],
            'status' => ['required', 'in:scheduled,in_progress,completed,cancelled'],
        ]);

        $previousStatus = $maintenanceRecord->status;
        $maintenanceRecord->update($validated);

        if ($validated['status'] === 'completed' && in_array($previousStatus, ['scheduled', 'in_progress'])) {
            Equipment::where('id', $validated['equipment_id'])->update(['status' => 'available']);
            Equipment::where('id', $validated['equipment_id'])->increment('operating_hours', 0);
        }

        if ($validated['status'] === 'cancelled') {
            Equipment::where('id', $validated['equipment_id'])
                ->where('status', 'maintenance')->update(['status' => 'available']);
        }

        ActivityService::log('update', 'maintenance', $maintenanceRecord->id, "Maintenance {$maintenanceRecord->maintenance_number} updated");

        return redirect()->route('admin.maintenance.show', $maintenanceRecord->id)->with('success', 'Maintenance updated.');
    }

    public function destroy(MaintenanceRecord $maintenanceRecord): RedirectResponse
    {
        ActivityService::log('delete', 'maintenance', $maintenanceRecord->id, "Maintenance {$maintenanceRecord->maintenance_number} removed");
        $maintenanceRecord->delete();

        return redirect()->route('admin.maintenance.index')->with('success', 'Maintenance record removed.');
    }
}
