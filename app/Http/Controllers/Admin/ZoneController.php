<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    public function index()
    {
        $zones = Zone::all();
        return view('admin.zones.index', compact('zones'));
    }

    public function create()
    {
        return view('admin.zones.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Zone::create($validated);

        return redirect()->route('admin.zones.index')->with('success', 'Zona creada exitosamente.');
    }

    public function show(Zone $zone)
    {
        return view('admin.zones.show', compact('zone'));
    }

    public function edit(Zone $zone)
    {
        return view('admin.zones.edit', compact('zone'));
    }

    public function update(Request $request, Zone $zone)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $zone->update($validated);

        return redirect()->route('admin.zones.index')->with('success', 'Zona actualizada exitosamente.');
    }

    public function destroy(Zone $zone)
    {
        $zone->delete();
        return redirect()->route('admin.zones.index')->with('success', 'Zona eliminada exitosamente.');
    }
}