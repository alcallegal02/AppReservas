<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Table;
use App\Models\TimeSlot;
use App\Models\Zone;
use App\Models\RestaurantClosure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Auth::user()->reservations()->with(['table', 'timeSlot'])->get();
        return view('reservations.index', compact('reservations'));
    }

    public function create()
    {
        $zones = Zone::with('tables')->get();
        $timeSlots = TimeSlot::where('is_active', true)->get();
        $closures = RestaurantClosure::where('closure_date', '>=', now()->toDateString())->get();
        
        return view('reservations.create', compact('zones', 'timeSlots', 'closures'));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'reservation_date' => 'required|date|after_or_equal:today',
        'time_slot_id' => 'required|exists:time_slots,id',
        'zone_id' => 'required|exists:zones,id',
        'guest_count' => 'required|integer|min:1',
        'special_requests' => 'nullable|string',
    ]);

    // Verificar si el restaurante está cerrado
    if (RestaurantClosure::where('closure_date', $validated['reservation_date'])->exists()) {
        return back()->with('error', 'El restaurante está cerrado en la fecha seleccionada.');
    }

    // Buscar mesas activas en la zona con capacidad suficiente
    $availableTables = Table::where('zone_id', $validated['zone_id'])
        ->where('is_active', true)
        ->where('capacity', '>=', $validated['guest_count'])
        ->orderBy('capacity', 'asc') // Queremos la más ajustada posible
        ->get();

    // Filtrar las mesas que no estén ya reservadas en esa fecha y franja horaria
    $tableId = null;
    foreach ($availableTables as $table) {
        $alreadyReserved = Reservation::where('table_id', $table->id)
            ->where('reservation_date', $validated['reservation_date'])
            ->where('time_slot_id', $validated['time_slot_id'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if (!$alreadyReserved) {
            $tableId = $table->id;
            break;
        }
    }

    if (!$tableId) {
        return back()->with('error', 'No hay mesas disponibles para la selección realizada.');
    }

    // Agregar los datos de la mesa y usuario
    $validated['user_id'] = Auth::id();
    $validated['table_id'] = $tableId;
    $validated['status'] = 'pending';

    // Setear horario
    $timeSlot = TimeSlot::find($validated['time_slot_id']);
    $validated['start_time'] = $timeSlot->start_time;
    $validated['end_time'] = $timeSlot->end_time;

    Reservation::create($validated);

    return redirect()->route('reservations.index')->with('success', 'Reserva creada exitosamente. Está pendiente de confirmación.');
}


public function edit(Reservation $reservation)
{
    // Verificar si el usuario autenticado es el propietario de la reserva
    if (Auth::id() !== $reservation->user_id) {
        return redirect()->route('reservations.index')->with('error', 'No tienes permiso para editar esta reserva.');
    }

    $zones = Zone::with('tables')->get();
    $timeSlots = TimeSlot::where('is_active', true)->get();
    $closures = RestaurantClosure::where('closure_date', '>=', now()->toDateString())->get();

    // Pasar los datos a la vista de edición
    return view('reservations.edit', compact('reservation', 'zones', 'timeSlots', 'closures'));
}

public function update(Request $request, Reservation $reservation)
{
    // Verificar si el usuario autenticado es el propietario de la reserva
    if (Auth::id() !== $reservation->user_id) {
        return redirect()->route('reservations.index')->with('error', 'No tienes permiso para editar esta reserva.');
    }

    // Validación de los datos enviados
    $validated = $request->validate([
        'reservation_date' => 'required|date|after_or_equal:today',
        'time_slot_id' => 'required|exists:time_slots,id',
        'zone_id' => 'required|exists:zones,id',
        'guest_count' => 'required|integer|min:1',
        'special_requests' => 'nullable|string',
    ]);

    // Verificar si el restaurante está cerrado
    if (RestaurantClosure::where('closure_date', $validated['reservation_date'])->exists()) {
        return back()->with('error', 'El restaurante está cerrado en la fecha seleccionada.');
    }

    // Buscar mesas activas en la zona con capacidad suficiente
    $availableTables = Table::where('zone_id', $validated['zone_id'])
        ->where('is_active', true)
        ->where('capacity', '>=', $validated['guest_count'])
        ->orderBy('capacity', 'asc') // Queremos la más ajustada posible
        ->get();

    // Filtrar las mesas que no estén ya reservadas en esa fecha y franja horaria
    $tableId = null;
    foreach ($availableTables as $table) {
        $alreadyReserved = Reservation::where('table_id', $table->id)
            ->where('reservation_date', $validated['reservation_date'])
            ->where('time_slot_id', $validated['time_slot_id'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if (!$alreadyReserved) {
            $tableId = $table->id;
            break;
        }
    }

    if (!$tableId) {
        return back()->with('error', 'No hay mesas disponibles para la selección realizada.');
    }

    // Actualizar los datos de la reserva
    $validated['user_id'] = Auth::id();
    $validated['table_id'] = $tableId;
    $validated['status'] = 'pending';

    // Setear horario
    $timeSlot = TimeSlot::find($validated['time_slot_id']);
    $validated['start_time'] = $timeSlot->start_time;
    $validated['end_time'] = $timeSlot->end_time;

    // Actualizar la reserva existente
    $reservation->update($validated);

    return redirect()->route('reservations.index')->with('success', 'Reserva actualizada exitosamente.');
}

    public function destroy(Reservation $reservation)
    {
        $this->authorize('delete', $reservation);
        
        $reservation->update(['status' => 'cancelled']);
        
        return redirect()->route('reservations.index')->with('success', 'Reserva cancelada exitosamente.');
    }
}