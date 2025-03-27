<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $fillable = [
        'cliente_id', 'fecha_reserva', 'hora_inicio', 'hora_fin', 
        'num_personas', 'mesas_asignadas', 'estado'
    ];

    protected $casts = [
        'mesas_asignadas' => 'array',
        'fecha_reserva' => 'date',  // Esto convierte el campo en un objeto Carbon
        'hora_inicio' => 'datetime', // Opcional: si también quieres manipular la hora como Carbon
        'hora_fin' => 'datetime',    // Opcional: si también quieres manipular la hora como Carbon
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function mesas()
    {
        return $this->belongsToMany(Mesa::class, 'mesa_reserva')
            ->using(MesaReserva::class)
            ->withPivot(['fecha', 'hora_inicio', 'hora_fin']);
    }
}