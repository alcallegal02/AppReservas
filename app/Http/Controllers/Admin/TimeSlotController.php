<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TimeSlot;
use Illuminate\Http\Request;

class TimeSlotController extends Controller
{
    public function index()
    {
        $timeSlots = TimeSlot::all();
        return view('admin.time-slots.index', compact('timeSlots'));
    }

    public function create()
    {
        return view('admin.time-slots.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_active' => 'boolean',
        ]);

        TimeSlot::create($validated);

        return redirect()->route('admin.time-slots.index')->with('success', 'Franja horaria creada exitosamente.');
    }

    public function show(TimeSlot $timeSlot)
    {
        return view('admin.time-slots.show', compact('timeSlot'));
    }

    public function edit(TimeSlot $timeSlot)
    {
        return view('admin.time-slots.edit', compact('timeSlot'));
    }

    public function update(Request $request, TimeSlot $timeSlot)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_active' => 'boolean',
        ]);

        $timeSlot->update($validated);

        return redirect()->route('admin.time-slots.index')->with('success', 'Franja horaria actualizada exitosamente.');
    }

    public function destroy(TimeSlot $timeSlot)
    {
        $timeSlot->delete();
        return redirect()->route('admin.time-slots.index')->with('success', 'Franja horaria eliminada exitosamente.');
    }
}