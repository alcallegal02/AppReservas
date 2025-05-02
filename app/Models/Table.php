<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Atributos asignables masivamente
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'capacity',
        'zone_id',
        'is_active',
    ];

    /**
     * Conversión de tipos de atributos
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
    ];

    /**
     * Obtener la zona a la que pertenece esta mesa
     */
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    /**
     * Obtener todas las reservas asociadas a esta mesa
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Scope para mesas activas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para mesas con capacidad mínima
     */
    public function scopeWithMinCapacity($query, $minCapacity)
    {
        return $query->where('capacity', '>=', $minCapacity);
    }
}