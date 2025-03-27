<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


// app/Models/Mesa.php
class Mesa extends Model
{
    protected $fillable = ['capacidad_maxima', 'estado'];

    public function reservas()
    {
        return $this->belongsToMany(Reserva::class, 'mesa_reserva')
            ->using(MesaReserva::class)
            ->withPivot(['fecha', 'hora_inicio', 'hora_fin']);
    }
}
