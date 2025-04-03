<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener IDs de relaciones
        $userId = DB::table('users')->where('email', 'admin@example.com')->value('id');
        $tableId = DB::table('tables')->where('name', 'Mesa 1')->value('id');
        
        // Función para calcular end_time (1.5 horas después del start_time)
        $calculateEndTime = function ($startTime) {
            return Carbon::parse($startTime)->addHours(1.5)->format('H:i:s');
        };

        // Reserva 1 (Noche)
        $nightSlot = DB::table('time_slots')->where('name', 'Noche')->first();
        $nightStart = Carbon::parse($nightSlot->start_time);
        DB::table('reservations')->insert([
            'user_id' => $userId,
            'table_id' => $tableId,
            'time_slot_id' => $nightSlot->id,
            'reservation_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => $nightSlot->start_time,
            'end_time' => $calculateEndTime($nightSlot->start_time),
            'guest_count' => 4,
            'status' => 'confirmed',
            'special_requests' => 'Mesa cerca de la ventana',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Reserva 2 (Mediodía)
        $noonSlot = DB::table('time_slots')->where('name', 'Mediodía')->first();
        DB::table('reservations')->insert([
            'user_id' => $userId,
            'table_id' => $tableId,
            'time_slot_id' => $noonSlot->id,
            'reservation_date' => Carbon::today()->addDays(2)->format('Y-m-d'),
            'start_time' => $noonSlot->start_time,
            'end_time' => $calculateEndTime($noonSlot->start_time),
            'guest_count' => 2,
            'status' => 'pending',
            'special_requests' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Reserva 3 (Ejemplo con duración personalizada dentro del límite)
        $customStart = '19:30:00';
        DB::table('reservations')->insert([
            'user_id' => $userId,
            'table_id' => $tableId,
            'time_slot_id' => $nightSlot->id,
            'reservation_date' => Carbon::tomorrow()->addDays(3)->format('Y-m-d'),
            'start_time' => $customStart,
            'end_time' => Carbon::parse($customStart)->addHours(2)->format('H:i:s'), // Máximo permitido (2h)
            'guest_count' => 3,
            'status' => 'confirmed',
            'special_requests' => 'Celebración de aniversario',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}