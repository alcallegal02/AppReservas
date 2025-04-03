<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tables')->insert([
            [
                'name' => 'Mesa 1', 
                'capacity' => 4, 
                'zone_id' => 1, 
                'is_active' => true
            ],
            [
                'name' => 'Mesa 2', 
                'capacity' => 6, 
                'zone_id' => 1, 
                'is_active' => true
            ],
            [
                'name' => 'Mesa VIP', 
                'capacity' => 8, 
                'zone_id' => 2, 
                'is_active' => true
            ],
        ]);
    }
}
