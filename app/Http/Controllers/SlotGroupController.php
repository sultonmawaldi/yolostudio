<?php

namespace App\Http\Controllers;

use App\Models\SlotGroup;
use App\Models\Employee;
use Illuminate\Http\Request;

class SlotGroupController extends Controller
{
    public function index()
    {
        // FIX: load user dari employee (biar nama tidak null)
        $slotGroups = SlotGroup::with('employee.user')
            ->latest()
            ->get();

        return view('backend.slot-groups.index', compact('slotGroups'));
    }

    public function create()
    {
        // FIX: sekalian load user biar dropdown bisa tampil nama
        $employees = Employee::with('user')
            ->whereHas('user.roles', function ($q) {
                $q->where('name', 'employee');
            })
            ->get();

        return view('backend.slot-groups.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id'    => 'required|exists:employees,id',
            'name'           => 'required|string|max:255',
            'slot_duration'  => 'required|integer|min:1',
            'break_duration' => 'nullable|integer|min:0',
            'start_time'     => 'nullable',
            'end_time'       => 'nullable',
            'working_hours'  => 'nullable|array',
        ]);

        SlotGroup::create([
            'employee_id'    => $request->employee_id,
            'name'           => $request->name,
            'slot_duration'  => $request->slot_duration,
            'break_duration' => $request->break_duration ?? 0,
            'start_time'     => $request->start_time,
            'end_time'       => $request->end_time,
            'working_hours'  => $request->working_hours,
        ]);

        return redirect()->route('slot-group.index')
            ->with('success', 'Grup Slot berhasil dibuat');
    }

    public function edit($id)
    {
        $slotGroup = SlotGroup::with('employee.user')->findOrFail($id);

        $employees = Employee::with('user')
            ->whereHas('user.roles', function ($q) {
                $q->where('name', 'employee');
            })
            ->get();

        return view('backend.slot-groups.edit', compact('slotGroup', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $slotGroup = SlotGroup::findOrFail($id);

        $request->validate([
            'employee_id'    => 'required|exists:employees,id',
            'name'           => 'required|string|max:255',
            'slot_duration'  => 'required|integer|min:1',
            'break_duration' => 'nullable|integer|min:0',
            'start_time'     => 'nullable',
            'end_time'       => 'nullable',
            'working_hours'  => 'nullable|array',
        ]);

        $slotGroup->update([
            'employee_id'    => $request->employee_id,
            'name'           => $request->name,
            'slot_duration'  => $request->slot_duration,
            'break_duration' => $request->break_duration ?? 0,
            'start_time'     => $request->start_time,
            'end_time'       => $request->end_time,
            'working_hours'  => $request->working_hours,
        ]);

        return redirect()->route('slot-group.index')
            ->with('success', 'Grup Slot berhasil diperbarui');
    }

    public function destroy($id)
    {
        $slotGroup = SlotGroup::findOrFail($id);
        $slotGroup->delete();

        return redirect()->route('slot-group.index')
            ->with('success', 'Grup Slot berhasil dihapus');
    }
}
