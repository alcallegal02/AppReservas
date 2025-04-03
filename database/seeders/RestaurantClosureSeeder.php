<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RestaurantClosureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('restaurant_closures')->insert([
            [
                'closure_date' => Carbon::parse('next monday')->format('Y-m-d'), // Cierra todos los lunes
                'reason' => 'Día de descanso semanal',
                'is_recurring' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'closure_date' => Carbon::parse('December 25')->format('Y-m-d'), // Navidad
                'reason' => 'Festivo Navidad',
                'is_recurring' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
