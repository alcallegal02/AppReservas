<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestaurantClosure;
use Illuminate\Http\Request;

class RestaurantClosureController extends Controller
{
    public function index()
    {
        $closures = RestaurantClosure::all();
        return view('admin.restaurant-closures.index', compact('closures'));
    }

    public function create()
    {
        return view('admin.restaurant-closures.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'closure_date' => 'required|date|after_or_equal:today',
            'reason' => 'nullable|string|max:255',
            'is_recurring' => 'boolean',
        ]);

        RestaurantClosure::create($validated);

        return redirect()->route('admin.restaurant-closures.index')->with('success', 'Cierre registrado exitosamente.');
    }

    public function show(RestaurantClosure $restaurantClosure)
    {
        return view('admin.restaurant-closures.show', compact('restaurantClosure'));
    }

    public function edit(RestaurantClosure $restaurantClosure)
    {
        return view('admin.restaurant-closures.edit', compact('restaurantClosure'));
    }

    public function update(Request $request, RestaurantClosure $restaurantClosure)
    {
        $validated = $request->validate([
            'closure_date' => 'required|date',
            'reason' => 'nullable|string|max:255',
            'is_recurring' => 'boolean',
        ]);

        $restaurantClosure->update($validated);

        return redirect()->route('admin.restaurant-closures.index')->with('success', 'Cierre actualizado exitosamente.');
    }

    public function destroy(RestaurantClosure $restaurantClosure)
    {
        $restaurantClosure->delete();
        return redirect()->route('admin.restaurant-closures.index')->with('success', 'Cierre eliminado exitosamente.');
    }
}