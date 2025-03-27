@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Nueva Reserva</h1>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('reservas.store') }}" method="POST" class="border p-4 rounded">
        @csrf
        
        <h4 class="mb-3">Datos del Cliente</h4>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="col-md-6">
                <label for="apellidos" class="form-label">Apellidos</label>
                <input type="text" class="form-control" id="apellidos" name="apellidos" required>
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="col-md-6">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="tel" class="form-control" id="telefono" name="telefono" required>
            </div>
        </div>

        <h4 class="mb-3">Detalles de la Reserva</h4>
        <div class="row g-3">
            <div class="col-md-4">
                <label for="fecha_reserva" class="form-label">Fecha</label>
                <input type="date" class="form-control" id="fecha_reserva" name="fecha_reserva" required>
            </div>
            <div class="col-md-4">
                <label for="hora_inicio" class="form-label">Hora</label>
                <select class="form-select" id="hora_inicio" name="hora_inicio" required>
                    @for ($hora = 20; $hora <= 22; $hora++)
                        <option value="{{ sprintf('%02d:00', $hora) }}">{{ $hora }}:00</option>
                        <option value="{{ sprintf('%02d:30', $hora) }}">{{ $hora }}:30</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <label for="num_personas" class="form-label">Número de Personas</label>
                <input type="number" class="form-control" id="num_personas" name="num_personas" min="1" required>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Reservar</button>
            <a href="{{ route('reservas.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection