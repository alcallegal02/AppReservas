<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantClosure extends Model
{
    use HasFactory;

    protected $fillable = [
        'closure_date',
        'reason',
        'is_recurring'
    ];

    protected $casts = [
        'closure_date' => 'date',
        'is_recurring' => 'boolean'
    ];
}