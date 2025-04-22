<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\Zone;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::with('zone')->get();
        return view('admin.tables.index', compact('tables'));
    }

    public function create()
    {
        $zones = Zone::all();
        return view('admin.tables.create', compact('zones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'capacity' => 'required|integer|min:1',
            'zone_id' => 'required|exists:zones,id',
            'is_active' => 'boolean',
        ]);

        Table::create($validated);

        return redirect()->route('admin.tables.index')->with('success', 'Mesa creada exitosamente.');
    }

    public function show(Table $table)
    {
        return view('admin.tables.show', compact('table'));
    }

    public function edit(Table $table)
    {
        $zones = Zone::all();
        return view('admin.tables.edit', compact('table', 'zones'));
    }

    public function update(Request $request, Table $table)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'capacity' => 'required|integer|min:1',
            'zone_id' => 'required|exists:zones,id',
            'is_active' => 'boolean',
        ]);

        $table->update($validated);

        return redirect()->route('admin.tables.index')->with('success', 'Mesa actualizada exitosamente.');
    }

    public function destroy(Table $table)
    {
        $table->delete();
        return redirect()->route('admin.tables.index')->with('success', 'Mesa eliminada exitosamente.');
    }
}