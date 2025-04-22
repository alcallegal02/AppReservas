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
            'table_id' => 'required|exists:tables,id',
            'guest_count' => 'required|integer|min:1',
            'special_requests' => 'nullable|string',
        ]);

        // Verificar si el restaurante está cerrado en esa fecha
        $isClosed = RestaurantClosure::where('closure_date', $validated['reservation_date'])->exists();
        if ($isClosed) {
            return back()->with('error', 'El restaurante está cerrado en la fecha seleccionada.');
        }

        // Verificar disponibilidad de la mesa
        $existingReservation = Reservation::where('table_id', $validated['table_id'])
            ->where('reservation_date', $validated['reservation_date'])
            ->where('time_slot_id', $validated['time_slot_id'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($existingReservation) {
            return back()->with('error', 'La mesa ya está reservada para esa fecha y horario.');
        }

        // Verificar capacidad de la mesa
        $table = Table::find($validated['table_id']);
        if ($validated['guest_count'] > $table->capacity) {
            return back()->with('error', 'El número de invitados excede la capacidad de la mesa.');
        }

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pending';
        $timeSlot = TimeSlot::find($validated['time_slot_id']);
        $validated['start_time'] = $timeSlot->start_time;
        $validated['end_time'] = $timeSlot->end_time;

        Reservation::create($validated);

        return redirect()->route('reservations.index')->with('success', 'Reserva creada exitosamente. Está pendiente de confirmación.');
    }

    public function show(Reservation $reservation)
    {
        $this->authorize('view', $reservation);
        return view('reservations.show', compact('reservation'));
    }

    public function edit(Reservation $reservation)
    {
        $this->authorize('update', $reservation);
        
        $zones = Zone::with('tables')->get();
        $timeSlots = TimeSlot::where('is_active', true)->get();
        $closures = RestaurantClosure::where('closure_date', '>=', now()->toDateString())->get();
        
        return view('reservations.edit', compact('reservation', 'zones', 'timeSlots', 'closures'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $this->authorize('update', $reservation);

        $validated = $request->validate([
            'reservation_date' => 'required|date|after_or_equal:today',
            'time_slot_id' => 'required|exists:time_slots,id',
            'table_id' => 'required|exists:tables,id',
            'guest_count' => 'required|integer|min:1',
            'special_requests' => 'nullable|string',
        ]);

        // Verificar si el restaurante está cerrado en esa fecha
        $isClosed = RestaurantClosure::where('closure_date', $validated['reservation_date'])->exists();
        if ($isClosed) {
            return back()->with('error', 'El restaurante está cerrado en la fecha seleccionada.');
        }

        // Verificar disponibilidad de la mesa (excluyendo la reserva actual)
        $existingReservation = Reservation::where('table_id', $validated['table_id'])
            ->where('reservation_date', $validated['reservation_date'])
            ->where('time_slot_id', $validated['time_slot_id'])
            ->where('id', '!=', $reservation->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($existingReservation) {
            return back()->with('error', 'La mesa ya está reservada para esa fecha y horario.');
        }

        // Verificar capacidad de la mesa
        $table = Table::find($validated['table_id']);
        if ($validated['guest_count'] > $table->capacity) {
            return back()->with('error', 'El número de invitados excede la capacidad de la mesa.');
        }

        $timeSlot = TimeSlot::find($validated['time_slot_id']);
        $validated['start_time'] = $timeSlot->start_time;
        $validated['end_time'] = $timeSlot->end_time;

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