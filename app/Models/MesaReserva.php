<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MesaReserva extends Model
{
    protected $table = 'mesa_reserva';
    
    protected $fillable = [
        'reserva_id', 
        'mesa_id',
        'fecha',
        'hora_inicio',
        'hora_fin'
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }

    public function mesa()
    {
        return $this->belongsTo(Mesa::class);
    }
}
