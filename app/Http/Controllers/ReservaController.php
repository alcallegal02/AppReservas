<?php

namespace App\Http\Controllers;

// app/Http/Controllers/ReservaController.php
use App\Models\Cliente;
use App\Models\Mesa;
use App\Models\Reserva;
use App\Models\MesaReserva;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    public function create()
    {
        return view('reservas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'apellidos' => 'required|string',
            'email' => 'required|email',
            'telefono' => 'required|string',
            'fecha_reserva' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'num_personas' => 'required|integer|min:1',
        ]);

        // Calcular hora_fin (1.5 horas después)
        $horaFin = date('H:i', strtotime($request->hora_inicio . ' + 90 minutes'));

        // Verificar disponibilidad de mesas
        $mesasNecesarias = ceil($request->num_personas / 3);
        $mesasOcupadas = MesaReserva::where('fecha', $request->fecha_reserva)
        ->where(function($query) use ($request, $horaFin) {
            $query->where('hora_inicio', '<', $horaFin)
                  ->where('hora_fin', '>', $request->hora_inicio);
        })
        ->pluck('mesa_id');

        $mesasDisponibles = Mesa::whereNotIn('id', $mesasOcupadas)
            ->take($mesasNecesarias)
            ->get();

        if ($mesasDisponibles->count() < $mesasNecesarias) {
            return back()->with('error', 'No hay mesas disponibles en ese horario.');
        }

        // Crear cliente
        $cliente = Cliente::create($request->only(['nombre', 'apellidos', 'email', 'telefono']));

        // Crear reserva
        $reserva = Reserva::create([
            'cliente_id' => $cliente->id,
            'fecha_reserva' => $request->fecha_reserva,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $horaFin,
            'num_personas' => $request->num_personas,
            'mesas_asignadas' => $mesasDisponibles->pluck('id')->toArray(),
        ]);

        // Registrar mesas ocupadas
        foreach ($mesasDisponibles as $mesa) {
            MesaReserva::create([
                'reserva_id' => $reserva->id,
                'mesa_id' => $mesa->id,
                'fecha' => $request->fecha_reserva,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $horaFin
            ]);
        }

        return redirect()->route('reservas.create')->with('success', 'Reserva creada correctamente.');
    }


    public function index()
    {
        // Obtener reservas ordenadas por fecha (las más recientes primero)
        $reservas = Reserva::with(['cliente', 'mesas'])
                ->orderBy('fecha_reserva', 'desc')
                ->orderBy('hora_inicio', 'desc')
                ->get();

        return view('reservas.index', compact('reservas'));
    }
}
