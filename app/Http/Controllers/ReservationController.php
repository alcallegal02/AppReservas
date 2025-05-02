<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Table;
use App\Models\TimeSlot;
use App\Models\Zone;
use App\Models\RestaurantClosure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

        if (RestaurantClosure::where('closure_date', $validated['reservation_date'])->exists()) {
            return back()->withInput()->with('error', 'El restaurante está cerrado en la fecha seleccionada.');
        }

        $availableTables = Table::where('zone_id', $validated['zone_id'])
            ->where('is_active', true)
            ->where('capacity', '>=', $validated['guest_count'])
            ->orderBy('capacity', 'asc')
            ->get();

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
            return back()->withInput()->with('error', 'No hay mesas disponibles para la selección realizada.');
        }

        $validated['user_id'] = Auth::id();
        $validated['table_id'] = $tableId;
        $validated['status'] = 'pending';

        $timeSlot = TimeSlot::find($validated['time_slot_id']);
        $validated['start_time'] = $timeSlot->start_time;
        $validated['end_time'] = $timeSlot->end_time;

        Reservation::create($validated);

        return redirect()->route('reservations.index')->with('success', 'Reserva creada exitosamente. Está pendiente de confirmación.');
    }

    public function edit(Reservation $reservation)
    {
        if (Auth::id() !== $reservation->user_id) {
            return redirect()->route('reservations.index')->with('error', 'No tienes permiso para editar esta reserva.');
        }

        $reservation->load('table.zone', 'timeSlot');
        $zones = Zone::with('tables')->get();
        $timeSlots = TimeSlot::where('is_active', true)->get();
        $closures = RestaurantClosure::where('closure_date', '>=', now()->toDateString())->get();

        return view('reservations.edit', compact('reservation', 'zones', 'timeSlots', 'closures'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        Log::debug('Datos recibidos en update:', $request->all());

        if (Auth::id() !== $reservation->user_id) {
            return redirect()->route('reservations.index')->with('error', 'No tienes permiso para editar esta reserva.');
        }

        $validated = $request->validate([
            'reservation_date' => 'required|date|after_or_equal:today',
            'time_slot_id' => 'required|exists:time_slots,id',
            'zone_id' => 'required|exists:zones,id',
            'guest_count' => 'required|integer|min:1',
            'special_requests' => 'nullable|string',
        ]);

        if (RestaurantClosure::where('closure_date', $validated['reservation_date'])->exists()) {
            return back()->withInput()->with('error', 'El restaurante está cerrado en la fecha seleccionada.');
        }

        $currentTable = $reservation->table;
        $currentTableIsValid = $currentTable && 
                            $currentTable->zone_id == $validated['zone_id'] && 
                            $currentTable->capacity >= $validated['guest_count'];

        if ($currentTableIsValid) {
            $tableId = $currentTable->id;
        } else {
            $tableId = $this->findAvailableTable(
                $validated['zone_id'],
                $validated['reservation_date'],
                $validated['time_slot_id'],
                $validated['guest_count'],
                $reservation->id
            );

            if (!$tableId) {
                return back()->withInput()->with('error', 'No hay mesas disponibles para los nuevos parámetros seleccionados.');
            }
        }

        $timeSlot = TimeSlot::find($validated['time_slot_id']);

        $updateData = [
            'reservation_date' => $validated['reservation_date'],
            'time_slot_id' => $validated['time_slot_id'],
            'table_id' => $tableId,
            'guest_count' => $validated['guest_count'],
            'special_requests' => $validated['special_requests'],
            'start_time' => $timeSlot->start_time,
            'end_time' => $timeSlot->end_time,
            'status' => 'pending'
        ];

        if (!$reservation->update($updateData)) {
            return back()->withInput()->with('error', 'Error al actualizar la reserva.');
        }

        return redirect()->route('reservations.index')->with('success', 'Reserva actualizada exitosamente.');
    }

    protected function findAvailableTable($zoneId, $date, $timeSlotId, $guestCount, $excludeReservationId = null)
    {
        $query = Table::where('zone_id', $zoneId)
            ->where('is_active', true)
            ->where('capacity', '>=', $guestCount)
            ->orderBy('capacity', 'asc');

        if ($excludeReservationId) {
            $query->whereDoesntHave('reservations', function($q) use ($date, $timeSlotId, $excludeReservationId) {
                $q->where('reservation_date', $date)
                  ->where('time_slot_id', $timeSlotId)
                  ->where('id', '!=', $excludeReservationId)
                  ->whereIn('status', ['pending', 'confirmed']);
            });
        }

        return $query->value('id');
    }

    public function destroy(Reservation $reservation, Request $request)
{
    if (Auth::id() !== $reservation->user_id) {
        return redirect()->route('reservations.index')->with('error', 'No tienes permiso para esta acción.');
    }

    try {
        if ($request->has('complete_delete')) {
            // Eliminación permanente
            $reservation->delete();
            return redirect()->route('reservations.index')->with('success', 'Reserva eliminada permanentemente.');
        } else {
            // Cambio de estado a cancelado (como estaba antes)
            if (in_array($reservation->status, ['cancelled', 'completed', 'no_show'])) {
                return redirect()->back()->with('error', 'La reserva ya está '.$reservation->status);
            }
            $reservation->update(['status' => 'cancelled']);
            return redirect()->route('reservations.index')->with('success', 'Reserva cancelada exitosamente.');
        }
    } catch (\Exception $e) {
        Log::error('Error: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Ocurrió un error al procesar la solicitud.');
    }
}

public function show(Reservation $reservation)
{
    if (Auth::id() !== $reservation->user_id) {
        return redirect()->route('reservations.index')->with('error', 'No tienes permiso para ver esta reserva.');
    }

    // Cargar relaciones necesarias
    $reservation->load(['table.zone', 'timeSlot']);
    
    // Obtener días de cierre para mostrarlos en la vista
    $closures = RestaurantClosure::where('closure_date', '>=', now()->toDateString())->get();

    return view('reservations.show', compact('reservation', 'closures'));
}
}