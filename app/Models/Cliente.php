<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/Cliente.php
class Cliente extends Model
{
    protected $fillable = ['nombre', 'apellidos', 'email', 'telefono'];

    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }
}
