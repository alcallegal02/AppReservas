<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reservation_date',
        'start_time',
        'end_time',
        'guest_count',
        'status',
        'special_requests',
        'user_id',
        'table_id',
        'time_slot_id'
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    // Estados posibles
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_NO_SHOW = 'no_show';

    // Relaciones
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function table()
{
    return $this->belongsTo(Table::class)->with('zone');
}

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }

    // Scopes útiles
    public function scopeUpcoming($query)
    {
        return $query->where('reservation_date', '>=', now()->toDateString())
                    ->where('status', self::STATUS_CONFIRMED);
    }

    // En el modelo Reservation.php
public function zone()
{
    return $this->through('table')->has('zone');
}


}