@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Reservas Actuales</h1>
        <a href="{{ route('reservas.create') }}" class="btn btn-primary">Nueva Reserva</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Personas</th>
                    <th>Mesas</th>
                    <th>Duración</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservas as $reserva)
                <tr>
                    <td>{{ $reserva->cliente->nombre }} {{ $reserva->cliente->apellidos }}</td>
                    <td>{{ $reserva->fecha_reserva->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i') }}</td>
                    <td>{{ $reserva->num_personas }}</td>
                    <td>
                        @foreach($reserva->mesas as $mesa)
                            <span class="badge bg-secondary">Mesa #{{ $mesa->id }}</span>
                        @endforeach
                    </td>
                    <td>1.5 horas</td>
                    <td>
                        <span class="badge bg-{{ $reserva->estado === 'confirmada' ? 'success' : 'warning' }}">
                            {{ ucfirst($reserva->estado) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4">No hay reservas registradas</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection