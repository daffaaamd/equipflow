<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Operator;
use App\Models\Project;
use App\Services\ActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OperatorController extends Controller
{
    public function index(Request $request): View
    {
        $operators = Operator::with('assignedEquipment', 'project')
            ->when($request->search, fn ($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('operator_code', 'like', "%{$s}%");
            }))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->availability, fn ($q, $v) => $q->where('availability', $v))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $filters = $request->only(['search', 'status', 'availability']);
        $certificationExpiring = Operator::where('status', 'active')->get()->filter(fn ($o) => $o->isCertificationExpiring())->count();

        return view('pages.operators.index', compact('operators', 'filters', 'certificationExpiring'));
    }

    public function create(): View
    {
        $equipment = Equipment::with('category')->get();
        $projects = Project::whereIn('status', ['planning', 'active'])->get();

        return view('pages.operators.create', compact('equipment', 'projects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateData($request);
        $operator = Operator::create(array_merge($validated, [
            'operator_code' => 'OPR-' . str_pad((string) (Operator::count() + 1), 4, '0', STR_PAD_LEFT),
        ]));

        ActivityService::log('create', 'operator', $operator->id, "Operator {$operator->name} added");

        return redirect()->route('admin.operators.show', $operator->id)->with('success', 'Operator created successfully.');
    }

    public function show(Operator $operator): View
    {
        $operator->load('assignedEquipment', 'project');

        return view('pages.operators.show', compact('operator'));
    }

    public function edit(Operator $operator): View
    {
        $equipment = Equipment::with('category')->get();
        $projects = Project::whereIn('status', ['planning', 'active'])->get();

        return view('pages.operators.edit', compact('operator', 'equipment', 'projects'));
    }

    public function update(Request $request, Operator $operator): RedirectResponse
    {
        $operator->update($this->validateData($request));
        ActivityService::log('update', 'operator', $operator->id, "Operator {$operator->name} updated");

        return redirect()->route('admin.operators.show', $operator->id)->with('success', 'Operator updated successfully.');
    }

    public function destroy(Operator $operator): RedirectResponse
    {
        ActivityService::log('delete', 'operator', $operator->id, "Operator {$operator->name} removed");
        $operator->delete();

        return redirect()->route('admin.operators.index')->with('success', 'Operator removed.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'certification' => ['nullable', 'string', 'max:255'],
            'certification_expiry' => ['nullable', 'date'],
            'license_number' => ['nullable', 'string', 'max:50'],
            'years_experience' => ['nullable', 'integer', 'min:0'],
            'assigned_equipment_id' => ['nullable', 'exists:equipment,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'working_hours' => ['nullable', 'numeric', 'min:0'],
            'availability' => ['required', 'in:available,assigned,on_leave'],
            'status' => ['required', 'in:active,inactive'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
